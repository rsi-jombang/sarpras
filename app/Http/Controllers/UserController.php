<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        if ($request->ajax() && $request->has('draw')) {
            $users = User::with('unit');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('role_badge', function (User $user) {
                    $map = [
                        'admin'   => ['danger',  'Administrator'],
                        'sarpras' => ['primary', 'Sarpras'],
                        'unit'    => ['secondary', 'Unit'],
                    ];
                    [$color, $label] = $map[$user->role->value] ?? ['secondary', $user->role->value];
                    return '<span class="badge bg-' . $color . '-subtle text-' . $color . ' px-2.5 py-1" style="font-size:11px">'
                        . $label . '</span>';
                })
                ->addColumn('unit_name', function (User $user) {
                    return $user->unit?->name ?? '<em class="text-muted" style="font-size:12.5px">—</em>';
                })
                ->addColumn('status', function (User $user) {
                    $checked = $user->is_active ? 'checked' : '';
                    return '<div class="form-check form-switch d-inline-block">
                        <input class="form-check-input btn-toggle-status" type="checkbox" role="switch"
                               data-id="' . $user->id . '" ' . $checked . ' style="cursor:pointer">
                    </div>';
                })
                ->addColumn('action', function (User $user) {
                    $buttons = '<button class="btn btn-sm btn-outline-primary btn-edit" data-id="' . $user->id . '" title="Edit"
                        style="border-radius:var(--radius-sm)"><i class="bi bi-pencil-square"></i></button>';

                    if ($user->id !== auth()->id()) {
                        $buttons .= '<button class="btn btn-sm btn-outline-danger btn-delete ms-1" data-id="' . $user->id . '" title="Hapus"
                            style="border-radius:var(--radius-sm)"><i class="bi bi-trash"></i></button>';
                    }

                    return '<div class="d-flex justify-content-end">' . $buttons . '</div>';
                })
                ->rawColumns(['role_badge', 'unit_name', 'status', 'action'])
                ->make(true);
        }

        $totalUsers  = User::count();
        $adminCount  = User::where('role', 'admin')->count();
        $sarprasCount = User::where('role', 'sarpras')->count();
        $unitCount   = User::where('role', 'unit')->count();
        $activeUsers = User::where('is_active', true)->count();
        $units       = Unit::active()->orderBy('name')->get(['id', 'name']);

        return view('user.index', compact('totalUsers', 'adminCount', 'sarprasCount', 'unitCount', 'activeUsers', 'units'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'username'    => ['required', 'string', 'max:50', 'unique:users,username'],
            'email'       => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password'    => ['required', 'string', 'min:8'],
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:users,employee_id'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'role'        => ['required', Rule::in(['admin', 'sarpras', 'unit'])],
            'unit_id'     => ['nullable', 'exists:units,id'],
            'is_active'   => ['nullable', 'boolean'],
        ], [
            'name.required'       => 'Nama pengguna wajib diisi.',
            'username.required'   => 'Username wajib diisi.',
            'username.unique'     => 'Username ini sudah terdaftar.',
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Email ini sudah terdaftar.',
            'password.required'   => 'Password wajib diisi.',
            'password.min'        => 'Password minimal 8 karakter.',
            'employee_id.unique'  => 'NIP/NIK ini sudah terdaftar.',
            'role.required'       => 'Role harus dipilih.',
            'role.in'             => 'Role tidak valid.',
            'unit_id.exists'      => 'Unit tidak valid.',
        ]);

        $validated['password']  = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna "' . $user->name . '" berhasil ditambahkan!',
            'data'    => $user,
        ], 201);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load('unit');

        return response()->json([
            'success' => true,
            'data'    => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'username'    => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email'       => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'    => ['nullable', 'string', 'min:8'],
            'employee_id' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'phone'       => ['nullable', 'string', 'max:20'],
            'role'        => ['required', Rule::in(['admin', 'sarpras', 'unit'])],
            'unit_id'     => ['nullable', 'exists:units,id'],
            'is_active'   => ['nullable', 'boolean'],
        ], [
            'name.required'       => 'Nama pengguna wajib diisi.',
            'username.required'   => 'Username wajib diisi.',
            'username.unique'     => 'Username ini sudah terdaftar.',
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Email ini sudah terdaftar.',
            'password.min'        => 'Password minimal 8 karakter.',
            'employee_id.unique'  => 'NIP/NIK ini sudah terdaftar.',
            'role.required'       => 'Role harus dipilih.',
            'role.in'             => 'Role tidak valid.',
            'unit_id.exists'      => 'Unit tidak valid.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna "' . $user->name . '" berhasil diperbarui!',
            'data'    => $user,
        ]);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri!',
            ], 422);
        }

        $userName = $user->name;
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna "' . $userName . '" berhasil dihapus!',
        ]);
    }

    public function toggleStatus(User $user)
    {
        $this->authorize('update', $user);

        $user->update(['is_active' => !$user->is_active]);

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => 'Status pengguna "' . $user->name . '" berhasil ' . $statusStr . '!',
            'data'    => $user,
        ]);
    }

}
