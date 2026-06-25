<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\StockMovement;
use App\Services\ItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ItemApiController extends Controller
{
    public function __construct(
        private readonly ItemService $itemService,
    ) {}

    // -------------------------------------------------------------------------
    // DataTables — daftar barang aktif
    // -------------------------------------------------------------------------
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Item::class);

        $query = Item::with('category')
            ->withSum('stocks as total_stock', 'quantity')
            ->select('items.*');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('item_type')) {
            $query->where('item_type', $request->item_type);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereHas('stocks', fn($q) =>
                    $q->whereColumn('quantity', '<=', 'minimum_stock')->where('minimum_stock', '>', 0)
                );
            } elseif ($request->stock_status === 'empty') {
                $query->whereDoesntHave('stocks', fn($q) => $q->where('quantity', '>', 0));
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name_display', function (Item $item) {
                $badge = $item->item_type->value === 'consumable'
                    ? '<span class="badge bg-info-subtle text-info ms-1" style="font-size:10px">Habis Pakai</span>'
                    : '<span class="badge bg-primary-subtle text-primary ms-1" style="font-size:10px">Inventaris</span>';
                return '<div class="fw-semibold" style="color:var(--text-primary)">'
                    . e($item->name) . $badge
                    . '</div><small class="text-muted">' . e($item->code) . '</small>';
            })
            ->addColumn('category_display', function (Item $item) {
                return e($item->category->name ?? '—');
            })
            ->addColumn('stock_display', function (Item $item) {
                $total = (int) ($item->total_stock ?? 0);
                $isLow = $item->is_low_stock;

                $color  = $total === 0 ? 'danger' : ($isLow ? 'warning' : 'success');
                $icon   = $total === 0 ? 'bi-exclamation-circle' : ($isLow ? 'bi-exclamation-triangle' : 'bi-check-circle');
                $suffix = $isLow && $total > 0 ? ' <i class="bi bi-exclamation-triangle text-warning" title="Stok Rendah"></i>' : '';

                return '<span class="fw-bold text-' . $color . '">'
                    . $total . '</span> <small class="text-muted">' . e($item->unit_of_measure) . '</small>'
                    . $suffix;
            })
            ->addColumn('action', function (Item $item) {
                $user    = auth()->user();
                $buttons = '<a href="' . route('items.show', $item->id) . '"
                    class="btn btn-sm btn-outline-primary me-1" title="Detail"
                    style="border-radius:var(--radius-sm)"><i class="bi bi-eye"></i></a>';

                if ($user->isSarpras() || $user->isAdmin()) {
                    $buttons .= '<button class="btn btn-sm btn-outline-warning me-1 btn-edit"
                        data-id="' . $item->id . '" title="Edit"
                        style="border-radius:var(--radius-sm)"><i class="bi bi-pencil"></i></button>';
                }

                if ($user->isAdmin()) {
                    $buttons .= '<button class="btn btn-sm btn-danger btn-delete"
                        data-id="' . $item->id . '" data-name="' . e($item->name) . '" title="Hapus"
                        style="border-radius:var(--radius-sm)"><i class="bi bi-trash"></i></button>';
                }

                return '<div class="d-flex justify-content-end gap-1">' . $buttons . '</div>';
            })
            ->rawColumns(['name_display', 'stock_display', 'action'])
            ->make(true);
    }

    // -------------------------------------------------------------------------
    // Store
    // -------------------------------------------------------------------------
    public function store(StoreItemRequest $request): JsonResponse
    {
        $this->authorize('create', Item::class);

        $item = $this->itemService->create(
            $request->validated(),
            $request->file('image')
        );

        return response()->json([
            'success' => true,
            'message' => 'Barang ' . $item->name . ' berhasil ditambahkan.',
            'data'    => $item->load('category'),
        ]);
    }

    // -------------------------------------------------------------------------
    // Show — detail item untuk modal edit
    // -------------------------------------------------------------------------
    public function show(Item $item): JsonResponse
    {
        $this->authorize('view', $item);

        return response()->json([
            'success' => true,
            'data'    => $item->load('category'),
        ]);
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------
    public function update(UpdateItemRequest $request, Item $item): JsonResponse
    {
        $this->authorize('update', $item);

        $updated = $this->itemService->update(
            $item,
            $request->validated(),
            $request->file('image')
        );

        return response()->json([
            'success' => true,
            'message' => 'Barang ' . $updated->name . ' berhasil diperbarui.',
            'data'    => $updated,
        ]);
    }

    // -------------------------------------------------------------------------
    // Destroy — soft delete
    // -------------------------------------------------------------------------
    public function destroy(Item $item): JsonResponse
    {
        $this->authorize('delete', $item);

        try {
            $this->itemService->delete($item);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Barang ' . $item->name . ' berhasil dihapus.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Trash — DataTables onlyTrashed
    // -------------------------------------------------------------------------
    public function trash(): JsonResponse
    {
        $this->authorize('viewAny', Item::class);

        $query = Item::onlyTrashed()->with('category')->select('items.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name_display', function (Item $item) {
                return '<div class="fw-semibold text-muted">' . e($item->name) . '</div>'
                    . '<small class="text-muted">' . e($item->code) . '</small>';
            })
            ->addColumn('category_display', fn(Item $item) => e($item->category->name ?? '—'))
            ->addColumn('type_badge', function (Item $item) {
                return $item->item_type->value === 'consumable'
                    ? '<span class="badge bg-info-subtle text-info">Habis Pakai</span>'
                    : '<span class="badge bg-purple-subtle text-purple">Inventaris</span>';
            })
            ->addColumn('deleted_at_display', fn(Item $item) => $item->deleted_at->format('d M Y H:i'))
            ->addColumn('action', function (Item $item) {
                if (!auth()->user()->isAdmin()) return '';
                return '<div class="d-flex justify-content-end gap-1">
                    <button class="btn btn-sm btn-outline-success me-1 btn-restore"
                        data-id="' . $item->id . '" title="Pulihkan"
                        style="border-radius:var(--radius-sm)"><i class="bi bi-arrow-counterclockwise"></i></button>
                    <button class="btn btn-sm btn-danger btn-force-delete"
                        data-id="' . $item->id . '" data-name="' . e($item->name) . '" title="Hapus Permanen"
                        style="border-radius:var(--radius-sm)"><i class="bi bi-trash3"></i></button>
                </div>';
            })
            ->rawColumns(['name_display', 'type_badge', 'action'])
            ->make(true);
    }

    // -------------------------------------------------------------------------
    // Restore
    // -------------------------------------------------------------------------
    public function restore(int $id): JsonResponse
    {
        $this->authorize('create', Item::class);

        $item = Item::onlyTrashed()->findOrFail($id);
        $item->restore();

        return response()->json([
            'success' => true,
            'message' => 'Barang ' . $item->name . ' berhasil dipulihkan.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Force Delete
    // -------------------------------------------------------------------------
    public function forceDelete(int $id): JsonResponse
    {
        $this->authorize('delete', Item::class);

        $item = Item::onlyTrashed()->findOrFail($id);
        $name = $item->name;

        if ($item->stocks()->sum('quantity') > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak dapat dihapus permanen karena masih memiliki stok.',
            ], 422);
        }

        $item->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Barang ' . $name . ' dihapus permanen dari sistem.',
        ]);
    }

    // -------------------------------------------------------------------------
    // Stocks — daftar batch stok per item (untuk tab di show)
    // -------------------------------------------------------------------------
    public function stocks(Item $item): JsonResponse
    {
        $this->authorize('view', $item);

        $stocks = ItemStock::where('item_id', $item->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($s) => [
                'id'             => $s->id,
                'purchase_price' => $s->purchase_price,
                'selling_price'  => $s->selling_price,
                'quantity'       => $s->quantity,
                'minimum_stock'  => $s->minimum_stock,
                'batch_number'   => $s->batch_number ?? '—',
                'expired_date'   => $s->expired_date?->format('d M Y') ?? '—',
                'is_low_stock'   => $s->is_low_stock,
                'is_expired'     => $s->is_expired,
                'is_expiring_soon' => $s->is_expiring_soon,
                'created_at'     => $s->created_at->format('d M Y'),
            ]);

        return response()->json(['success' => true, 'data' => $stocks]);
    }

    // -------------------------------------------------------------------------
    // Movements — riwayat pergerakan stok per item (DataTables)
    // -------------------------------------------------------------------------
    public function movements(Request $request, Item $item): JsonResponse
    {
        $this->authorize('view', $item);

        $query = StockMovement::where('item_id', $item->id)
            ->with(['performer', 'itemStock'])
            ->select('stock_movements.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('type_badge', function (StockMovement $m) {
                $map = [
                    'in'         => ['success', 'bi-arrow-down-circle', 'Masuk'],
                    'out'        => ['danger',  'bi-arrow-up-circle',   'Keluar'],
                    'adjustment' => ['warning', 'bi-sliders',           'Penyesuaian'],
                    'return'     => ['info',    'bi-arrow-return-left', 'Kembali'],
                ];
                [$color, $icon, $label] = $map[$m->movement_type->value] ?? ['secondary', 'bi-dash', $m->movement_type->value];
                return '<span class="badge rounded-pill bg-' . $color . '-subtle text-' . $color . ' px-2 py-1">'
                    . '<i class="bi ' . $icon . ' me-1"></i>' . $label . '</span>';
            })
            ->addColumn('qty_display', function (StockMovement $m) {
                $sign  = $m->quantity > 0 ? '+' : '';
                $color = $m->quantity >= 0 ? 'success' : 'danger';
                return '<span class="fw-bold text-' . $color . '">' . $sign . $m->quantity . '</span>';
            })
            ->addColumn('stock_change', function (StockMovement $m) {
                return '<span class="text-muted">' . $m->stock_before . '</span>'
                    . ' <i class="bi bi-arrow-right text-muted" style="font-size:11px"></i> '
                    . '<span class="fw-semibold">' . $m->stock_after . '</span>';
            })
            ->addColumn('batch_display', function (StockMovement $m) {
                return $m->itemStock?->batch_number ?? '<em class="text-muted">—</em>';
            })
            ->addColumn('performer_display', fn(StockMovement $m) => e($m->performer->name ?? '—'))
            ->addColumn('date_display', fn(StockMovement $m) => $m->created_at->format('d M Y H:i'))
            ->rawColumns(['type_badge', 'qty_display', 'stock_change', 'batch_display'])
            ->make(true);
    }

    // -------------------------------------------------------------------------
    // Stats — counter widget di halaman index
    // -------------------------------------------------------------------------
    public function stats(): JsonResponse
    {
        $this->authorize('viewAny', Item::class);

        $lowStock = Item::whereHas('stocks', fn($q) =>
            $q->whereColumn('quantity', '<=', 'minimum_stock')->where('minimum_stock', '>', 0)
        )->count();

        $emptyStock = Item::whereDoesntHave('stocks', fn($q) =>
            $q->where('quantity', '>', 0)
        )->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'total'       => Item::count(),
                'consumable'  => Item::where('item_type', 'consumable')->count(),
                'inventory'   => Item::where('item_type', 'inventory')->count(),
                'low_stock'   => $lowStock,
                'empty_stock' => $emptyStock,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Categories — untuk dropdown filter & form
    // -------------------------------------------------------------------------
    public function categories(): JsonResponse
    {
        $categories = Category::active()->orderBy('name')
            ->get(['id', 'name', 'code', 'item_type']);

        return response()->json(['success' => true, 'data' => $categories]);
    }
}
