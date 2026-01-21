<?php

namespace Calema\StudentManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_profile_id',
        'tenant_id',
        'name',
        'relationship',
        'phone',
        'email',
        'postal_code',
        'address',
        'is_primary_contact',
        'can_pickup',
        'notes',
    ];

    protected $casts = [
        'is_primary_contact' => 'boolean',
        'can_pickup' => 'boolean',
    ];

    /**
     * Get the student profile that owns the guardian
     */
    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    /**
     * Get the tenant that owns the guardian
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\Calema\MultiTenancy\Models\Tenant::class);
    }

    /**
     * Get relationship display name
     */
    public function getRelationshipDisplayAttribute(): string
    {
        return match($this->relationship) {
            'father' => '父親',
            'mother' => '母親',
            'grandfather' => '祖父',
            'grandmother' => '祖母',
            'other' => 'その他',
            default => $this->relationship,
        };
    }
}
