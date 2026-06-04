<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\Request as RequestModel;
use App\Models\StockMovement;
use App\Models\StockOpname;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    // -------------------------------------------------------------------------
    // Stats Unit — kartu statistik untuk role unit
    // -------------------------------------------------------------------------
    public function unitStats(Request $request): JsonResponse
    {
        $unitId = $request->user()->unit_id;

        $base = RequestModel::where('unit_id', $unitId);

        return response()->json([
            'success' => true,
            'data'    => [
                'pending'   => (clone $base)->where('status', 'pending')->count(),
                'approved'  => (clone $base)->whereIn('status', ['approved', 'partial'])->count(),
                'ready'     => (clone $base)->where('status', 'ready')->count(),
                'completed' => (clone $base)->where('status', 'completed')
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count(),
                'total_this_month' => (clone $base)
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count(),
                'rejected_this_month' => (clone $base)
                                ->where('status', 'rejected')
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count(),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Chart Unit — permintaan per bulan (6 bulan terakhir)
    // -------------------------------------------------------------------------
    public function unitCharts(Request $request): JsonResponse
    {
        $unitId = $request->user()->unit_id;

        // Permintaan per bulan 6 bulan terakhir
        $monthly = collect(range(5, 0))->map(function ($i) use ($unitId) {
            $date  = now()->subMonths($i);
            $count = RequestModel::where('unit_id', $unitId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            return [
                'label' => $date->translatedFormat('M Y'),
                'value' => $count,
            ];
        });

        // Distribusi status bulan ini
        $statusDist = RequestModel::where('unit_id', $unitId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabels = [
            'pending'   => 'Menunggu',
            'approved'  => 'Disetujui',
            'partial'   => 'Sebagian',
            'rejected'  => 'Ditolak',
            'ready'     => 'Siap Ambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return response()->json([
            'success' => true,
            'data'    => [
                'monthly_requests' => $monthly->values(),
                'status_dist'      => $statusDist->mapWithKeys(fn($v, $k) => [
                    ($statusLabels[$k] ?? $k) => $v,
                ]),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Recent Requests Unit — 5 permintaan terbaru
    // -------------------------------------------------------------------------
    public function unitRecentRequests(Request $request): JsonResponse
    {
        $requests = RequestModel::where('unit_id', $request->user()->unit_id)
            ->with('items.item')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn($r) => [
                'id'             => $r->id,
                'request_number' => $r->request_number,
                'status'         => $r->status->value,
                'priority'       => $r->priority,
                'request_date'   => $r->request_date->format('d M Y'),
                'needed_date'    => $r->needed_date?->format('d M Y'),
                'items_count'    => $r->items->count(),
                'url'            => route('requests.show', $r->id),
            ]);

        return response()->json(['success' => true, 'data' => $requests]);
    }

    // -------------------------------------------------------------------------
    // Stats Sarpras
    // -------------------------------------------------------------------------
    public function sarprasStats(): JsonResponse
    {
        $lowStockItems = Item::whereHas('stocks', fn($q) =>
            $q->whereColumn('quantity', '<=', 'minimum_stock')->where('minimum_stock', '>', 0)
        )->count();

        $emptyStockItems = Item::whereDoesntHave('stocks', fn($q) =>
            $q->where('quantity', '>', 0)
        )->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'pending_requests'  => RequestModel::where('status', 'pending')->count(),
                'ready_requests'    => RequestModel::where('status', 'ready')->count(),
                'low_stock_items'   => $lowStockItems,
                'empty_stock_items' => $emptyStockItems,
                'draft_pos'         => PurchaseOrder::where('status', 'draft')->count(),
                'active_pos'        => PurchaseOrder::whereIn('status', ['approved', 'ordered'])->count(),
                'open_opname'       => StockOpname::whereIn('status', ['open', 'in_progress'])->count(),
                'total_items'       => Item::count(),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Charts Sarpras
    // -------------------------------------------------------------------------
    public function sarprasCharts(): JsonResponse
    {
        // Permintaan per bulan 6 bulan terakhir (semua unit)
        $monthlyRequests = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'label'     => $date->translatedFormat('M Y'),
                'total'     => RequestModel::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
                'completed' => RequestModel::where('status', 'completed')->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
                'rejected'  => RequestModel::where('status', 'rejected')->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
            ];
        });

        // Top 5 barang paling sering diminta bulan ini
        $topItems = DB::table('request_items')
            ->join('requests', 'requests.id', '=', 'request_items.request_id')
            ->join('items', 'items.id', '=', 'request_items.item_id')
            ->whereMonth('requests.created_at', now()->month)
            ->whereYear('requests.created_at', now()->year)
            ->select('items.name', DB::raw('SUM(request_items.quantity_requested) as total_qty'))
            ->groupBy('items.id', 'items.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Distribusi stok: normal, rendah, habis
        $totalItems  = Item::count();
        $lowStock    = Item::whereHas('stocks', fn($q) => $q->whereColumn('quantity', '<=', 'minimum_stock')->where('minimum_stock', '>', 0))->count();
        $emptyStock  = Item::whereDoesntHave('stocks', fn($q) => $q->where('quantity', '>', 0))->count();
        $normalStock = max(0, $totalItems - $lowStock - $emptyStock);

        // Pergerakan stok 7 hari terakhir
        $stockMovements = collect(range(6, 0))->map(function ($i) {
            $date = now()->subDays($i);
            return [
                'label' => $date->format('d M'),
                'in'    => StockMovement::where('movement_type', 'in')->whereDate('created_at', $date)->sum(DB::raw('ABS(quantity)')),
                'out'   => StockMovement::where('movement_type', 'out')->whereDate('created_at', $date)->sum(DB::raw('ABS(quantity)')),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'monthly_requests' => $monthlyRequests->values(),
                'top_items'        => $topItems,
                'stock_status'     => [
                    'Normal'  => $normalStock,
                    'Rendah'  => $lowStock,
                    'Habis'   => $emptyStock,
                ],
                'stock_movements'  => $stockMovements->values(),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Recent Activity Sarpras — permintaan pending & pergerakan stok terbaru
    // -------------------------------------------------------------------------
    public function sarprasActivity(): JsonResponse
    {
        $pendingRequests = RequestModel::with(['unit', 'requester'])
            ->where('status', 'pending')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn($r) => [
                'id'             => $r->id,
                'request_number' => $r->request_number,
                'unit'           => $r->unit->name ?? '—',
                'requester'      => $r->requester->name ?? '—',
                'priority'       => $r->priority,
                'request_date'   => $r->request_date->format('d M Y'),
                'items_count'    => $r->items()->count(),
                'url'            => route('requests.show', $r->id),
            ]);

        $lowStockItems = Item::with('category')
            ->whereHas('stocks', fn($q) =>
                $q->whereColumn('quantity', '<=', 'minimum_stock')->where('minimum_stock', '>', 0)
            )
            ->withSum('stocks as total_stock', 'quantity')
            ->limit(6)
            ->get()
            ->map(fn($i) => [
                'id'          => $i->id,
                'name'        => $i->name,
                'code'        => $i->code,
                'category'    => $i->category->name ?? '—',
                'total_stock' => (int) ($i->total_stock ?? 0),
                'uom'         => $i->unit_of_measure,
                'url'         => route('items.show', $i->id),
            ]);

        $recentMovements = StockMovement::with(['item', 'performer'])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($m) => [
                'item'          => $m->item->name ?? '—',
                'type'          => $m->movement_type->value,
                'quantity'      => $m->quantity,
                'stock_after'   => $m->stock_after,
                'performer'     => $m->performer->name ?? '—',
                'created_at'    => $m->created_at->diffForHumans(),
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'pending_requests' => $pendingRequests,
                'low_stock_items'  => $lowStockItems,
                'recent_movements' => $recentMovements,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Stats Admin — gabungan semua
    // -------------------------------------------------------------------------
    public function adminStats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                // Requests
                'total_requests'      => RequestModel::count(),
                'pending_requests'    => RequestModel::where('status', 'pending')->count(),
                'completed_requests'  => RequestModel::where('status', 'completed')->whereMonth('created_at', now()->month)->count(),
                // Items
                'total_items'         => Item::count(),
                'low_stock_items'     => Item::whereHas('stocks', fn($q) => $q->whereColumn('quantity', '<=', 'minimum_stock')->where('minimum_stock', '>', 0))->count(),
                // PO
                'total_pos'           => PurchaseOrder::count(),
                'pending_pos'         => PurchaseOrder::whereIn('status', ['draft', 'approved', 'ordered'])->count(),
                // Users & opname
                'total_users'         => \App\Models\User::count(),
                'total_units'         => \App\Models\Unit::count(),
                'open_opname'         => StockOpname::whereIn('status', ['open', 'in_progress'])->count(),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Charts Admin
    // -------------------------------------------------------------------------
    public function adminCharts(): JsonResponse
    {
        // Permintaan per bulan 12 bulan terakhir
        $monthlyRequests = collect(range(11, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'label'     => $date->translatedFormat('M Y'),
                'total'     => RequestModel::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
                'completed' => RequestModel::where('status', 'completed')->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
            ];
        });

        // PO per bulan 6 bulan terakhir
        $monthlyPOs = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'label' => $date->translatedFormat('M Y'),
                'total' => PurchaseOrder::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
                'value' => (float) PurchaseOrder::where('status', 'received')->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->sum('total_amount'),
            ];
        });

        // Top 5 unit paling aktif mengajukan bulan ini
        $topUnits = DB::table('requests')
            ->join('units', 'units.id', '=', 'requests.unit_id')
            ->whereMonth('requests.created_at', now()->month)
            ->whereYear('requests.created_at', now()->year)
            ->select('units.name', DB::raw('count(*) as total'))
            ->groupBy('units.id', 'units.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Distribusi status request bulan ini
        $statusDist = RequestModel::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabels = [
            'pending'   => 'Menunggu',
            'approved'  => 'Disetujui',
            'partial'   => 'Sebagian',
            'rejected'  => 'Ditolak',
            'ready'     => 'Siap Ambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return response()->json([
            'success' => true,
            'data'    => [
                'monthly_requests' => $monthlyRequests->values(),
                'monthly_pos'      => $monthlyPOs->values(),
                'top_units'        => $topUnits,
                'status_dist'      => $statusDist->mapWithKeys(fn($v, $k) => [
                    ($statusLabels[$k] ?? $k) => $v,
                ]),
            ],
        ]);
    }
}
