<?php

namespace Calema\StudentManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'student_number',
        'grade',
        'school_name',
        'date_of_birth',
        'gender',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'medical_notes',
        'blood_type',
        'joined_date',
        'withdrawal_date',
        'status',
        'custom_fields',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joined_date' => 'date',
        'withdrawal_date' => 'date',
        'custom_fields' => 'array',
    ];

    /**
     * Get the user that owns the student profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the tenant that owns the student profile
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\Calema\MultiTenancy\Models\Tenant::class);
    }

    /**
     * Get the guardians for the student
     */
    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class);
    }

    /**
     * Get the primary guardian
     */
    public function primaryGuardian()
    {
        return $this->guardians()->where('is_primary_contact', true)->first();
    }

    /**
     * Get enrollments for this student
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(\Calema\ClassManagement\Models\ClassEnrollment::class, 'user_id', 'user_id');
    }

    /**
     * Scope to only include active students
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Get age from date of birth
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }

        return $this->date_of_birth->age;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'active' => '在籍中',
            'inactive' => '休会中',
            'graduated' => '卒業',
            'withdrawn' => '退会',
            default => $this->status,
        };
    }

    /**
     * Generate unique student number
     */
    public static function generateStudentNumber($tenantId): string
    {
        $prefix = now()->format('Ym');
        $lastStudent = self::where('tenant_id', $tenantId)
            ->where('student_number', 'like', $prefix . '%')
            ->orderBy('student_number', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->student_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
