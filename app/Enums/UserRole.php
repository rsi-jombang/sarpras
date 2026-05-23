<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin    = 'admin';
    case Sarpras  = 'sarpras';
    case Unit     = 'unit';

    public function label(): string
    {
        return match($this) {
            self::Admin   => 'Administrator',
            self::Sarpras => 'Sarana Prasarana',
            self::Unit    => 'Unit / Departemen',
        };
    }

    public function canApproveRequest(): bool
    {
        return $this === self::Sarpras || $this === self::Admin;
    }

    public function canCreatePO(): bool
    {
        return $this === self::Sarpras || $this === self::Admin;
    }
}
