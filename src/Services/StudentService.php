<?php

namespace Calema\StudentManagement\Services;

use App\Models\User;
use Calema\StudentManagement\Models\StudentProfile;
use Calema\StudentManagement\Models\Guardian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentService
{
    /**
     * Create a new student with profile and optional guardians
     */
    public function createStudent(array $data, int $tenantId): StudentProfile
    {
        return DB::transaction(function () use ($data, $tenantId) {
            // Create User account
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? 'temporary123'),
                'user_type' => 'student',
                'phone' => $data['phone'] ?? null,
                'tenant_id' => $tenantId,
            ]);

            // Generate student number
            $studentNumber = StudentProfile::generateStudentNumber($tenantId);

            // Create Student Profile
            $profile = StudentProfile::create([
                'user_id' => $user->id,
                'tenant_id' => $tenantId,
                'student_number' => $studentNumber,
                'grade' => $data['grade'] ?? null,
                'school_name' => $data['school_name'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'emergency_contact_relationship' => $data['emergency_contact_relationship'] ?? null,
                'medical_notes' => $data['medical_notes'] ?? null,
                'blood_type' => $data['blood_type'] ?? null,
                'joined_date' => $data['joined_date'] ?? now(),
                'status' => 'active',
                'custom_fields' => $data['custom_fields'] ?? [],
                'notes' => $data['notes'] ?? null,
            ]);

            // Create Guardians if provided
            if (!empty($data['guardians'])) {
                foreach ($data['guardians'] as $guardianData) {
                    Guardian::create([
                        'student_profile_id' => $profile->id,
                        'tenant_id' => $tenantId,
                        'name' => $guardianData['name'],
                        'relationship' => $guardianData['relationship'],
                        'phone' => $guardianData['phone'],
                        'email' => $guardianData['email'] ?? null,
                        'postal_code' => $guardianData['postal_code'] ?? null,
                        'address' => $guardianData['address'] ?? null,
                        'is_primary_contact' => $guardianData['is_primary_contact'] ?? false,
                        'can_pickup' => $guardianData['can_pickup'] ?? true,
                        'notes' => $guardianData['notes'] ?? null,
                    ]);
                }
            }

            return $profile->load(['user', 'guardians']);
        });
    }

    /**
     * Update student profile
     */
    public function updateStudent(StudentProfile $profile, array $data): StudentProfile
    {
        return DB::transaction(function () use ($profile, $data) {
            // Update User
            $profile->user->update([
                'name' => $data['name'] ?? $profile->user->name,
                'email' => $data['email'] ?? $profile->user->email,
                'phone' => $data['phone'] ?? $profile->user->phone,
            ]);

            // Update Profile
            $profile->update([
                'grade' => $data['grade'] ?? $profile->grade,
                'school_name' => $data['school_name'] ?? $profile->school_name,
                'date_of_birth' => $data['date_of_birth'] ?? $profile->date_of_birth,
                'gender' => $data['gender'] ?? $profile->gender,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? $profile->emergency_contact_name,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? $profile->emergency_contact_phone,
                'emergency_contact_relationship' => $data['emergency_contact_relationship'] ?? $profile->emergency_contact_relationship,
                'medical_notes' => $data['medical_notes'] ?? $profile->medical_notes,
                'blood_type' => $data['blood_type'] ?? $profile->blood_type,
                'status' => $data['status'] ?? $profile->status,
                'custom_fields' => $data['custom_fields'] ?? $profile->custom_fields,
                'notes' => $data['notes'] ?? $profile->notes,
            ]);

            return $profile->fresh(['user', 'guardians']);
        });
    }

    /**
     * Add guardian to student
     */
    public function addGuardian(StudentProfile $profile, array $guardianData): Guardian
    {
        return Guardian::create([
            'student_profile_id' => $profile->id,
            'tenant_id' => $profile->tenant_id,
            'name' => $guardianData['name'],
            'relationship' => $guardianData['relationship'],
            'phone' => $guardianData['phone'],
            'email' => $guardianData['email'] ?? null,
            'postal_code' => $guardianData['postal_code'] ?? null,
            'address' => $guardianData['address'] ?? null,
            'is_primary_contact' => $guardianData['is_primary_contact'] ?? false,
            'can_pickup' => $guardianData['can_pickup'] ?? true,
            'notes' => $guardianData['notes'] ?? null,
        ]);
    }

    /**
     * Delete student (soft delete)
     */
    public function deleteStudent(StudentProfile $profile): bool
    {
        return DB::transaction(function () use ($profile) {
            // Soft delete guardians
            $profile->guardians()->delete();

            // Soft delete profile
            $profile->delete();

            // Optionally delete user account
            // $profile->user->delete();

            return true;
        });
    }
}
