<?php

namespace App\Enums;

enum RequestStatus: string
{
    case Draft     = 'draft';
    case Pending   = 'pending';
    case Approved  = 'approved';
    case Partial   = 'partial';
    case Rejected  = 'rejected';
    case Ready     = 'ready';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft     => 'Draft',
            self::Pending   => 'Menunggu Review',
            self::Approved  => 'Disetujui',
            self::Partial   => 'Disetujui Sebagian',
            self::Rejected  => 'Ditolak',
            self::Ready     => 'Siap Diambil',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft     => 'secondary',
            self::Pending   => 'warning',
            self::Approved  => 'info',
            self::Partial   => 'info',
            self::Rejected  => 'danger',
            self::Ready     => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'secondary',
        };
    }

    /** Status yang bisa difilter oleh sarpras */
    public static function sarprasFilters(): array
    {
        return [self::Pending, self::Approved, self::Partial, self::Ready, self::Completed, self::Rejected];
    }

    /** Status yang masih bisa dibatalkan pengaju */
    public function canBeCancelledByRequester(): bool
    {
        return $this === self::Pending || $this === self::Draft;
    }
}
