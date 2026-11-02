<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Return\StoreItemReturnRequest;
use App\Models\ItemReturn;
use App\Services\ItemReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ItemReturnApiController extends Controller
{
    public function __construct(private readonly ItemReturnService $itemReturnService) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ItemReturn::class);

        $query = ItemReturn::with(['creator', 'unit', 'receiver'])
            ->select('item_returns.*');

        if ($request->user()->isUnit()) {
            $query->where('created_by', $request->user()->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('unit_id') && !$request->user()->isUnit()) {
            $query->where('unit_id', $request->unit_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('return_number_display', function (ItemReturn $r) {
                return '<a href="' . route('item-returns.show', $r->id) . '" class="fw-bold text-decoration-none" style="color:var(--accent-primary)">' . e($r->return_number) . '</a>';
            })
            ->addColumn('unit_display', fn(ItemReturn $r) => e($r->unit->name ?? '—'))
            ->addColumn('creator_display', fn(ItemReturn $r) => e($r->creator->name ?? '—'))
            ->addColumn('status_badge', function (ItemReturn $r) {
                $map = [
                    'draft'     => ['secondary', 'Draft'],
                    'submitted' => ['warning', 'Menunggu Terima'],
                    'received'  => ['success', 'Diterima'],
                    'cancelled' => ['danger', 'Dibatalkan'],
                ];
                [$color, $label] = $map[$r->status] ?? ['secondary', $r->status];
                return '<span class="badge rounded-pill bg-' . $color . '-subtle text-' . $color . ' px-3 py-1">' . $label . '</span>';
            })
            ->addColumn('total_qty', function (ItemReturn $r) {
                return (int) $r->items->sum('quantity_returned');
            })
            ->addColumn('action', function (ItemReturn $r) {
                $user = auth()->user();
                $btn = '<a href="' . route('item-returns.show', $r->id) . '" class="btn btn-sm btn-outline-primary me-1" title="Detail" style="border-radius:var(--radius-sm)"><i class="bi bi-eye"></i></a>';
                if ($user->isUnit() && $r->status === 'draft' && $r->created_by === $user->id) {
                    $btn .= '<a href="' . route('item-returns.edit', $r->id) . '" class="btn btn-sm btn-outline-warning me-1" title="Edit" style="border-radius:var(--radius-sm)"><i class="bi bi-pencil"></i></a>';
                }
                if (($user->isSarpras() || $user->isAdmin()) && $r->status === 'submitted') {
                    $btn .= '<button class="btn btn-sm btn-success btn-receive" data-id="' . $r->id . '" title="Terima" style="border-radius:var(--radius-sm)"><i class="bi bi-check-lg"></i></button>';
                }
                if ($r->status !== 'received' && ($user->isAdmin() || ($user->isUnit() && $r->created_by === $user->id))) {
                    $btn .= '<button class="btn btn-sm btn-outline-danger btn-cancel" data-id="' . $r->id . '" title="Batalkan" style="border-radius:var(--radius-sm)"><i class="bi bi-x-lg"></i></button>';
                }
                return '<div class="d-flex justify-content-end gap-1">' . $btn . '</div>';
            })
            ->rawColumns(['return_number_display', 'status_badge', 'action'])
            ->make(true);
    }

    public function stats(Request $request): JsonResponse
    {
        $base = ItemReturn::query();
        if ($request->user()->isUnit()) {
            $base->where('created_by', $request->user()->id);
        }
        return response()->json([
            'success' => true,
            'data'    => [
                'total'      => (clone $base)->count(),
                'draft'      => (clone $base)->where('status', 'draft')->count(),
                'submitted'  => (clone $base)->where('status', 'submitted')->count(),
                'received'   => (clone $base)->where('status', 'received')->count(),
            ],
        ]);
    }

    public function getReturnableItems(Request $request, int $requestId): JsonResponse
    {
        $req = \App\Models\Request::with('items.item')->findOrFail($requestId);
        if ($request->user()->isUnit() && $req->requested_by !== $request->user()->id) {
            abort(403);
        }
        // Only completed requests can be returned from
        if ($req->status !== \App\Enums\RequestStatus::Completed) {
            return response()->json(['success' => false, 'message' => 'Hanya permintaan yang selesai dapat dikembalikan'], 400);
        }

        $returnedByRI = \App\Models\ItemReturnItem::whereHas(
            'itemReturn',
            fn($q) => $q->whereNotIn('status', ['cancelled'])
        )->whereIn('request_item_id', $req->items->pluck('id'))
            ->select('request_item_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity_returned) as total'))
            ->groupBy('request_item_id')
            ->pluck('total', 'request_item_id');

        $items = $req->items->map(fn($it) => [
            'request_item_id' => $it->id,
            'item_id'         => $it->item_id,
            'item_name'       => $it->item->name,
            'item_code'       => $it->item->code,
            'uom'             => $it->item->unit_of_measure,
            'given'           => (int) $it->quantity_given,
            'returned'        => (int) ($returnedByRI[$it->id] ?? 0),
            'remaining'       => max(0, (int) $it->quantity_given - (int) ($returnedByRI[$it->id] ?? 0)),
        ])->filter(fn($it) => $it['remaining'] > 0);

        return response()->json(['success' => true, 'data' => $items->values()]);
    }

    public function receive(Request $httpRequest, ItemReturn $itemReturn): JsonResponse
    {
        $this->authorize('receive', $itemReturn);
        $this->itemReturnService->receive($itemReturn, $httpRequest->user());
        return response()->json([
            'success' => true,
            'message' => 'Pengembalian ' . $itemReturn->return_number . ' diterima. Stok diperbarui.',
        ]);
    }

    public function cancel(Request $httpRequest, ItemReturn $itemReturn): JsonResponse
    {
        $this->authorize('cancel', $itemReturn);
        $this->itemReturnService->cancel($itemReturn, $httpRequest->user());
        return response()->json([
            'success' => true,
            'message' => 'Pengembalian dibatalkan.',
        ]);
    }

    public function submit(Request $httpRequest, ItemReturn $itemReturn): JsonResponse
    {
        $this->authorize('submit', $itemReturn);
        $this->itemReturnService->submit($itemReturn);
        return response()->json([
            'success' => true,
            'message' => 'Pengembalian ' . $itemReturn->return_number . ' dikirim ke Sarpras.',
        ]);
    }
}