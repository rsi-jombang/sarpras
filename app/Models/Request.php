<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_number',
        'requested_by',
        'unit_id',
        'status',
        'priority',
        'request_date',
        'needed_date',
        'purpose',
        'notes',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'served_by',
        'served_at',
        'received_by_name',
        'received_at',
    ];

    protected $casts = [
        'status'       => RequestStatus::class,
        'request_date' => 'date',
        'needed_date'  => 'date',
        'reviewed_at'  => 'datetime',
        'served_at'    => 'datetime',
        'received_at'  => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    /** User yang mengajukan permintaan */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /** Unit asal permintaan */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /** Petugas sarpras yang mereview (approve/reject) */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /** Petugas sarpras yang menyiapkan & menyerahkan barang */
    public function server(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RequestItem::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByStatus($query, RequestStatus $status)
    {
        return $query->where('status', $status->value);
    }

    public function scopePending($query)
    {
        return $query->where('status', RequestStatus::Pending->value);
    }

    public function scopeForUnit($query, int $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isPending(): bool
    {
        return $this->status === RequestStatus::Pending;
    }

    public function isCompleted(): bool
    {
        return $this->status === RequestStatus::Completed;
    }

    public function canBeCancelledByRequester(): bool
    {
        return $this->status->canBeCancelledByRequester();
    }
}
