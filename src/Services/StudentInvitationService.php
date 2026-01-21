<?php

namespace Calema\StudentManagement\Services;

use Calema\StudentManagement\Models\StudentInvitation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class StudentInvitationService
{
    /**
     * Create and send invitation
     */
    public function sendInvitation(array $data, int $tenantId, int $invitedBy): StudentInvitation
    {
        $invitation = StudentInvitation::create([
            'tenant_id' => $tenantId,
            'invited_by' => $invitedBy,
            'email' => $data['email'],
            'name' => $data['name'],
            'user_type' => $data['user_type'] ?? 'student',
            'token' => StudentInvitation::generateToken(),
            'expires_at' => now()->addDays(7),
            'status' => 'pending',
            'metadata' => $data['metadata'] ?? [],
        ]);

        // Send invitation email
        $this->sendInvitationEmail($invitation);

        return $invitation;
    }

    /**
     * Send invitation email
     */
    protected function sendInvitationEmail(StudentInvitation $invitation): void
    {
        $invitationUrl = $this->generateInvitationUrl($invitation);

        // TODO: Implement actual email sending
        // Mail::to($invitation->email)->send(new StudentInvitationMail($invitation, $invitationUrl));

        \Log::info('Invitation email sent', [
            'email' => $invitation->email,
            'url' => $invitationUrl,
        ]);
    }

    /**
     * Generate invitation URL
     */
    public function generateInvitationUrl(StudentInvitation $invitation): string
    {
        return URL::temporarySignedRoute(
            'students.invitation.accept',
            $invitation->expires_at,
            ['token' => $invitation->token]
        );
    }

    /**
     * Accept invitation and create user
     */
    public function acceptInvitation(string $token, array $userData): ?array
    {
        $invitation = StudentInvitation::where('token', $token)
            ->pending()
            ->first();

        if (!$invitation) {
            return null;
        }

        $studentService = new StudentService();

        $profile = $studentService->createStudent([
            'name' => $invitation->name,
            'email' => $invitation->email,
            'password' => $userData['password'],
            'phone' => $userData['phone'] ?? null,
            'date_of_birth' => $userData['date_of_birth'] ?? null,
            'gender' => $userData['gender'] ?? null,
            ...$invitation->metadata,
        ], $invitation->tenant_id);

        $invitation->markAsAccepted();

        return [
            'profile' => $profile,
            'invitation' => $invitation,
        ];
    }

    /**
     * Resend invitation
     */
    public function resendInvitation(StudentInvitation $invitation): void
    {
        if (!$invitation->isPending()) {
            throw new \Exception('招待状態が無効です。');
        }

        $invitation->update([
            'expires_at' => now()->addDays(7),
        ]);

        $this->sendInvitationEmail($invitation);
    }

    /**
     * Cancel invitation
     */
    public function cancelInvitation(StudentInvitation $invitation): void
    {
        $invitation->update(['status' => 'cancelled']);
    }
}
