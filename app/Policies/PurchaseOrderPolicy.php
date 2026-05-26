<?php

namespace App\Policies;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSarpras() || $user->isAdmin();
    }

    public function view(User $user, PurchaseOrder $po): bool
    {
        return $user->isSarpras() || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->canCreatePO();
    }

    public function update(User $user, PurchaseOrder $po): bool
    {
        return $user->canCreatePO() && $po->isEditable();
    }

    public function approve(User $user, PurchaseOrder $po): bool
    {
        return $user->isAdmin() && $po->status === PurchaseOrderStatus::Draft;
    }

    public function receive(User $user, PurchaseOrder $po): bool
    {
        return $user->canCreatePO() && $po->canBeReceived();
    }

    public function cancel(User $user, PurchaseOrder $po): bool
    {
        return $user->isAdmin()
            && !in_array($po->status, [
                PurchaseOrderStatus::Received,
                PurchaseOrderStatus::Cancelled,
            ]);
    }

    public function delete(User $user, PurchaseOrder $po): bool
    {
        return $user->isAdmin() && $po->status === PurchaseOrderStatus::Draft;
    }
}
