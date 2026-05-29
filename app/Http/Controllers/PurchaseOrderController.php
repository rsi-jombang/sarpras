<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderStatus;
use App\Http\Requests\PurchaseOrder\ReceivePurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\StorePurchaseOrderRequest;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private readonly PurchaseOrderService $purchaseOrderService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', PurchaseOrder::class);

        $query = PurchaseOrder::with(['creator', 'approver'])->latest();

        if ($request->filled('status')) {
            $query->byStatus(PurchaseOrderStatus::from($request->status));
        }

        $purchaseOrders = $query->paginate(15)->withQueryString();

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    public function create(): View
    {
        $this->authorize('create', PurchaseOrder::class);

        $items = Item::with('category')->active()->orderBy('name')->get();

        return view('purchase-orders.create', compact('items'));
    }

    public function store(StorePurchaseOrderRequest $request): RedirectResponse
    {
        $this->authorize('create', PurchaseOrder::class);

        $this->purchaseOrderService->create($request->user(), $request->validated());

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dibuat.');
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $this->authorize('view', $purchaseOrder);

        $purchaseOrder->load(['creator', 'approver', 'items.item.category', 'items.itemStock']);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder): View
    {
        $this->authorize('update', $purchaseOrder);

        $items = Item::with('category')->active()->orderBy('name')->get();
        $purchaseOrder->load('items.item');

        return view('purchase-orders.edit', compact('purchaseOrder', 'items'));
    }

    public function update(StorePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('update', $purchaseOrder);

        $this->purchaseOrderService->update($purchaseOrder, $request->validated());

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order berhasil diperbarui.');
    }

    public function approve(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('approve', $purchaseOrder);

        $this->purchaseOrderService->approve($purchaseOrder, $request->user());

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order disetujui.');
    }

    public function receive(ReceivePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('receive', $purchaseOrder);

        $this->purchaseOrderService->receive($purchaseOrder, $request->user(), $request->validated());

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Barang berhasil diterima. Stok telah diperbarui.');
    }

    public function cancel(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('cancel', $purchaseOrder);

        $this->purchaseOrderService->cancel($purchaseOrder);

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order dibatalkan.');
    }

    public function trash()
    {
        return view('purchase-orders.trash');
    }

    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('delete', $purchaseOrder);

        $this->purchaseOrderService->delete($purchaseOrder);

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dihapus.');
    }
}
