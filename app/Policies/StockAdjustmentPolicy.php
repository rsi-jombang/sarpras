<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\StockAdjustment;
use App\Models\User;

class StockAdjustmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Admin || $user->role === UserRole::Sarpras;
    }

    public function view(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $user->role === UserRole::Admin || $user->role === UserRole::Sarpras;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Sarpras;
    }

    public function update(User $user, StockAdjustment $stockAdjustment): bool
    {
        return false;
    }

    public function delete(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function restore(User $user, StockAdjustment $stockAdjustment): bool
    {
        return false;
    }

    public function forceDelete(User $user, StockAdjustment $stockAdjustment): bool
    {
        return false;
    }
}
