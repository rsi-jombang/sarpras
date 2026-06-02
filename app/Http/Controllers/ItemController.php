<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Services\ItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(
        private readonly ItemService $itemService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Item::class);

        return view('items.index');
    }

    public function trash(): View
    {
        $this->authorize('viewAny', Item::class);

        return view('items.trash');
    }

    public function create(): View
    {
        $this->authorize('create', Item::class);

        return view('items.create');
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        $this->authorize('create', Item::class);

        $this->itemService->create($request->validated(), $request->file('image'));

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Item $item): View
    {
        $this->authorize('view', $item);

        $item->load('category');

        return view('items.show', compact('item'));
    }

    public function edit(Item $item): View
    {
        $this->authorize('update', $item);

        return view('items.edit', compact('item'));
    }

    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        $this->authorize('update', $item);

        $this->itemService->update($item, $request->validated(), $request->file('image'));

        return redirect()->route('items.show', $item)->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $this->authorize('delete', $item);

        try {
            $this->itemService->delete($item);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }
}
