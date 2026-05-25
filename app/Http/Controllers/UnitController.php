<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    /**
     * Tampilkan halaman utama Unit, atau kembalikan data AJAX untuk Yajra DataTables.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $units = Unit::query(); // Mengambil data Unit (termasuk query builder untuk Yajra)

            return DataTables::of($units)
                ->addIndexColumn()
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
                            <button class="btn btn-sm btn-outline-primary btn-edit d-flex align-items-center justify-content-center"
                                    data-id="' . $row->id . '" title="Edit Unit"
                                    style="border-radius: var(--radius-sm); width: 30px; height: 30px; padding: 0;">
                                <i class="bi bi-pencil-square" style="font-size: 14px;"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete d-flex align-items-center justify-content-center"
                                    data-id="' . $row->id . '" title="Hapus Unit"
                                    style="border-radius: var(--radius-sm); width: 30px; height: 30px; padding: 0;">
                                <i class="bi bi-trash" style="font-size: 14px;"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $totalUnits = Unit::count('*');
        $activeUnits = Unit::count('is_active', true);
        $inactiveUnits = Unit::count('is_active', false);

        return view('unit.index', compact('totalUnits', 'activeUnits', 'inactiveUnits'));
    }

    /**
     * Simpan Unit baru secara AJAX.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:units,code'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama unit wajib diisi.',
            'name.max' => 'Nama unit maksimal 255 karakter.',
            'code.required' => 'Kode unit wajib diisi.',
            'code.max' => 'Kode unit maksimal 20 karakter.',
            'code.unique' => 'Kode unit ini sudah terdaftar di sistem.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        $unit = Unit::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Unit baru "' . $unit->name . '" berhasil ditambahkan!',
            'data' => $unit
        ], 201);
    }

    /**
     * Ambil data satu Unit untuk diedit (AJAX).
     */
    public function show(Unit $unit)
    {
        return response()->json([
            'success' => true,
            'data' => $unit
        ]);
    }

    /**
     * Perbarui Unit secara AJAX.
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', Rule::unique('units')->ignore($unit->id)],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama unit wajib diisi.',
            'name.max' => 'Nama unit maksimal 255 karakter.',
            'code.required' => 'Kode unit wajib diisi.',
            'code.max' => 'Kode unit maksimal 20 karakter.',
            'code.unique' => 'Kode unit ini sudah terdaftar di sistem.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : $unit->is_active;

        $unit->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Unit "' . $unit->name . '" berhasil diperbarui!',
            'data' => $unit
        ]);
    }

    /**
     * Hapus Unit secara AJAX (Soft Delete).
     */
    public function destroy(Unit $unit)
    {
        // Cek apakah unit masih terikat ke user aktif untuk menjaga integritas data riil
        if ($unit->users()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus! Unit ini masih terikat dengan beberapa akun pengguna (User).'
            ], 422);
        }

        // Cek juga jika ada request dari unit ini
        if ($unit->requests()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus! Unit ini memiliki riwayat pengajuan permintaan barang.'
            ], 422);
        }

        $unitName = $unit->name;
        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit "' . $unitName . '" berhasil dihapus!'
        ]);
    }

    /**
     * Toggle status aktif/nonaktif Unit secara AJAX.
     */
    public function toggleStatus(Unit $unit)
    {
        $unit->update([
            'is_active' => !$unit->is_active
        ]);

        $statusStr = $unit->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => 'Status unit "' . $unit->name . '" berhasil ' . $statusStr . '!',
            'data' => $unit
        ]);
    }
}
