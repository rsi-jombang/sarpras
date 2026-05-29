<?php

namespace App\Http\Controllers\Api;

use App\Enums\PurchaseOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrder\ReceivePurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\StorePurchaseOrderRequest;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PurchaseOrderApiController extends Controller
{
    public function __construct(
        private readonly PurchaseOrderService $purchaseOrderService,
    ) {}

    // -------------------------------------------------------------------------
    // DataTables — list semua PO
    // -------------------------------------------------------------------------
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', PurchaseOrder::class);

        $query = PurchaseOrder::with(['creator', 'approver'])
            ->select('purchase_orders.*');

        // Filter status dari luar DataTables (dropdown filter)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('po_number_display', function (PurchaseOrder $po) {
                return '<span class="fw-bold" style="color: var(--accent-primary)">'
                    . e($po->po_number)
                    . '</span>';
            })
            ->addColumn('supplier_display', function (PurchaseOrder $po) {
                if (!$po->supplier_name) {
                    return '<em class="text-muted" style="font-size:12.5px">Tidak ada supplier</em>';
                }
                $contact = $po->supplier_contact
                    ? '<br><small class="text-muted">' . e($po->supplier_contact) . '</small>'
                    : '';
                return '<span class="fw-semibold">' . e($po->supplier_name) . '</span>' . $contact;
            })
            ->addColumn('status_badge', function (PurchaseOrder $po) {
                $map = [
                    'draft'     => ['secondary', 'Draft'],
                    'approved'  => ['info',      'Disetujui'],
                    'ordered'   => ['warning',   'Dipesan'],
                    'received'  => ['success',   'Diterima'],
                    'cancelled' => ['danger',    'Dibatalkan'],
                ];
                [$color, $label] = $map[$po->status->value] ?? ['secondary', $po->status->value];
                return '<span class="badge rounded-pill bg-' . $color . '-subtle text-' . $color . ' px-3 py-1">'
                    . $label . '</span>';
            })
            ->addColumn('total_display', function (PurchaseOrder $po) {
                return '<span class="fw-semibold">Rp ' . number_format($po->total_amount, 0, ',', '.') . '</span>';
            })
            ->addColumn('order_date_display', function (PurchaseOrder $po) {
                return $po->order_date
                    ? $po->order_date->format('d M Y')
                    : '<em class="text-muted" style="font-size:12.5px">—</em>';
            })
            ->addColumn('created_by_display', function (PurchaseOrder $po) {
                return e($po->creator->name ?? '—');
            })
            ->addColumn('action', function (PurchaseOrder $po) {
                $user    = auth()->user();
                $buttons = '';

                // Tombol detail — selalu tampil
                $buttons .= '<a href="' . route('purchase-orders.show', $po->id) . '"
                    class="btn btn-sm btn-outline-primary me-1" title="Detail"
                    style="border-radius: var(--radius-sm)">
                    <i class="bi bi-eye"></i>
                </a>';

                // Edit — hanya jika masih draft
                if ($po->isEditable() && $user->canCreatePO()) {
                    $buttons .= '<button class="btn btn-sm btn-outline-warning me-1 btn-edit"
                        data-id="' . $po->id . '" title="Edit"
                        style="border-radius: var(--radius-sm)">
                        <i class="bi bi-pencil"></i>
                    </button>';
                }

                // Approve — hanya admin & draft
                if ($po->status === PurchaseOrderStatus::Draft && $user->isAdmin()) {
                    $buttons .= '<button class="btn btn-sm btn-outline-success me-1 btn-approve"
                        data-id="' . $po->id . '" title="Setujui"
                        style="border-radius: var(--radius-sm)">
                        <i class="bi bi-check-lg"></i>
                    </button>';
                }

                // Receive — approved/ordered & sarpras
                if ($po->canBeReceived() && $user->canCreatePO()) {
                    $buttons .= '<button class="btn btn-sm btn-outline-info me-1 btn-receive"
                        data-id="' . $po->id . '" title="Terima Barang"
                        style="border-radius: var(--radius-sm)">
                        <i class="bi bi-box-arrow-in-down"></i>
                    </button>';
                }

                // Cancel — admin, belum received/cancelled
                if (!in_array($po->status, [PurchaseOrderStatus::Received, PurchaseOrderStatus::Cancelled])
                    && $user->isAdmin()) {
                    $buttons .= '<button class="btn btn-sm btn-outline-danger me-1 btn-cancel"
                        data-id="' . $po->id . '" title="Batalkan"
                        style="border-radius: var(--radius-sm)">
                        <i class="bi bi-x-lg"></i>
                    </button>';
                }

                // Delete (soft) — hanya draft & admin
                if ($po->status === PurchaseOrderStatus::Draft && $user->isAdmin()) {
                    $buttons .= '<button class="btn btn-sm btn-danger btn-delete"
                        data-id="' . $po->id . '" data-po="' . e($po->po_number) . '" title="Hapus"
                        style="border-radius: var(--radius-sm)">
                        <i class="bi bi-trash"></i>
                    </button>';
                }

                return '<div class="d-flex justify-content-end gap-1">' . $buttons . '</div>';
            })
            ->rawColumns(['po_number_display', 'supplier_display', 'status_badge', 'total_display', 'order_date_display', 'action'])
            ->make(true);
    }

    // -------------------------------------------------------------------------
    // Store — buat PO baru
    // -------------------------------------------------------------------------
    public function store(StorePurchaseOrderRequest $request): JsonResponse
    {
        $this->authorize('create', PurchaseOrder::class);

        $po = $this->purchaseOrderService->create($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order ' . $po->po_number . ' berhasil dibuat.',
            'data'    => $po,
        ]);
    }

    // -------------------------------------------------------------------------
    // Show — detail PO untuk modal edit / receive
    // -------------------------------------------------------------------------
    public function show(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $this->authorize('view', $purchaseOrder);

        $purchaseOrder->load(['creator', 'approver', 'items.item.category']);

        return response()->json([
            'success' => true,
            'data'    => $purchaseOrder,
        ]);
    }

    // -------------------------------------------------------------------------
    // Update — edit PO yang masih draft
    // -------------------------------------------------------------------------
    public function update(StorePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $this->authorize('update', $purchaseOrder);

        $po = $this->purchaseOrderService->update($purchaseOrder, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order ' . $po->po_number . ' berhasil diperbarui.',
            'data'    => $po,
        ]);
    }

    // -------------------------------------------------------------------------
    // Approve — draft → approved
    // -------------------------------------------------------------------------
    public function approve(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $this->authorize('approve', $purchaseOrder);

        $po = $this->purchaseOrderService->approve($purchaseOrder, auth()->user());

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order ' . $po->po_number . ' berhasil disetujui.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Receive — terima barang, stok otomatis masuk
    // -------------------------------------------------------------------------
    public function receive(ReceivePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $this->authorize('receive', $purchaseOrder);

        $po = $this->purchaseOrderService->receive($purchaseOrder, $request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Barang dari PO ' . $po->po_number . ' berhasil diterima. Stok telah diperbarui.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Destroy — soft delete PO (hanya draft)
    // -------------------------------------------------------------------------
    public function destroy(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $this->authorize('delete', $purchaseOrder);

        $this->purchaseOrderService->delete($purchaseOrder);

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order ' . $purchaseOrder->po_number . ' berhasil dihapus.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Trash — DataTables list PO yang sudah dihapus (onlyTrashed)
    // -------------------------------------------------------------------------
    public function trash(Request $request): JsonResponse
    {
        $this->authorize('viewAny', PurchaseOrder::class);

        $query = PurchaseOrder::onlyTrashed()
            ->with(['creator', 'approver'])
            ->select('purchase_orders.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('po_number_display', function (PurchaseOrder $po) {
                return '<span class="fw-bold text-muted">' . e($po->po_number) . '</span>';
            })
            ->addColumn('supplier_display', function (PurchaseOrder $po) {
                return $po->supplier_name
                    ? e($po->supplier_name)
                    : '<em class="text-muted" style="font-size:12.5px">Tidak ada supplier</em>';
            })
            ->addColumn('status_badge', function (PurchaseOrder $po) {
                $map = [
                    'draft'     => ['secondary', 'Draft'],
                    'approved'  => ['info',      'Disetujui'],
                    'ordered'   => ['warning',   'Dipesan'],
                    'received'  => ['success',   'Diterima'],
                    'cancelled' => ['danger',    'Dibatalkan'],
                ];
                [$color, $label] = $map[$po->status->value] ?? ['secondary', $po->status->value];
                return '<span class="badge rounded-pill bg-' . $color . '-subtle text-' . $color . ' px-3 py-1">'
                    . $label . '</span>';
            })
            ->addColumn('total_display', function (PurchaseOrder $po) {
                return 'Rp ' . number_format($po->total_amount, 0, ',', '.');
            })
            ->addColumn('deleted_at_display', function (PurchaseOrder $po) {
                return $po->deleted_at->format('d M Y H:i');
            })
            ->addColumn('created_by_display', function (PurchaseOrder $po) {
                return e($po->creator->name ?? '—');
            })
            ->addColumn('action', function (PurchaseOrder $po) {
                $buttons = '';

                if (auth()->user()->isAdmin()) {
                    $buttons .= '<button class="btn btn-sm btn-outline-success me-1 btn-restore"
                        data-id="' . $po->id . '" title="Pulihkan"
                        style="border-radius: var(--radius-sm)">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>';

                    $buttons .= '<button class="btn btn-sm btn-danger btn-force-delete"
                        data-id="' . $po->id . '" data-po="' . e($po->po_number) . '" title="Hapus Permanen"
                        style="border-radius: var(--radius-sm)">
                        <i class="bi bi-trash3"></i>
                    </button>';
                }

                return '<div class="d-flex justify-content-end gap-1">' . $buttons . '</div>';
            })
            ->rawColumns(['po_number_display', 'supplier_display', 'status_badge', 'action'])
            ->make(true);
    }

    // -------------------------------------------------------------------------
    // Restore — kembalikan PO dari trash
    // -------------------------------------------------------------------------
    public function restore(int $id): JsonResponse
    {
        $this->authorize('viewAny', PurchaseOrder::class);

        $po = PurchaseOrder::onlyTrashed()->findOrFail($id);
        $this->purchaseOrderService->restore($po);

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order ' . $po->po_number . ' berhasil dipulihkan.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Force Delete — hapus permanen dari trash
    // -------------------------------------------------------------------------
    public function forceDelete(int $id): JsonResponse
    {
        $this->authorize('delete', PurchaseOrder::class);

        $po = PurchaseOrder::onlyTrashed()->findOrFail($id);
        $poNumber = $po->po_number;
        $this->purchaseOrderService->forceDelete($po);

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order ' . $poNumber . ' dihapus permanen dari sistem.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Trash Stats — counter untuk halaman trash
    // -------------------------------------------------------------------------
    public function trashStats(): JsonResponse
    {
        $this->authorize('viewAny', PurchaseOrder::class);

        return response()->json([
            'success' => true,
            'data'    => [
                'total'     => PurchaseOrder::onlyTrashed()->count(),
                'draft'     => PurchaseOrder::onlyTrashed()->where('status', 'draft')->count(),
                'received'  => PurchaseOrder::onlyTrashed()->where('status', 'received')->count(),
                'cancelled' => PurchaseOrder::onlyTrashed()->where('status', 'cancelled')->count(),
            ],
        ]);
    }

    public function cancel(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $this->authorize('cancel', $purchaseOrder);

        $po = $this->purchaseOrderService->cancel($purchaseOrder);

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order ' . $po->po_number . ' berhasil dibatalkan.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Items — ambil list item untuk dropdown di form
    // -------------------------------------------------------------------------
    public function items(): JsonResponse
    {
        $items = Item::with('category')
            ->active()
            ->orderBy('name')
            ->get()
            ->map(fn($item) => [
                'id'              => $item->id,
                'name'            => $item->name,
                'code'            => $item->code,
                'unit_of_measure' => $item->unit_of_measure,
                'category'        => $item->category->name,
                'item_type'       => $item->item_type->label(),
            ]);

        return response()->json(['success' => true, 'data' => $items]);
    }

    // -------------------------------------------------------------------------
    // Stats — counter widget di halaman index
    // -------------------------------------------------------------------------
    public function stats(): JsonResponse
    {
        $this->authorize('viewAny', PurchaseOrder::class);

        return response()->json([
            'success' => true,
            'data'    => [
                'total'     => PurchaseOrder::count(),
                'draft'     => PurchaseOrder::where('status', 'draft')->count(),
                'ordered'   => PurchaseOrder::whereIn('status', ['approved', 'ordered'])->count(),
                'received'  => PurchaseOrder::where('status', 'received')->count(),
                'cancelled' => PurchaseOrder::where('status', 'cancelled')->count(),
            ],
        ]);
    }
}
