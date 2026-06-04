<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    protected $fillable = [
        'stock_opname_id',
        'item_stock_id',
        'item_id',
        'system_quantity',
        'physical_quantity',
        'difference',
        'is_adjusted',
        'notes',
    ];

    protected $casts = [
        'system_quantity'   => 'integer',
        'physical_quantity' => 'integer',
        'difference'        => 'integer',
        'is_adjusted'       => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function itemStock(): BelongsTo
    {
        return $this->belongsTo(ItemStock::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    // -------------------------------------------------------------------------
    // Mutator: otomatis hitung difference saat physical_quantity diisi
    // -------------------------------------------------------------------------

    // public function setPhysicalQuantityAttribute(int $value): void
    // {
    //     $this->attributes['physical_quantity'] = $value;
    //     $this->attributes['difference']        = $value - ($this->system_quantity ?? 0);
    // }

    public function setPhysicalQuantityAttribute(?int $value): void
    {
        $this->attributes['physical_quantity'] = $value;

        // Hanya hitung jika physical_quantity diisi
        if ($value !== null) {
            $this->attributes['difference'] = $value - ($this->system_quantity ?? 0);
        } else {
            // Jika null, set difference juga null
            $this->attributes['difference'] = null;
        }
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getHasDifferenceAttribute(): bool
    {
        return $this->difference !== null && $this->difference !== 0;
    }
}
