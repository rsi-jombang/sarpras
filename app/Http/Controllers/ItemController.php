<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\ItemStock;
use App\Enums\ItemType;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    public function __construct(
        private readonly ItemService $itemService,
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = Item::with('category')->withTotalStock();

            if ($request->search) {
                $items->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('code', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->category && $request->category != 'all') {
                $items->where('category_id', $request->category);
            }

            if ($request->type && $request->type != 'all') {
                $items->where('item_type', $request->type);
            }

            if ($request->status && $request->status != 'all') {
                $items->where('is_active', $request->status == 'active');
            }

            return DataTables::of($items)
                ->addIndexColumn()
                ->editColumn('item_type', function($row){
                    return $row->item_type->value == 'consumable' ? '<span class="badge text-bg-warning">Habis Pakai</span>' : '<span class="badge text-bg-success">Inventaris</span>';
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->is_active ? 'checked' : '';
                    return '
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input btn-toggle-status" type="checkbox" role="switch"
                                   data-id="' . $row->id . '" ' . $checked . ' style="cursor: pointer;">
                        </div>
                    ';
                })
                ->addColumn('image', function ($row) {
                    if ($row->image_path) {
                        return '<img src="'.asset('storage/'.$row->image_path).'" alt="'.$row->name.'" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">';
                    }
                    return '<div class="rounded bg-secondary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; color: white;"><i class="bi bi-box"></i></div>';
                })
                ->addColumn('total_stock', function($row){
                    return $row->total_stock ?? 0;
                })
                ->addColumn('action', function ($row) {
                    $btnMinStock = '';
                    if (auth()->user()->role->value === 'admin') {
                        $btnMinStock = '
                            <button class="btn btn-sm btn-outline-warning btn-min-stock d-flex align-items-center justify-content-center minStockBtn"
                                    data-id="' . $row->id . '" data-name="'.$row->name.'" data-min="'.$row->minimum_stock.'" title="Set Stok Aman"
                                    style="border-radius: var(--radius-sm); width: 30px; height: 30px; padding: 0;">
                                <i class="bi bi-shield-exclamation" style="font-size: 14px;"></i>
                            </button>';
                    }

                    return '
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            ' . $btnMinStock . '
                            <button class="btn btn-sm btn-outline-info btn-stock d-flex align-items-center justify-content-center viewStockBtn"
                                    data-id="' . $row->id . '" title="Lihat Stok"
                                    style="border-radius: var(--radius-sm); width: 30px; height: 30px; padding: 0;">
                                <i class="bi bi-box-seam" style="font-size: 14px;"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary btn-edit d-flex align-items-center justify-content-center editItemBtn"
                                    data-id="' . $row->id . '" title="Edit Barang"
                                    style="border-radius: var(--radius-sm); width: 30px; height: 30px; padding: 0;">
                                <i class="bi bi-pencil-square" style="font-size: 14px;"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete d-flex align-items-center justify-content-center deleteItemBtn"
                                    data-id="' . $row->id . '" data-name="'.$row->name.'" title="Hapus Barang"
                                    style="border-radius: var(--radius-sm); width: 30px; height: 30px; padding: 0;">
                                <i class="bi bi-trash" style="font-size: 14px;"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['item_type', 'status', 'image', 'action'])
                ->make(true);
        }

        $totalItems = Item::count();
        $categories = Category::active()->get();
        $units = ['Pcs', 'Box', 'Roll', 'Lembar', 'Botol', 'Pack', 'Set'];

        return view('item.index', compact('totalItems', 'categories', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request)
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        $item = $this->itemService->create($validated, $request->file('image'));

        if ($request->filled('initial_stock') && $request->initial_stock > 0) {
            ItemStock::create([
                'item_id' => $item->id,
                'quantity' => $request->initial_stock,
                'purchase_price' => 0,
                'selling_price' => 0,
                'minimum_stock' => 0,
                'notes' => 'Stok Awal'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Barang baru "' . $item->name . '" berhasil ditambahkan!',
            'data' => $item
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return response()->json([
            'success' => true,
            'data' => $item->load('category')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        return response()->json([
            'success' => true,
            'data' => $item,
            'image_url' => $item->image_path ? asset('storage/' . $item->image_path) : null
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $path = $request->file('image')->store('items', 'public');
            $validated['image_path'] = $path;
        }

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : $item->is_active;

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Barang "' . $item->name . '" berhasil diperbarui!',
            'data' => $item
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $itemName = $item->name;

        try {
            $this->itemService->delete($item);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Barang "' . $itemName . '" berhasil dihapus!'
        ]);
    }

    /**
     * Toggle status aktif/nonaktif.
     */
    public function toggleStatus(Item $item)
    {
        $item->update([
            'is_active' => !$item->is_active
        ]);

        $statusStr = $item->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => 'Status barang "' . $item->name . '" berhasil ' . $statusStr . '!',
            'data' => $item
        ]);
    }

    /**
     * Get item stocks
     */
    public function stocks(Item $item): JsonResponse
    {
        $stocks = $item->stocks()->orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $stocks
        ]);
    }

    /**
     * Update stok aman — hanya admin.
     */
    public function updateMinimumStock(Request $request, Item $item): JsonResponse
    {
        // Gate: hanya admin
        if (auth()->user()->role->value !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengubah stok aman.'
            ], 403);
        }

        $request->validate([
            'minimum_stock' => 'required|integer|min:0|max:99999',
        ], [
            'minimum_stock.required' => 'Nilai stok aman harus diisi.',
            'minimum_stock.integer'  => 'Stok aman harus berupa angka bulat.',
            'minimum_stock.min'      => 'Stok aman tidak boleh negatif.',
            'minimum_stock.max'      => 'Stok aman terlalu besar.',
        ]);

        $oldValue = $item->minimum_stock;
        $newValue = (int) $request->minimum_stock;

        $item->update(['minimum_stock' => $newValue]);

        // Manual activity log dengan detail perubahan (old → new)
        activity('stok_aman')
            ->performedOn($item)
            ->causedBy(auth()->user())
            ->withProperties([
                'old' => $oldValue,
                'new' => $newValue,
            ])
            ->log('Admin "' . auth()->user()->name . '" mengubah stok aman "' . $item->name . '" dari ' . $oldValue . ' → ' . $newValue);

        return response()->json([
            'success' => true,
            'message' => 'Stok aman "' . $item->name . '" berhasil diubah menjadi ' . $newValue . '.',
            'data'    => ['minimum_stock' => $newValue]
        ]);
    }
}
