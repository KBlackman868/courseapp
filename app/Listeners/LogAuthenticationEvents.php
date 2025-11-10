<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;

class LogAuthenticationEvents
{
    public function handleLogin(Login $event)
    {
        ActivityLogger::logAuth('login', "User logged in: {$event->user->email}", [
            'remember' => $event->remember ?? false,
        ]);
    }

    public function handleLogout(Logout $event)
    {
        if ($event->user) {
            ActivityLogger::logAuth('logout', "User logged out: {$event->user->email}");
        }
    }

    public function handleFailed(Failed $event)
    {
        ActivityLogger::logAuth('failed', "Failed login attempt", [
            'email' => $event->credentials['email'] ?? 'unknown',
        ], 'failed', 'warning');
    }

    public function handleRegistered(Registered $event)
    {
        ActivityLogger::logAuth('registered', "New user registered: {$event->user->email}", [
            'user_id' => $event->user->id,
        ]);
    }

    public function handleVerified(Verified $event)
    {
        ActivityLogger::logAuth('verified', "Email verified: {$event->user->email}");
    }

    public function subscribe($events)
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
            Registered::class => 'handleRegistered',
            Verified::class => 'handleVerified',
        ];
    }
}