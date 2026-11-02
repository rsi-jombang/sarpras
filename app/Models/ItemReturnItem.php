<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemReturnItem extends Model
{
    protected $fillable = [
        'item_return_id',
        'request_item_id',
        'item_id',
        'item_stock_id',
        'quantity_returned',
        'condition',
        'quantity_accepted',
        'notes',
    ];

    protected $casts = [
        'quantity_returned' => 'integer',
        'quantity_accepted' => 'integer',
    ];

    public function itemReturn(): BelongsTo
    {
        return $this->belongsTo(ItemReturn::class);
    }

    /** Permintaan asal barang (nullable untuk retur manual) */
    public function requestItem(): BelongsTo
    {
        return $this->belongsTo(RequestItem::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /** Batch stok yang ditambahkan saat penerimaan */
    public function itemStock(): BelongsTo
    {
        return $this->belongsTo(ItemStock::class);
    }

    /** Jumlah yang bisa diterima ke stok = quantity_returned jika usable */
    public function getAcceptedQuantityAttribute(): int
    {
        return $this->condition === 'usable' ? (int) $this->quantity_returned : 0;
    }
}