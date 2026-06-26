<?php

namespace App\Enums;

enum StockMovementType: string
{
    case In                 = 'in';
    case Out                = 'out';
    case Adjustment         = 'adjustment';
    case ManualAdjustment   = 'manual_adjustment';
    case Return             = 'return';

    public function label(): string
    {
        return match($this) {
            self::In                => 'Barang Masuk',
            self::Out               => 'Barang Keluar',
            self::Adjustment        => 'Penyesuaian Opname',
            self::ManualAdjustment  => 'Penyesuaian Manual',
            self::Return            => 'Pengembalian',
        };
    }

    public function isIncoming(): bool
    {
        return $this === self::In || $this === self::Return;
    }
}
