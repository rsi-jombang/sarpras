<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'username',
    'email',
    'password',
    'employee_id',
    'phone',
    'is_active',
    'unit_id',
    'role'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }


    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /** Permintaan yang dibuat oleh user ini */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class, 'requested_by');
    }

    /** Permintaan yang di-review oleh user ini (sarpras) */
    public function reviewedRequests(): HasMany
    {
        return $this->hasMany(Request::class, 'reviewed_by');
    }

    /** Permintaan yang dilayani oleh user ini (sarpras) */
    public function servedRequests(): HasMany
    {
        return $this->hasMany(Request::class, 'served_by');
    }

    public function itemReturns(): HasMany
    {
        return $this->hasMany(ItemReturn::class, 'created_by');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'performed_by');
    }

    public function stockOpnames(): HasMany
    {
        return $this->hasMany(StockOpname::class, 'created_by');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isSarpras(): bool
    {
        return $this->role === UserRole::Sarpras;
    }

    public function isUnit(): bool
    {
        return $this->role === UserRole::Unit;
    }

    public function canApproveRequest(): bool
    {
        return $this->role->canApproveRequest();
    }

    public function canCreatePO(): bool
    {
        return $this->role->canCreatePO();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, UserRole $role)
    {
        return $query->where('role', $role->value);
    }
}
