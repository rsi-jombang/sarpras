<?php

namespace App\Models;

use App\Enums\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'po_number',
        'created_by',
        'approved_by',
        'supplier_name',
        'supplier_contact',
        'status',
        'order_date',
        'expected_date',
        'received_date',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'status'        => PurchaseOrderStatus::class,
        'order_date'    => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
        'total_amount'  => 'decimal:2',
    ];

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByStatus($query, PurchaseOrderStatus $status)
    {
        return $query->where('status', $status->value);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isEditable(): bool
    {
        return $this->status === PurchaseOrderStatus::Draft;
    }

    public function canBeReceived(): bool
    {
        return in_array($this->status, [
            PurchaseOrderStatus::Approved,
            PurchaseOrderStatus::Ordered,
        ]);
    }
}
