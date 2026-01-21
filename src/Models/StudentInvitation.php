<?php

namespace Calema\StudentManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class StudentInvitation extends Model
{
    protected $fillable = [
        'tenant_id',
        'invited_by',
        'email',
        'name',
        'user_type',
        'token',
        'expires_at',
        'accepted_at',
        'status',
        'metadata',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the tenant that owns the invitation
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\Calema\MultiTenancy\Models\Tenant::class);
    }

    /**
     * Get the user who sent the invitation
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'invited_by');
    }

    /**
     * Check if invitation is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if invitation is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    /**
     * Mark invitation as accepted
     */
    public function markAsAccepted(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    /**
     * Generate invitation token
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Scope to only include pending invitations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '>', now());
    }

    /**
     * Scope to filter by tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
