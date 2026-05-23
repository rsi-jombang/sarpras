<?php

namespace App\Enums;

enum ItemType: string
{
    case Consumable = 'consumable';
    case Inventory  = 'inventory';

    public function label(): string
    {
        return match($this) {
            self::Consumable => 'Barang Habis Pakai',
            self::Inventory  => 'Inventaris',
        };
    }
}
