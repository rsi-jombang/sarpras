<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestItem extends Model
{
    protected $fillable = [
        'request_id',
        'item_id',
        'item_stock_id',
        'quantity_requested',
        'quantity_approved',
        'quantity_given',
        'status',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'quantity_requested' => 'integer',
        'quantity_approved'  => 'integer',
        'quantity_given'     => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Batch stok dari mana barang diambil.
     * Diisi oleh sarpras saat menyiapkan barang (FIFO).
     */
    public function itemStock(): BelongsTo
    {
        return $this->belongsTo(ItemStock::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getIsFullyApprovedAttribute(): bool
    {
        return $this->quantity_approved !== null
            && $this->quantity_approved === $this->quantity_requested;
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }
}
