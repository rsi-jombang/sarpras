<?php

namespace App\Models;

use App\Enums\ItemType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'item_type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'item_type' => ItemType::class,
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeConsumable($query)
    {
        return $query->where('item_type', ItemType::Consumable->value);
    }

    public function scopeInventory($query)
    {
        return $query->where('item_type', ItemType::Inventory->value);
    }
}
