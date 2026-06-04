<?php

namespace App\Policies;

use App\Models\StockOpname;
use App\Models\User;

class StockOpnamePolicy
{
    public function viewAny(User $user): bool { return $user->isSarpras() || $user->isAdmin(); }
    public function view(User $user, StockOpname $opname): bool { return $user->isSarpras() || $user->isAdmin(); }
    public function create(User $user): bool { return $user->isSarpras() || $user->isAdmin(); }
    public function update(User $user, StockOpname $opname): bool
    {
        return ($user->isSarpras() || $user->isAdmin()) && $opname->isOpen();
    }
    public function close(User $user, StockOpname $opname): bool
    {
        return ($user->isSarpras() || $user->isAdmin()) && $opname->isOpen();
    }
    public function delete(User $user, StockOpname $opname): bool
    {
        return $user->isAdmin() && $opname->status === 'open';
    }
}
