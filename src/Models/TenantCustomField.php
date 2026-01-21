<?php

namespace Calema\StudentManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantCustomField extends Model
{
    protected $fillable = [
        'tenant_id',
        'field_name',
        'field_label',
        'field_type',
        'field_options',
        'target_model',
        'is_required',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'field_options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the tenant that owns the custom field
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\Calema\MultiTenancy\Models\Tenant::class);
    }

    /**
     * Scope to only include active fields
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by target model
     */
    public function scopeForModel($query, $model)
    {
        return $query->where('target_model', $model);
    }

    /**
     * Scope to filter by tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
