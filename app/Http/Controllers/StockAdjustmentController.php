<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustment\StoreStockAdjustmentRequest;
use App\Models\Item;
use App\Models\StockAdjustment;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StockAdjustmentController extends Controller
{
    public function __construct(
        private readonly StockService $stockService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', StockAdjustment::class);

        $adjustments = StockAdjustment::with(['item', 'adjustedBy'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('stock-adjustments.index', compact('adjustments'));
    }

    public function create(): View
    {
        $this->authorize('create', StockAdjustment::class);

        $items = Item::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('stock-adjustments.create', compact('items'));
    }

    public function store(StoreStockAdjustmentRequest $request): RedirectResponse
    {
        try {
            $item = Item::findOrFail($request->item_id);

            $adjustment = $this->stockService->adjustStock(
                item: $item,
                adjustmentQty: $request->quantity_adjustment,
                reason: $request->reason,
                adjustedBy: auth()->id(),
                notes: $request->notes,
            );

            return redirect()->route('stock-adjustments.show', $adjustment)
                ->with('success', 'Penyesuaian stok berhasil disimpan');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(StockAdjustment $stockAdjustment): View
    {
        $this->authorize('view', $stockAdjustment);

        $stockAdjustment->load(['item', 'itemStock', 'adjustedBy']);

        return view('stock-adjustments.show', compact('stockAdjustment'));
    }

    public function destroy(StockAdjustment $stockAdjustment): RedirectResponse
    {
        $this->authorize('delete', $stockAdjustment);

        $stockAdjustment->delete();

        return redirect()->route('stock-adjustments.index')
            ->with('success', 'Penyesuaian stok berhasil dihapus');
    }
}
