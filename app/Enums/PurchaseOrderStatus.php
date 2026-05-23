<?php

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case Draft     = 'draft';
    case Approved  = 'approved';
    case Ordered   = 'ordered';
    case Received  = 'received';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft     => 'Draft',
            self::Approved  => 'Disetujui',
            self::Ordered   => 'Dipesan',
            self::Received  => 'Diterima',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft     => 'secondary',
            self::Approved  => 'info',
            self::Ordered   => 'warning',
            self::Received  => 'success',
            self::Cancelled => 'danger',
        };
    }
}
