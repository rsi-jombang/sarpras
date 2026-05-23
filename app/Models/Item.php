<?php

namespace App\Models;

use App\Enums\ItemType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'code',
        'unit_of_measure',
        'item_type',
        'description',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'item_type' => ItemType::class,
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ItemStock::class);
    }

    /** Stok yang masih tersedia (quantity > 0), urut FIFO */
    public function availableStocks(): HasMany
    {
        return $this->hasMany(ItemStock::class)
            ->where('quantity', '>', 0)
            ->orderBy('created_at', 'asc');
    }

    public function requestItems(): HasMany
    {
        return $this->hasMany(RequestItem::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /** Total seluruh stok dari semua batch */
    public function getTotalStockAttribute(): int
    {
        return $this->stocks()->sum('quantity');
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stocks()
            ->whereColumn('quantity', '<=', 'minimum_stock')
            ->where('minimum_stock', '>', 0)
            ->exists();
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

    public function scopeWithTotalStock($query)
    {
        return $query->withSum('stocks as total_stock', 'quantity');
    }
}
