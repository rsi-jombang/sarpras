<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    // Tidak ada update; log bersifat immutable
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'item_stock_id',
        'item_id',
        'movement_type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference_type',
        'reference_id',
        'performed_by',
        'notes',
    ];

    protected $casts = [
        'movement_type' => StockMovementType::class,
        'quantity'      => 'integer',
        'stock_before'  => 'integer',
        'stock_after'   => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    public function itemStock(): BelongsTo
    {
        return $this->belongsTo(ItemStock::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Relasi polymorphic ke sumber pergerakan.
     * Bisa berupa: PurchaseOrder, Request, atau StockOpname.
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeIncoming($query)
    {
        return $query->whereIn('movement_type', [
            StockMovementType::In->value,
            StockMovementType::Return->value,
        ]);
    }

    public function scopeOutgoing($query)
    {
        return $query->whereIn('movement_type', [
            StockMovementType::Out->value,
            StockMovementType::Adjustment->value,
        ]);
    }

    public function scopeForItem($query, int $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }
}
