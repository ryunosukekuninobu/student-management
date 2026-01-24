<?php

namespace Calema\StudentManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Calema\MultiTenancy\Models\Tenant;
use Calema\StudentManagement\Models\TenantCustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantSettingsController extends Controller
{
    /**
     * Display tenant settings for student management
     */
    public function index()
    {
        $user = Auth::user();

        // 生徒は生徒管理設定にアクセス不可
        if ($user->hasRole('student')) {
            abort(403, '生徒ユーザは生徒管理設定にアクセスできません。');
        }

        $tenant = Tenant::findOrFail(session('tenant_id'));

        // Get custom fields for this tenant
        $customFields = TenantCustomField::where('tenant_id', $tenant->id)
            ->where('entity_type', 'student')
            ->orderBy('display_order')
            ->get();

        return view('student-management::settings.index', compact('tenant', 'customFields'));
    }

    /**
     * Update tenant settings
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 生徒は生徒管理設定にアクセス不可
        if ($user->hasRole('student')) {
            abort(403, '生徒ユーザは生徒管理設定にアクセスできません。');
        }

        $validated = $request->validate([
            'requires_guardian_info' => 'required|boolean',
            'auto_generate_student_number' => 'nullable|boolean',
            'invitation_validity_days' => 'nullable|integer|min:1|max:30',
            'max_guardians_per_student' => 'nullable|integer|min:1|max:10',
        ]);

        $tenant = Tenant::findOrFail(session('tenant_id'));

        DB::beginTransaction();
        try {
            // Update guardian requirement
            $tenant->requires_guardian_info = $validated['requires_guardian_info'];

            // Update other settings in the settings JSON column
            $settings = $tenant->settings ?? [];
            $settings['student_management'] = [
                'auto_generate_student_number' => $validated['auto_generate_student_number'] ?? true,
                'invitation_validity_days' => $validated['invitation_validity_days'] ?? 7,
                'max_guardians_per_student' => $validated['max_guardians_per_student'] ?? 5,
            ];
            $tenant->settings = $settings;

            $tenant->save();

            DB::commit();

            return redirect()
                ->route('students.settings.index')
                ->with('success', '設定を更新しました');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => '設定の更新に失敗しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Store a new custom field
     */
    public function storeCustomField(Request $request)
    {
        $user = Auth::user();

        // 生徒は生徒管理設定にアクセス不可
        if ($user->hasRole('student')) {
            abort(403, '生徒ユーザは生徒管理設定にアクセスできません。');
        }

        $validated = $request->validate([
            'field_name' => 'required|string|max:100',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,textarea,number,date,select,checkbox,radio',
            'field_options' => 'nullable|json',
            'is_required' => 'nullable|boolean',
            'display_order' => 'nullable|integer',
        ]);

        $tenant = Tenant::findOrFail(session('tenant_id'));

        try {
            $customField = TenantCustomField::create([
                'tenant_id' => $tenant->id,
                'entity_type' => 'student',
                'field_name' => $validated['field_name'],
                'field_label' => $validated['field_label'],
                'field_type' => $validated['field_type'],
                'field_options' => $validated['field_options'] ? json_decode($validated['field_options'], true) : null,
                'is_required' => $validated['is_required'] ?? false,
                'display_order' => $validated['display_order'] ?? 0,
                'is_active' => true,
            ]);

            return redirect()
                ->route('students.settings.index')
                ->with('success', 'カスタムフィールドを追加しました');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'カスタムフィールドの追加に失敗しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Update custom field
     */
    public function updateCustomField(Request $request, TenantCustomField $customField)
    {
        $user = Auth::user();

        // 生徒は生徒管理設定にアクセス不可
        if ($user->hasRole('student')) {
            abort(403, '生徒ユーザは生徒管理設定にアクセスできません。');
        }

        $validated = $request->validate([
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,textarea,number,date,select,checkbox,radio',
            'field_options' => 'nullable|json',
            'is_required' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'display_order' => 'nullable|integer',
        ]);

        $tenant = Tenant::findOrFail(session('tenant_id'));

        // Ensure the custom field belongs to this tenant
        if ($customField->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access to custom field');
        }

        try {
            $customField->update([
                'field_label' => $validated['field_label'],
                'field_type' => $validated['field_type'],
                'field_options' => $validated['field_options'] ? json_decode($validated['field_options'], true) : null,
                'is_required' => $validated['is_required'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
                'display_order' => $validated['display_order'] ?? $customField->display_order,
            ]);

            return redirect()
                ->route('students.settings.index')
                ->with('success', 'カスタムフィールドを更新しました');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'カスタムフィールドの更新に失敗しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete custom field
     */
    public function destroyCustomField(TenantCustomField $customField)
    {
        $user = Auth::user();

        // 生徒は生徒管理設定にアクセス不可
        if ($user->hasRole('student')) {
            abort(403, '生徒ユーザは生徒管理設定にアクセスできません。');
        }

        $tenant = Tenant::findOrFail(session('tenant_id'));

        // Ensure the custom field belongs to this tenant
        if ($customField->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access to custom field');
        }

        try {
            $customField->delete();

            return redirect()
                ->route('students.settings.index')
                ->with('success', 'カスタムフィールドを削除しました');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'カスタムフィールドの削除に失敗しました: ' . $e->getMessage()]);
        }
    }
}
