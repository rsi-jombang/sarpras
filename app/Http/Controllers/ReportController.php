<?php

namespace App\Http\Controllers;

use App\Exports\RequestsExport;
use App\Exports\ItemStocksExport;
use App\Exports\PurchaseOrdersExport;
use App\Exports\StockOpnameExport;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\Request as RequestModel;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    // =========================================================================
    // INDEX — halaman utama laporan
    // =========================================================================
    public function index(): View
    {
        $units = Unit::active()->orderBy('name')->get(['id', 'name']);

        return view('reports.index', compact('units'));
    }

    // =========================================================================
    // LAPORAN PERMINTAAN BARANG
    // =========================================================================
    public function requestsPreview(Request $request): View
    {
        $filters  = $this->requestFilters($request);
        $requests = $this->queryRequests($filters)->paginate(30)->withQueryString();
        $summary  = $this->requestSummary($filters);
        $units    = Unit::active()->orderBy('name')->get(['id', 'name']);

        return view('reports.requests', compact('requests', 'summary', 'filters', 'units'));
    }

    public function requestsPdf(Request $request): Response
    {
        $filters  = $this->requestFilters($request);
        $requests = $this->queryRequests($filters)->get();
        $summary  = $this->requestSummary($filters);

        $pdf = Pdf::loadView('reports.pdf.requests', compact('requests', 'summary', 'filters'))
            ->setPaper('a4', 'landscape')
            ->setOptions($this->pdfOptions());

        return $pdf->download('laporan-permintaan-' . now()->format('Ymd-His') . '.pdf');
    }

    public function requestsExcel(Request $request): BinaryFileResponse
    {
        $filters  = $this->requestFilters($request);
        $filename = 'laporan-permintaan-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new RequestsExport($filters), $filename);
    }

    private function requestFilters(Request $request): array
    {
        return [
            'unit_id'   => $request->input('unit_id'),
            'status'    => $request->input('status'),
            'priority'  => $request->input('priority'),
            'date_from' => $request->input('date_from'),
            'date_to'   => $request->input('date_to'),
        ];
    }

    private function queryRequests(array $filters)
    {
        $query = RequestModel::with(['requester', 'unit', 'reviewer', 'server', 'items.item'])
            ->latest('request_date');

        if (!empty($filters['unit_id']))   $query->where('unit_id', $filters['unit_id']);
        if (!empty($filters['status']))    $query->where('status', $filters['status']);
        if (!empty($filters['priority']))  $query->where('priority', $filters['priority']);
        if (!empty($filters['date_from'])) $query->whereDate('request_date', '>=', $filters['date_from']);
        if (!empty($filters['date_to']))   $query->whereDate('request_date', '<=', $filters['date_to']);

        return $query;
    }

    private function requestSummary(array $filters): array
    {
        $query = RequestModel::query();
        if (!empty($filters['unit_id']))   $query->where('unit_id', $filters['unit_id']);
        if (!empty($filters['date_from'])) $query->whereDate('request_date', '>=', $filters['date_from']);
        if (!empty($filters['date_to']))   $query->whereDate('request_date', '<=', $filters['date_to']);

        return [
            'total'     => (clone $query)->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'rejected'  => (clone $query)->where('status', 'rejected')->count(),
            'pending'   => (clone $query)->where('status', 'pending')->count(),
        ];
    }

    // =========================================================================
    // LAPORAN STOK BARANG
    // =========================================================================
    public function stocksPreview(Request $request): View
    {
        $filters = $this->stockFilters($request);
        $items   = $this->queryStocks($filters)->paginate(30)->withQueryString();
        $summary = $this->stocksSummary($items->pluck('id'), $filters);

        return view('reports.stocks', compact('items', 'filters', 'summary'));
    }

    public function stocksPdf(Request $request): Response
    {
        $filters = $this->stockFilters($request);
        $items   = $this->queryStocks($filters)->get();
        $summary = $this->stocksSummary($items->pluck('id'), $filters);

        $pdf = Pdf::loadView('reports.pdf.stocks', compact('items', 'filters', 'summary'))
            ->setPaper('a4', 'landscape')
            ->setOptions($this->pdfOptions());

        return $pdf->download('laporan-stok-' . now()->format('Ymd-His') . '.pdf');
    }

    public function stocksExcel(Request $request): BinaryFileResponse
    {
        $filters  = $this->stockFilters($request);
        $summary  = $this->stocksSummary($this->queryStocks($filters)->pluck('id'), $filters);
        $filename = 'laporan-stok-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new ItemStocksExport($filters, $summary), $filename);
    }

    private function stockFilters(Request $request): array
    {
        return [
            'item_type'    => $request->input('item_type'),
            'category_id'  => $request->input('category_id'),
            'stock_status' => $request->input('stock_status'),
            'date_from'    => $request->input('date_from', now()->startOfMonth()->toDateString()),
            'date_to'      => $request->input('date_to', now()->toDateString()),
        ];
    }

    /**
     * Hitung Stok Awal / Masuk / Keluar / Tersedia per item.
     * - Awal   : hasil opname terakhir yang ditutup sebelum date_from
     * - Masuk  : SUM qty positif stock_movements pada periode
     * - Keluar : SUM |qty negatif| stock_movements pada periode
     * - Tersedia : Awal + Masuk - Keluar
     */
    private function stocksSummary($itemIds, array $filters): \Illuminate\Support\Collection
    {
        $itemIds = collect($itemIds)->filter()->unique()->values();

        if ($itemIds->isEmpty()) {
            return collect();
        }

        $dateFrom = Carbon::parse($filters['date_from'])->startOfDay();
        $dateTo   = Carbon::parse($filters['date_to'])->endOfDay();

        // Stok awal dari opname terakhir yang ditutup sebelum date_from
        $opname = StockOpname::where('status', 'closed')
            ->whereDate('closed_date', '<=', $dateFrom)
            ->latest('closed_date')
            ->first();

        $awalByItem = collect();
        if ($opname) {
            $awalByItem = StockOpnameItem::where('stock_opname_id', $opname->id)
                ->whereIn('item_id', $itemIds)
                ->select('item_id', DB::raw('SUM(physical_quantity) as awal'))
                ->groupBy('item_id')
                ->pluck('awal', 'item_id');
        }

        // Masuk & keluar dari stock_movements pada periode
        $movements = StockMovement::whereIn('item_id', $itemIds)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(
                'item_id',
                DB::raw('SUM(CASE WHEN quantity > 0 THEN quantity ELSE 0 END) as masuk'),
                DB::raw('SUM(CASE WHEN quantity < 0 THEN ABS(quantity) ELSE 0 END) as keluar')
            )
            ->groupBy('item_id')
            ->get()
            ->keyBy('item_id');

        return $itemIds->mapWithKeys(function ($id) use ($awalByItem, $movements) {
            $awal   = (int) ($awalByItem[$id] ?? 0);
            $masuk  = (int) ($movements[$id]->masuk ?? 0);
            $keluar = (int) ($movements[$id]->keluar ?? 0);

            return [$id => [
                'awal'     => $awal,
                'masuk'    => $masuk,
                'keluar'   => $keluar,
                'tersedia' => $awal + $masuk - $keluar,
            ]];
        });
    }

    private function queryStocks(array $filters)
    {
        $query = Item::with(['category', 'stocks'])
            ->withSum('stocks as total_stock', 'quantity')
            ->active()
            ->orderBy('name');

        if (!empty($filters['item_type']))   $query->where('item_type', $filters['item_type']);
        if (!empty($filters['category_id'])) $query->where('category_id', $filters['category_id']);

        if (!empty($filters['stock_status'])) {
            match ($filters['stock_status']) {
                'low'   => $query->whereHas('stocks', fn($q) => $q->whereColumn('quantity', '<=', 'minimum_stock')->where('minimum_stock', '>', 0)),
                'empty' => $query->whereDoesntHave('stocks', fn($q) => $q->where('quantity', '>', 0)),
                default => null,
            };
        }

        return $query;
    }

    // =========================================================================
    // LAPORAN PURCHASE ORDER
    // =========================================================================
    public function purchaseOrdersPreview(Request $request): View
    {
        $filters        = $this->poFilters($request);
        $purchaseOrders = $this->queryPOs($filters)->paginate(20)->withQueryString();
        $summary        = $this->poSummary($filters);

        return view('reports.purchase-orders', compact('purchaseOrders', 'summary', 'filters'));
    }

    public function purchaseOrdersPdf(Request $request): Response
    {
        $filters        = $this->poFilters($request);
        $purchaseOrders = $this->queryPOs($filters)->get();
        $summary        = $this->poSummary($filters);

        $pdf = Pdf::loadView('reports.pdf.purchase-orders', compact('purchaseOrders', 'summary', 'filters'))
            ->setPaper('a4', 'landscape')
            ->setOptions($this->pdfOptions());

        return $pdf->download('laporan-po-' . now()->format('Ymd-His') . '.pdf');
    }

    public function purchaseOrdersExcel(Request $request): BinaryFileResponse
    {
        $filters  = $this->poFilters($request);
        $filename = 'laporan-po-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new PurchaseOrdersExport($filters), $filename);
    }

    private function poFilters(Request $request): array
    {
        return [
            'status'    => $request->input('status'),
            'date_from' => $request->input('date_from'),
            'date_to'   => $request->input('date_to'),
        ];
    }

    private function queryPOs(array $filters)
    {
        $query = PurchaseOrder::with(['creator', 'approver', 'items.item'])
            ->latest('order_date');

        if (!empty($filters['status']))    $query->where('status', $filters['status']);
        if (!empty($filters['date_from'])) $query->whereDate('order_date', '>=', $filters['date_from']);
        if (!empty($filters['date_to']))   $query->whereDate('order_date', '<=', $filters['date_to']);

        return $query;
    }

    private function poSummary(array $filters): array
    {
        $query = PurchaseOrder::query();
        if (!empty($filters['date_from'])) $query->whereDate('order_date', '>=', $filters['date_from']);
        if (!empty($filters['date_to']))   $query->whereDate('order_date', '<=', $filters['date_to']);

        return [
            'total'        => (clone $query)->count(),
            'received'     => (clone $query)->where('status', 'received')->count(),
            'total_amount' => (clone $query)->where('status', 'received')->sum('total_amount'),
            'cancelled'    => (clone $query)->where('status', 'cancelled')->count(),
        ];
    }

    // =========================================================================
    // LAPORAN STOK OPNAME
    // =========================================================================
    public function opnamePreview(Request $request): View
    {
        $opnames = StockOpname::with(['creator', 'closer'])
            ->where('status', 'closed')
            ->latest('closed_date')
            ->paginate(15)
            ->withQueryString();

        $selectedOpname = null;
        $opnameItems    = collect();

        if ($request->filled('opname_id')) {
            $selectedOpname = StockOpname::with(['creator', 'closer'])
                ->findOrFail($request->opname_id);
            $opnameItems = $selectedOpname->items()
                ->with(['item.category', 'counter'])
                ->orderBy('difference')
                ->get();
        }

        return view('reports.opname', compact('opnames', 'selectedOpname', 'opnameItems'));
    }

    public function opnamePdf(Request $request): Response
    {
        $opname      = StockOpname::with(['creator', 'closer'])->findOrFail($request->opname_id);
        $opnameItems = $opname->items()->with(['item.category', 'counter'])->orderBy('difference')->get();

        $pdf = Pdf::loadView('reports.pdf.opname', compact('opname', 'opnameItems'))
            ->setPaper('a4', 'landscape')
            ->setOptions($this->pdfOptions());

        return $pdf->download('laporan-opname-' . $opname->opname_number . '-' . now()->format('Ymd') . '.pdf');
    }

    public function opnameExcel(Request $request): BinaryFileResponse
    {
        $opname   = StockOpname::findOrFail($request->opname_id);
        $filename = 'laporan-opname-' . $opname->opname_number . '-' . now()->format('Ymd') . '.xlsx';

        return Excel::download(new StockOpnameExport($opname), $filename);
    }

    private function pdfOptions(): array
    {
        return [
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'isJavascriptEnabled'  => false,
        ];
    }
}
