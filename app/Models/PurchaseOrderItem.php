<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'item_stock_id',
        'quantity_ordered',
        'quantity_received',
        'unit_price',
        'batch_number',
        'expired_date',
        'notes',
    ];

    protected $casts = [
        'unit_price'   => 'decimal:2',
        'expired_date' => 'date',
    ];

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Batch stok yang dibuat saat PO diterima.
     * Null selama PO belum berstatus 'received'.
     */
    public function itemStock(): BelongsTo
    {
        return $this->belongsTo(ItemStock::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getSubtotalAttribute(): float
    {
        return $this->quantity_ordered * $this->unit_price;
    }
}
