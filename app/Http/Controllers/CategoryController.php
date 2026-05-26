<?php

namespace App\Http\Controllers;

use App\Enums\ItemType;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = Category::query();

            // Tambahkan filter
            if ($request->search) {
                $categories->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            }

            if ($request->type != 'all') {
                $categories->where('item_type', $request->type);
            }

            if ($request->status != 'all') {
                $categories->where('is_active', $request->status == 'active');
            }

            return DataTables::of($categories)
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
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <button class="btn btn-sm btn-outline-primary btn-edit d-flex align-items-center justify-content-center editCategoryBtn"
                                    data-id="' . $row->id . '" title="Edit Unit"
                                    style="border-radius: var(--radius-sm); width: 30px; height: 30px; padding: 0;">
                                <i class="bi bi-pencil-square" style="font-size: 14px;"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete d-flex align-items-center justify-content-center deleteCategoryBtn"
                                    data-id="' . $row->id . '" data-name="'.$row->name.'" title="Hapus Unit"
                                    style="border-radius: var(--radius-sm); width: 30px; height: 30px; padding: 0;">
                                <i class="bi bi-trash" style="font-size: 14px;"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['item_type','status', 'action'])
                ->make(true);
        }

        $totalCategories    = Category::count();
        $consumableCategory = Category::where('item_type', ItemType::Consumable)->count();
        $inventoryCategory  = Category::where('item_type', ItemType::Inventory)->count();

        return view('category.index', compact('totalCategories', 'consumableCategory', 'inventoryCategory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'item_type' => 'in:consumable,inventory',
            'is_active' => 'boolean',
            'code' => 'required|unique:categories,code',
        ], [
            'name.required' => 'Nama kategori harus diisi.',
            'item_type.required' => 'Jenis barang harus diisi.',
            'item_type.in' => 'Jenis barang harus consumable atau inventory.',
            'is_active.required' => 'Status harus diisi.',
            'is_active.boolean' => 'Status harus boolean.',
            'code.required' => 'Kode kategori harus diisi.',
            'code.unique' => 'Kode kategori ini sudah digunakan.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;
        $validated['description'] = $request->has('description') ? $request->description : null;

        // $lastCategory = Category::latest()->first();
        // $validated['code'] = $lastCategory ? 'KTG-' . str_pad($lastCategory->id + 1, 4, '0', STR_PAD_LEFT) : 'KTG-0001';
        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori baru "' . $category->name . '" berhasil ditambahkan!',
            'data' => $category
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required',
            'item_type' => 'in:consumable,inventory',
            'is_active' => 'boolean',
            'code' => ['required', Rule::unique('categories')->ignore($category->id)],
        ], [
            'name.required' => 'Nama kategori harus diisi.',
            'item_type.required' => 'Jenis barang harus diisi.',
            'item_type.in' => 'Jenis barang harus consumable atau inventory.',
            'is_active.required' => 'Status harus diisi.',
            'is_active.boolean' => 'Status harus boolean.',
            'code.required' => 'Kode kategori harus diisi.',
            'code.unique' => 'Kode kategori ini sudah digunakan.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : $category->is_active;
        $validated['description'] = $request->has('description') ? $request->description : null;

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori "' . $category->name . '" berhasil diperbarui!',
            'data' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Cek apakah category sudah memiliki relasi ke item atau belum
        if ($category->items()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus! Kategori ini masih terhubung dengan beberapa data barang.'
            ]); // HTTP 200 — penolakan bisnis logis, bukan error sistem
        }

        $categoryName = $category->name;
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori "' . $categoryName . '" berhasil dihapus!'
        ]);
    }

    /**
     * Toggle status aktif/nonaktif Unit secara AJAX.
     */
    public function toggleStatus(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active
        ]);

        $statusStr = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => 'Status kategori "' . $category->name . '" berhasil ' . $statusStr . '!',
            'data' => $category
        ]);
    }
}
