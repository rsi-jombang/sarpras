<?php

namespace App\Policies;

use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\Request;
use App\Models\User;

class RequestPolicy
{
    /** Semua role bisa melihat list, tapi scope-nya dibatasi di controller */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** Unit hanya bisa lihat milik unitnya; sarpras & admin bisa semua */
    public function view(User $user, Request $request): bool
    {
        if ($user->isSarpras() || $user->isAdmin()) return true;

        return $request->unit_id === $user->unit_id;
    }

    /** Hanya role unit yang bisa membuat permintaan */
    public function create(User $user): bool
    {
        return $user->isUnit();
    }

    /** Hanya bisa edit jika masih pending dan milik unit sendiri */
    public function update(User $user, Request $request): bool
    {
        if (!$user->isUnit()) return false;

        return $request->unit_id === $user->unit_id
            && $request->status === RequestStatus::Pending;
    }

    /** Hanya sarpras/admin yang bisa approve atau reject */
    public function review(User $user, Request $request): bool
    {
        if (!$user->canApproveRequest()) return false;

        return $request->status === RequestStatus::Pending;
    }

    /** Sarpras menandai barang sudah siap diambil */
    public function fulfill(User $user, Request $request): bool
    {
        if (!$user->canApproveRequest()) return false;

        return in_array($request->status, [
            RequestStatus::Approved,
            RequestStatus::Partial,
        ]);
    }

    /** Sarpras konfirmasi pengambilan barang */
    public function complete(User $user, Request $request): bool
    {
        if (!$user->canApproveRequest()) return false;

        return $request->status === RequestStatus::Ready;
    }

    /** Unit hanya bisa batalkan milik sendiri yang masih pending */
    public function cancel(User $user, Request $request): bool
    {
        if (!$user->isUnit()) return false;

        return $request->unit_id === $user->unit_id
            && $request->status->canBeCancelledByRequester();
    }

    /** Hanya admin yang bisa hapus */
    public function delete(User $user, Request $request): bool
    {
        return $user->isAdmin();
    }
}
