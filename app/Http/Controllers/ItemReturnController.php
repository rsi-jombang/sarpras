<?php

namespace App\Http\Controllers;

use App\Http\Requests\Return\StoreItemReturnRequest;
use App\Models\ItemReturn;
use App\Services\ItemReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemReturnController extends Controller
{
    public function __construct(
        private readonly ItemReturnService $itemReturnService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', ItemReturn::class);
        return view('item-returns.index');
    }

    public function create(): View
    {
        $this->authorize('create', ItemReturn::class);
        return view('item-returns.create');
    }

    public function store(StoreItemReturnRequest $request): RedirectResponse
    {
        $this->authorize('create', ItemReturn::class);

        $ret = $this->itemReturnService->create($request->user(), $request->validated());

        return redirect()->route('item-returns.show', $ret)
            ->with('success', 'Draft pengembalian ' . $ret->return_number . ' berhasil disimpan.');
    }

    public function show(ItemReturn $itemReturn): View
    {
        $this->authorize('view', $itemReturn);
        $itemReturn->load(['creator', 'unit', 'receiver', 'items.item', 'items.requestItem']);
        return view('item-returns.show', compact('itemReturn'));
    }

    public function edit(ItemReturn $itemReturn): View
    {
        $this->authorize('update', $itemReturn);
        $itemReturn->load(['items.item']);
        return view('item-returns.edit', compact('itemReturn'));
    }

    public function update(StoreItemReturnRequest $request, ItemReturn $itemReturn): RedirectResponse
    {
        $this->authorize('update', $itemReturn);
        $this->itemReturnService->update($itemReturn, $request->validated());
        return redirect()->route('item-returns.show', $itemReturn)
            ->with('success', 'Draft pengembalian diperbarui.');
    }

    public function submit(Request $httpRequest, ItemReturn $itemReturn): RedirectResponse
    {
        $this->authorize('submit', $itemReturn);
        $this->itemReturnService->submit($itemReturn);
        return redirect()->route('item-returns.show', $itemReturn)
            ->with('success', 'Pengembalian ' . $itemReturn->return_number . ' dikirim. Menunggu penerimaan Sarpras.');
    }

    public function receive(Request $httpRequest, ItemReturn $itemReturn): RedirectResponse
    {
        $this->authorize('receive', $itemReturn);
        $this->itemReturnService->receive($itemReturn, $httpRequest->user());
        return redirect()->route('item-returns.show', $itemReturn)
            ->with('success', 'Pengembalian ' . $itemReturn->return_number . ' diterima. Stok diperbarui.');
    }

    public function cancel(Request $httpRequest, ItemReturn $itemReturn): RedirectResponse
    {
        $this->authorize('cancel', $itemReturn);
        $this->itemReturnService->cancel($itemReturn, $httpRequest->user());
        return redirect()->route('item-returns.index')
            ->with('success', 'Pengembalian berhasil dibatalkan.');
    }

    public function destroy(ItemReturn $itemReturn): RedirectResponse
    {
        $this->authorize('delete', $itemReturn);
        $this->itemReturnService->delete($itemReturn);
        return redirect()->route('item-returns.index')
            ->with('success', 'Pengembalian berhasil dihapus.');
    }
}