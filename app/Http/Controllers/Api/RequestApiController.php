<?php

namespace App\Http\Controllers\Api;

use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Request\CompleteRequestRequest;
use App\Models\Item;
use App\Models\Request as RequestModel;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RequestApiController extends Controller
{
    // -------------------------------------------------------------------------
    // DataTables — daftar permintaan (scope by role)
    // -------------------------------------------------------------------------
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', RequestModel::class);

        $query = RequestModel::with(['requester', 'unit', 'reviewer'])
            ->select('requests.*');

        // Unit hanya lihat milik unitnya
        if ($request->user()->isUnit()) {
            $query->where('unit_id', $request->user()->unit_id);
        }

        // Filter dari luar DataTables
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('unit_id') && !$request->user()->isUnit()) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('request_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('request_date', '<=', $request->date_to);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('request_number_display', function (RequestModel $req) {
                return '<a href="' . route('requests.show', $req->id) . '"
                    class="fw-bold text-decoration-none" style="color:var(--accent-primary)">'
                    . e($req->request_number) . '</a>';
            })
            ->addColumn('unit_display', function (RequestModel $req) {
                return '<span class="fw-semibold">' . e($req->unit->name ?? '—') . '</span>';
            })
            ->addColumn('requester_display', function (RequestModel $req) {
                return e($req->requester->name ?? '—');
            })
            ->addColumn('priority_badge', function (RequestModel $req) {
                $map = [
                    'low'    => ['secondary', 'Rendah'],
                    'normal' => ['info',      'Normal'],
                    'high'   => ['warning',   'Tinggi'],
                    'urgent' => ['danger',    'Mendesak'],
                ];
                [$color, $label] = $map[$req->priority] ?? ['secondary', $req->priority];
                return '<span class="badge rounded-pill bg-' . $color . '-subtle text-' . $color . ' px-2 py-1" style="font-size:11px">'
                    . $label . '</span>';
            })
            ->addColumn('status_badge', function (RequestModel $req) {
                $map = [
                    'draft'     => ['secondary', 'Draft'],
                    'pending'   => ['warning',   'Menunggu'],
                    'approved'  => ['info',      'Disetujui'],
                    'partial'   => ['info',      'Sebagian'],
                    'rejected'  => ['danger',    'Ditolak'],
                    'ready'     => ['primary',   'Siap Ambil'],
                    'completed' => ['success',   'Selesai'],
                    'cancelled' => ['secondary', 'Dibatalkan'],
                ];
                [$color, $label] = $map[$req->status->value] ?? ['secondary', $req->status->value];
                return '<span class="badge rounded-pill bg-' . $color . '-subtle text-' . $color . ' px-3 py-1">'
                    . $label . '</span>';
            })
            ->addColumn('request_date_display', function (RequestModel $req) {
                return $req->request_date->format('d M Y');
            })
            ->addColumn('needed_date_display', function (RequestModel $req) {
                if (!$req->needed_date) return '<em class="text-muted">—</em>';
                $isLate = $req->needed_date->isPast() && !in_array($req->status->value, ['completed','cancelled']);
                return '<span class="' . ($isLate ? 'text-danger fw-semibold' : '') . '">'
                    . $req->needed_date->format('d M Y') . '</span>';
            })
            ->addColumn('action', function (RequestModel $req) {
                $user    = auth()->user();
                $buttons = '<a href="' . route('requests.show', $req->id) . '"
                    class="btn btn-sm btn-outline-primary me-1" title="Detail"
                    style="border-radius:var(--radius-sm)"><i class="bi bi-eye"></i></a>';

                // Unit: kirim draft
                if ($user->isUnit() && $req->status->value === 'draft' && $req->unit_id === $user->unit_id) {
                    $buttons .= '<button class="btn btn-sm btn-outline-success me-1 btn-submit"
                        data-id="' . $req->id . '" title="Kirim Permintaan"
                        style="border-radius:var(--radius-sm)"><i class="bi bi-send"></i></button>';
                }

                // Sarpras: tombol review jika pending
                if ($user->canApproveRequest() && $req->status->value === 'pending') {
                    $buttons .= '<a href="' . route('requests.review', $req->id) . '"
                        class="btn btn-sm btn-outline-warning me-1" title="Review"
                        style="border-radius:var(--radius-sm)"><i class="bi bi-pencil-square"></i></a>';
                }

                // Sarpras: siapkan barang jika approved/partial
                if ($user->canApproveRequest() && in_array($req->status->value, ['approved','partial'])) {
                    $buttons .= '<button class="btn btn-sm btn-outline-info me-1 btn-fulfill"
                        data-id="' . $req->id . '" title="Siapkan Barang"
                        style="border-radius:var(--radius-sm)"><i class="bi bi-box-arrow-in-down"></i></button>';
                }

                // Unit: batalkan jika masih draft atau pending
                if ($user->isUnit() && in_array($req->status->value, ['draft', 'pending']) && $req->unit_id === $user->unit_id) {
                    $buttons .= '<button class="btn btn-sm btn-outline-danger btn-cancel"
                        data-id="' . $req->id . '" data-no="' . e($req->request_number) . '" title="Batalkan"
                        style="border-radius:var(--radius-sm)"><i class="bi bi-x-lg"></i></button>';
                }

                // Sarpras: konfirmasi ambil jika ready
                if ($user->canApproveRequest() && $req->status->value === 'ready') {
                    $buttons .= '<button class="btn btn-sm btn-success btn-complete"
                        data-id="' . $req->id . '" title="Konfirmasi Pengambilan"
                        style="border-radius:var(--radius-sm)"><i class="bi bi-check-lg"></i></button>';
                }

                return '<div class="d-flex justify-content-end gap-1">' . $buttons . '</div>';
            })
            ->rawColumns(['request_number_display', 'unit_display', 'priority_badge', 'status_badge', 'needed_date_display', 'action'])
            ->make(true);
    }

    // -------------------------------------------------------------------------
    // Stats — counter widget
    // -------------------------------------------------------------------------
    public function stats(Request $request): JsonResponse
    {
        $this->authorize('viewAny', RequestModel::class);

        $base = RequestModel::query();

        if ($request->user()->isUnit()) {
            $base->where('unit_id', $request->user()->unit_id);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'total'     => (clone $base)->count(),
                'draft'     => (clone $base)->where('status', 'draft')->count(),
                'pending'   => (clone $base)->where('status', 'pending')->count(),
                'approved'  => (clone $base)->whereIn('status', ['approved', 'partial'])->count(),
                'ready'     => (clone $base)->where('status', 'ready')->count(),
                'completed' => (clone $base)->where('status', 'completed')
                    ->whereMonth('created_at', now()->month)->count(),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Fulfill — sarpras siapkan barang → status Ready
    // -------------------------------------------------------------------------
    public function fulfill(Request $httpRequest, RequestModel $request): JsonResponse
    {
        $this->authorize('fulfill', $request);

        app(\App\Services\RequestService::class)->fulfill($request, $httpRequest->user());

        return response()->json([
            'success' => true,
            'message' => 'Barang untuk ' . $request->request_number . ' sudah disiapkan. Menunggu pengambilan unit.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Complete — unit konfirmasi pengambilan
    // -------------------------------------------------------------------------
    public function complete(CompleteRequestRequest $httpRequest, RequestModel $request): JsonResponse
    {
        $this->authorize('complete', $request);

        $httpRequest->validate([
            'received_by_name' => ['required', 'string', 'max:100'],
        ]);

        app(\App\Services\RequestService::class)->complete($request, $httpRequest->validated());

        return response()->json([
            'success' => true,
            'message' => $request->request_number . ' selesai. Barang telah diterima.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Submit — unit kirim draft ke sarpras
    // -------------------------------------------------------------------------
    public function submit(RequestModel $request): JsonResponse
    {
        $this->authorize('submit', $request);

        app(\App\Services\RequestService::class)->submit($request);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan ' . $request->request_number . ' berhasil dikirim. Menunggu review Sarpras.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Cancel — unit batalkan permintaan
    // -------------------------------------------------------------------------
    public function cancel(RequestModel $request): JsonResponse
    {
        $this->authorize('cancel', $request);

        app(\App\Services\RequestService::class)->cancel($request);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan ' . $request->request_number . ' berhasil dibatalkan.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Items — list barang aktif untuk form pengajuan
    // -------------------------------------------------------------------------
    public function items(): JsonResponse
    {
        $items = Item::with('category')
            ->active()
            ->withSum('stocks as total_stock', 'quantity')
            ->orderBy('name')
            ->get()
            ->map(fn($item) => [
                'id'              => $item->id,
                'name'            => $item->name,
                'code'            => $item->code,
                'unit_of_measure' => $item->unit_of_measure,
                'category'        => $item->category->name,
                'item_type'       => $item->item_type->label(),
                'total_stock'     => (int) ($item->total_stock ?? 0),
            ]);

        return response()->json(['success' => true, 'data' => $items]);
    }

    // -------------------------------------------------------------------------
    // Units — list unit untuk filter (sarpras/admin)
    // -------------------------------------------------------------------------
    public function units(): JsonResponse
    {
        $units = Unit::active()->orderBy('name')->get(['id', 'name', 'code']);

        return response()->json(['success' => true, 'data' => $units]);
    }
}
