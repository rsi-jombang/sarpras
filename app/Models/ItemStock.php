<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemStock extends Model
{
    protected $fillable = [
        'item_id',
        'purchase_price',
        'selling_price',
        'quantity',
        'minimum_stock',
        'batch_number',
        'expired_date',
        'notes',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price'  => 'decimal:2',
        'expired_date'   => 'date',
    ];

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function requestItems(): HasMany
    {
        return $this->hasMany(RequestItem::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function stockOpnameItems(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getIsLowStockAttribute(): bool
    {
        return $this->minimum_stock > 0 && $this->quantity <= $this->minimum_stock;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expired_date !== null && $this->expired_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expired_date !== null
            && !$this->is_expired
            && $this->expired_date->diffInDays(now()) <= 30;
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeAvailable($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /** FIFO: batch terlama dulu */
    public function scopeFifo($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'minimum_stock')
                     ->where('minimum_stock', '>', 0);
    }
}
