<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthenticationActivity
{
    /**
     * Handle user login.
     */
    public function handleLogin(Login $event): void
    {
        if ($event->user instanceof User) {
            ActivityLog::record(
                action: 'LOGIN',
                module: 'Auth',
                description: "Pengguna {$event->user->name} berhasil login",
                subject: $event->user,
                user: $event->user
            );
        }
    }

    /**
     * Handle user logout.
     */
    public function handleLogout(Logout $event): void
    {
        if ($event->user instanceof User) {
            ActivityLog::record(
                action: 'LOGOUT',
                module: 'Auth',
                description: "Pengguna {$event->user->name} logout dari sistem",
                subject: $event->user,
                user: $event->user
            );
        }
    }

    /**
     * Handle failed login attempt.
     */
    public function handleFailed(Failed $event): void
    {
        $email = $event->credentials['email'] ?? $event->credentials['nip'] ?? 'unknown';

        ActivityLog::record(
            action: 'LOGIN_FAILED',
            module: 'Auth',
            description: "Percobaan login gagal untuk akun/identitas: {$email}",
            properties: [
                'identifier' => $email,
            ]
        );
    }
}
