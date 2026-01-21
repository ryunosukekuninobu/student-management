<?php

namespace Calema\StudentManagement\Http\Controllers;

use Calema\StudentManagement\Models\StudentInvitation;
use Calema\StudentManagement\Services\StudentInvitationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class StudentInvitationController extends Controller
{
    protected StudentInvitationService $invitationService;

    public function __construct(StudentInvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    /**
     * Show invitation form
     */
    public function create()
    {
        $tenant = tenant();

        if (!$tenant) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'テナント情報が見つかりません。');
        }

        return view('student-management::students.invite', compact('tenant'));
    }

    /**
     * Send invitation
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
            'email' => 'required|email',
            'user_type' => 'required|in:student,guardian',
            'grade' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
        ]);

        try {
            $invitation = $this->invitationService->sendInvitation(
                $validated,
                $tenant->id,
                Auth::id()
            );

            return redirect()->route('students.index')
                ->with('success', "招待メールを {$invitation->email} に送信しました。");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', '招待の送信に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * Show invitation acceptance form
     */
    public function showAcceptForm(Request $request, string $token)
    {
        $invitation = StudentInvitation::where('token', $token)->pending()->first();

        if (!$invitation) {
            return redirect()->route('login')
                ->with('error', '招待が無効または期限切れです。');
        }

        return view('student-management::students.accept-invitation', compact('invitation'));
    }

    /**
     * Accept invitation and create account
     */
    public function accept(Request $request, string $token)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
        ]);

        try {
            $result = $this->invitationService->acceptInvitation($token, $validated);

            if (!$result) {
                return back()->with('error', '招待が無効または期限切れです。');
            }

            // Log in the new user
            Auth::login($result['profile']->user);

            return redirect()->route('tenant.dashboard')
                ->with('success', 'アカウントを作成しました。ようこそ！');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'アカウントの作成に失敗しました: ' . $e->getMessage());
        }
    }
}
