<?php

namespace Calema\StudentManagement\Http\Controllers;

use Calema\StudentManagement\Models\StudentProfile;
use Calema\StudentManagement\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    protected StudentService $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * Display a listing of students
     */
    public function index(Request $request)
    {
        $tenant = tenant();

        if (!$tenant) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'テナント情報が見つかりません。');
        }

        $query = StudentProfile::forTenant($tenant->id)
            ->with(['user', 'guardians', 'enrollments.course']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('student_number', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('student-management::students.index', compact('students', 'tenant'));
    }

    /**
     * Show the form for creating a new student
     */
    public function create()
    {
        $tenant = tenant();

        if (!$tenant) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'テナント情報が見つかりません。');
        }

        return view('student-management::students.create', compact('tenant'));
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request)
    {
        $tenant = tenant();

        if (!$tenant) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'テナント情報が見つかりません。');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'grade' => 'nullable|string|max:50',
            'school_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:50',
            'medical_notes' => 'nullable|string',
            'blood_type' => 'nullable|string|max:10',
            'joined_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'guardians' => 'nullable|array',
            'guardians.*.name' => 'required|string|max:255',
            'guardians.*.relationship' => 'required|string',
            'guardians.*.phone' => 'required|string|max:20',
            'guardians.*.email' => 'nullable|email',
            'guardians.*.address' => 'nullable|string',
            'guardians.*.postal_code' => 'nullable|string|max:10',
            'guardians.*.is_primary_contact' => 'boolean',
        ]);

        try {
            $profile = $this->studentService->createStudent($validated, $tenant->id);

            return redirect()->route('students.show', $profile)
                ->with('success', "生徒「{$profile->user->name}」を登録しました。");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', '生徒の登録に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified student
     */
    public function show(StudentProfile $student)
    {
        $tenant = tenant();

        if (!$tenant || $student->tenant_id !== $tenant->id) {
            abort(403, 'アクセス権限がありません。');
        }

        $student->load(['user', 'guardians', 'enrollments.course']);

        return view('student-management::students.show', compact('student', 'tenant'));
    }

    /**
     * Show the form for editing the specified student
     */
    public function edit(StudentProfile $student)
    {
        $tenant = tenant();

        if (!$tenant || $student->tenant_id !== $tenant->id) {
            abort(403, 'アクセス権限がありません。');
        }

        $student->load(['user', 'guardians']);

        return view('student-management::students.edit', compact('student', 'tenant'));
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, StudentProfile $student)
    {
        $tenant = tenant();

        if (!$tenant || $student->tenant_id !== $tenant->id) {
            abort(403, 'アクセス権限がありません。');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'phone' => 'nullable|string|max:20',
            'grade' => 'nullable|string|max:50',
            'school_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:50',
            'medical_notes' => 'nullable|string',
            'blood_type' => 'nullable|string|max:10',
            'status' => 'required|in:active,inactive,graduated,withdrawn',
            'notes' => 'nullable|string',
        ]);

        try {
            $profile = $this->studentService->updateStudent($student, $validated);

            return redirect()->route('students.show', $profile)
                ->with('success', '生徒情報を更新しました。');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', '生徒情報の更新に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified student
     */
    public function destroy(StudentProfile $student)
    {
        $tenant = tenant();

        if (!$tenant || $student->tenant_id !== $tenant->id) {
            abort(403, 'アクセス権限がありません。');
        }

        try {
            $this->studentService->deleteStudent($student);

            return redirect()->route('students.index')
                ->with('success', '生徒を削除しました。');
        } catch (\Exception $e) {
            return back()->with('error', '生徒の削除に失敗しました: ' . $e->getMessage());
        }
    }
}
