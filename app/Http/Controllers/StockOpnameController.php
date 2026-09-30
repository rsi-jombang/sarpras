<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockOpname\StoreStockOpnameRequest;
use App\Http\Requests\StockOpname\UpdateStockOpnameItemsRequest;
use App\Models\ItemStock;
use App\Models\StockOpname;
use Illuminate\Support\Facades\DB;
use App\Services\StockOpnameService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockOpnameController extends Controller
{
    public function __construct(
        private readonly StockOpnameService $stockOpnameService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', StockOpname::class);

        $openOpname = StockOpname::whereIn('status', ['open', 'in_progress'])
            ->latest('id')
            ->first();

        return view('stock-opname.index', compact('openOpname'));
    }

    public function create(): View
    {
        $this->authorize('create', StockOpname::class);

        return view('stock-opname.create');
    }

    public function store(StoreStockOpnameRequest $request): RedirectResponse
    {
        $this->authorize('create', StockOpname::class);

        try {
            $opname = $this->stockOpnameService->create($request->user(), $request->validated());
        } catch (\RuntimeException $e) {
            return redirect()->route('stock-opname.index')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('stock-opname.show', $opname)
            ->with('success', 'Stok opname ' . $opname->opname_number . ' berhasil dibuat. Mulai input hasil hitung fisik.');
    }

    public function show(StockOpname $stockOpname): View
    {
        $this->authorize('view', $stockOpname);

        $stockOpname->load('creator', 'closer');

        // Hitung progress
        $totalItems   = $stockOpname->items()->count();
        $countedItems = $stockOpname->items()->whereNotNull('physical_quantity')->count();
        $diffItems    = $stockOpname->items()->where('difference', '!=', 0)->whereNotNull('difference')->count();
        $progress     = $totalItems > 0 ? round(($countedItems / $totalItems) * 100) : 0;

        // Item berstok yang belum tercakup opname ini (lahir setelah snapshot)
        $uncoveredCount = ItemStock::whereNotIn('item_id', $stockOpname->items()->select('item_id'))
            ->distinct()
            ->count('item_id');

        return view('stock-opname.show', compact(
            'stockOpname', 'totalItems', 'countedItems', 'diffItems', 'progress',
            'uncoveredCount'
        ));
    }

    /** Tarik item baru yang belum tercakup ke dalam opname */
    public function syncItems(Request $httpRequest, StockOpname $stockOpname): RedirectResponse
    {
        $this->authorize('update', $stockOpname);

        $added = $this->stockOpnameService->syncItems($stockOpname);

        return redirect()->route('stock-opname.show', $stockOpname)
            ->with(
                'success',
                $added > 0
                    ? "{$added} item baru berhasil ditarik ke dalam opname."
                    : 'Tidak ada item baru. Semua item berstok sudah tercakup.'
            );
    }

    public function updateItems(UpdateStockOpnameItemsRequest $request, StockOpname $stockOpname): RedirectResponse
    {
        $this->authorize('update', $stockOpname);

        $this->stockOpnameService->updateItems($stockOpname, $request->validated(), $request->user());

        return redirect()->route('stock-opname.show', $stockOpname)
            ->with('success', 'Hasil hitung fisik berhasil disimpan.');
    }

    /** Halaman konfirmasi sebelum closing */
    public function closeConfirm(StockOpname $stockOpname): View
    {
        $this->authorize('close', $stockOpname);

        $stockOpname->load('creator');

        $totalItems    = $stockOpname->items()->count();
        $countedItems  = $stockOpname->items()->whereNotNull('physical_quantity')->count();
        $uncountedItems= $totalItems - $countedItems;
        $diffItems     = $stockOpname->items()->where('difference', '!=', 0)->whereNotNull('difference')->count();
        $surplusItems  = $stockOpname->items()->where('difference', '>', 0)->count();
        $deficitItems  = $stockOpname->items()->where('difference', '<', 0)->count();

        // Ambil item yang punya selisih untuk ditampilkan di ringkasan
        $itemsWithDiff = $stockOpname->items()
            ->with(['item.category'])
            ->where('difference', '!=', 0)
            ->whereNotNull('difference')
            ->orderBy('difference')
            ->get();

        // Total stok terkini per item (basis adjustment saat close, bukan snapshot);
        // tandai baris yang berubah sejak snapshot awal (mutasi operasional).
        $currentTotals = ItemStock::whereIn('item_id', $itemsWithDiff->pluck('item_id')->unique())
            ->select('item_id', DB::raw('SUM(quantity) as total'))
            ->groupBy('item_id')
            ->pluck('total', 'item_id');

        $itemsWithDiff->each(function ($oi) use ($currentTotals) {
            $oi->current_total = (int) ($currentTotals[$oi->item_id] ?? 0);
        });

        $changedCount = $itemsWithDiff
            ->filter(fn($oi) => $oi->current_total !== (int) $oi->system_quantity)
            ->count();

        return view('stock-opname.close-confirm', compact(
            'stockOpname',
            'totalItems', 'countedItems', 'uncountedItems',
            'diffItems', 'surplusItems', 'deficitItems',
            'itemsWithDiff', 'changedCount'
        ));
    }

    public function close(Request $request, StockOpname $stockOpname): RedirectResponse
    {
        $this->authorize('close', $stockOpname);

        try {
            $this->stockOpnameService->close($stockOpname, $request->user());
        } catch (\RuntimeException $e) {
            return redirect()->route('stock-opname.close.confirm', $stockOpname)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('stock-opname.show', $stockOpname)
            ->with('success', 'Stok opname ' . $stockOpname->opname_number . ' berhasil ditutup. Selisih telah disesuaikan.');
    }
}
