<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReturn extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'return_number',
        'created_by',
        'unit_id',
        'status',
        'reason',
        'notes',
        'submitted_at',
        'received_by',
        'received_at',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'received_at'  => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /** Pembuat (unit) */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Unit asal pengembalian */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /** Sarpras yang menerima */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /** User yang membatalkan */
    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemReturnItem::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isReceived(): bool
    {
        return $this->status === 'received';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canReceive(): bool
    {
        return $this->status === 'submitted';
    }
}