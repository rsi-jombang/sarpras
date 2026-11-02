<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ItemReturn;
use App\Models\User;

class ReturnPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ItemReturn $r): bool
    {
        if ($user->isSarpras() || $user->isAdmin()) return true;
        return $r->created_by === $user->id || (int) $r->unit_id === (int) $user->unit_id;
    }

    public function create(User $user): bool
    {
        return $user->isUnit();
    }

    public function update(User $user, ItemReturn $r): bool
    {
        if (!$user->isUnit()) return false;
        if ($r->created_by !== $user->id) return false;
        return in_array($r->status, ['draft', 'submitted']);
    }

    public function submit(User $user, ItemReturn $r): bool
    {
        if (!$user->isUnit()) return false;
        return $r->created_by === $user->id && $r->status === 'draft';
    }

    public function receive(User $user, ItemReturn $r): bool
    {
        return ($user->isSarpras() || $user->isAdmin()) && $r->status === 'submitted';
    }

    public function cancel(User $user, ItemReturn $r): bool
    {
        if ($user->isUnit()) {
            return $r->created_by === $user->id && in_array($r->status, ['draft', 'submitted']);
        }
        return $user->isSarpras() || $user->isAdmin();
    }

    public function delete(User $user, ItemReturn $r): bool
    {
        return $user->isAdmin() && $r->status === 'draft';
    }
}