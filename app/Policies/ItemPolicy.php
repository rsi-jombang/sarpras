<?php

namespace App\Policies;

use App\Models\User;

class ItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSarpras() || $user->isAdmin();
    }

    public function view(User $user): bool
    {
        return $user->isSarpras() || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSarpras();
    }

    public function update(User $user): bool
    {
        return $user->isAdmin() || $user->isSarpras();
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin() || $user->isSarpras();
    }
}
