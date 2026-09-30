<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockOpname;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockOpnameApiController extends Controller
{
    // -------------------------------------------------------------------------
    // DataTables — daftar opname aktif (open / in_progress)
    // -------------------------------------------------------------------------
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', StockOpname::class);

        $query = StockOpname::with('creator')
            ->whereIn('status', ['open', 'in_progress'])
            ->select('stock_opname.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('opname_number_display', function (StockOpname $o) {
                return '<a href="' . route('stock-opname.show', $o->id) . '"
                    class="fw-bold text-decoration-none" style="color:var(--accent-primary)">'
                    . e($o->opname_number) . '</a>';
            })
            ->addColumn('status_badge', function (StockOpname $o) {
                $map = [
                    'open'        => ['warning', 'bi-circle',       'Terbuka'],
                    'in_progress' => ['info',    'bi-arrow-repeat', 'Sedang Berjalan'],
                ];
                [$color, $icon, $label] = $map[$o->status] ?? ['secondary', 'bi-dash', $o->status];
                return '<span class="badge rounded-pill bg-' . $color . '-subtle text-' . $color . ' px-3 py-1">'
                    . '<i class="bi ' . $icon . ' me-1"></i>' . $label . '</span>';
            })
            ->addColumn('progress_display', function (StockOpname $o) {
                $total   = $o->items()->count();
                $counted = $o->items()->whereNotNull('physical_quantity')->count();
                $pct     = $total > 0 ? round(($counted / $total) * 100) : 0;
                $color   = $pct === 100 ? 'success' : ($pct > 0 ? 'info' : 'secondary');
                return '<div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height:6px;border-radius:99px">
                        <div class="progress-bar bg-' . $color . '" style="width:' . $pct . '%"></div>
                    </div>
                    <span class="text-muted" style="font-size:12px;white-space:nowrap">' . $counted . '/' . $total . '</span>
                </div>';
            })
            ->addColumn('opname_date_display', fn(StockOpname $o) => $o->opname_date->format('d M Y'))
            ->addColumn('created_by_display',  fn(StockOpname $o) => e($o->creator->name ?? '—'))
            ->addColumn('action', function (StockOpname $o) {
                $buttons = '<a href="' . route('stock-opname.show', $o->id) . '"
                    class="btn btn-sm btn-outline-primary me-1" title="Detail & Input"
                    style="border-radius:var(--radius-sm)"><i class="bi bi-pencil-square"></i></a>';

                if (auth()->user()->isAdmin() && $o->status === 'open') {
                    $buttons .= '<button class="btn btn-sm btn-outline-danger btn-delete-opname"
                        data-id="' . $o->id . '" data-no="' . e($o->opname_number) . '" title="Hapus"
                        style="border-radius:var(--radius-sm)"><i class="bi bi-trash"></i></button>';
                }

                return '<div class="d-flex justify-content-end gap-1">' . $buttons . '</div>';
            })
            ->rawColumns(['opname_number_display', 'status_badge', 'progress_display', 'action'])
            ->make(true);
    }

    // -------------------------------------------------------------------------
    // DataTables — riwayat opname yang sudah ditutup
    // -------------------------------------------------------------------------
    public function history(): JsonResponse
    {
        $this->authorize('viewAny', StockOpname::class);

        $query = StockOpname::with(['creator', 'closer'])
            ->where('status', 'closed')
            ->select('stock_opname.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('opname_number_display', function (StockOpname $o) {
                return '<a href="' . route('stock-opname.show', $o->id) . '"
                    class="fw-bold text-decoration-none" style="color:var(--accent-primary)">'
                    . e($o->opname_number) . '</a>';
            })
            ->addColumn('opname_date_display',  fn(StockOpname $o) => $o->opname_date->format('d M Y'))
            ->addColumn('closed_date_display',  fn(StockOpname $o) => $o->closed_date?->format('d M Y') ?? '—')
            ->addColumn('created_by_display',   fn(StockOpname $o) => e($o->creator->name ?? '—'))
            ->addColumn('closed_by_display',    fn(StockOpname $o) => e($o->closer?->name ?? '—'))
            ->addColumn('diff_summary', function (StockOpname $o) {
                $withDiff = $o->items()->where('difference', '!=', 0)->whereNotNull('difference')->count();
                $total    = $o->items()->count();
                if ($withDiff === 0) {
                    return '<span class="text-success" style="font-size:12.5px"><i class="bi bi-check-circle me-1"></i>Tidak ada selisih</span>';
                }
                return '<span class="text-warning" style="font-size:12.5px"><i class="bi bi-exclamation-triangle me-1"></i>'
                    . $withDiff . ' dari ' . $total . ' item selisih</span>';
            })
            ->addColumn('action', function (StockOpname $o) {
                return '<a href="' . route('stock-opname.show', $o->id) . '"
                    class="btn btn-sm btn-outline-primary" title="Lihat Detail"
                    style="border-radius:var(--radius-sm)"><i class="bi bi-eye"></i></a>';
            })
            ->rawColumns(['opname_number_display', 'diff_summary', 'action'])
            ->make(true);
    }

    // -------------------------------------------------------------------------
    // Stats — counter widget
    // -------------------------------------------------------------------------
    public function stats(): JsonResponse
    {
        $this->authorize('viewAny', StockOpname::class);

        return response()->json([
            'success' => true,
            'data'    => [
                'open'        => StockOpname::where('status', 'open')->count(),
                'in_progress' => StockOpname::where('status', 'in_progress')->count(),
                'closed'      => StockOpname::where('status', 'closed')->count(),
                'total'       => StockOpname::count(),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // DataTables — item opname untuk tab di show (server-side)
    // -------------------------------------------------------------------------
    public function items(Request $request, StockOpname $stockOpname): JsonResponse
    {
        $this->authorize('view', $stockOpname);

        $query = $stockOpname->items()
            ->with(['item.category', 'counter'])
            ->select('stock_opname_items.*');

        if ($request->filled('filter_status')) {
            if ($request->filter_status === 'counted') {
                $query->whereNotNull('physical_quantity');
            } elseif ($request->filter_status === 'uncounted') {
                $query->whereNull('physical_quantity');
            } elseif ($request->filter_status === 'diff') {
                $query->where('difference', '!=', 0)->whereNotNull('difference');
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('opname_item_id',    fn($oi) => $oi->id)
            ->addColumn('system_quantity',   fn($oi) => $oi->system_quantity)
            ->addColumn('physical_quantity', fn($oi) => $oi->physical_quantity)
            ->addColumn('item_display', function ($oi) {
                return '<div class="fw-semibold" style="color:var(--text-primary)">' . e($oi->item->name) . '</div>'
                    . '<small class="text-muted">' . e($oi->item->code) . ' · ' . e($oi->item->category->name) . '</small>';
            })
            ->addColumn('uom_display', fn($oi) => e($oi->item->unit_of_measure))
            ->addColumn('counted_by_display', fn($oi) => e($oi->counter?->name ?? '—'))
            ->addColumn('system_qty_display', function ($oi) {
                return '<span class="fw-semibold">' . $oi->system_quantity . '</span>';
            })
            ->addColumn('physical_qty_display', function ($oi) {
                if ($oi->physical_quantity === null) {
                    return '<span class="badge bg-secondary-subtle text-secondary" style="font-size:11px">Belum dihitung</span>';
                }
                return '<span class="fw-semibold">' . $oi->physical_quantity . '</span>';
            })
            ->addColumn('difference_display', function ($oi) {
                if ($oi->difference === null) return '<span class="text-muted">—</span>';
                if ($oi->difference === 0)    return '<span class="text-success fw-semibold">0</span>';
                $sign  = $oi->difference > 0 ? '+' : '';
                $color = $oi->difference > 0 ? 'success' : 'danger';
                return '<span class="fw-bold text-' . $color . '">' . $sign . $oi->difference . '</span>';
            })
            ->rawColumns(['item_display', 'counted_by_display', 'system_qty_display', 'physical_qty_display', 'difference_display'])
            ->make(true);
    }

    // -------------------------------------------------------------------------
    // Delete opname yang masih open
    // -------------------------------------------------------------------------
    public function destroy(StockOpname $stockOpname): JsonResponse
    {
        $this->authorize('delete', $stockOpname);

        $stockOpname->items()->delete();
        $stockOpname->delete();

        return response()->json([
            'success' => true,
            'message' => 'Opname ' . $stockOpname->opname_number . ' berhasil dihapus.',
        ]);
    }
}
