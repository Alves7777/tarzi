<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Records authentication events in the activity log.
 *
 * No activity log package covers these — they log model changes, not who
 * signed in — so an audit trail has to add them itself.
 */
class LogAuthenticationEvents
{
    public function __construct(private readonly Request $request) {}

    public function handleLogin(Login $event): void
    {
        $this->record('login', $event->user, ['guard' => $event->guard]);
    }

    public function handleLogout(Logout $event): void
    {
        $this->record('logout', $event->user, ['guard' => $event->guard]);
    }

    /**
     * A failed attempt names the account that was targeted, never the password.
     */
    public function handleFailed(Failed $event): void
    {
        $this->record('login_failed', $event->user, [
            'guard' => $event->guard,
            'email' => $event->credentials['email'] ?? null,
        ]);
    }

    public function handleLockout(Lockout $event): void
    {
        $this->record('lockout', null, [
            'email' => $event->request->input('email'),
        ]);
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        $this->record('password_reset', $event->user);
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    private function record(string $event, mixed $user, array $properties = []): void
    {
        $logger = activity('auth')
            ->event($event)
            ->withProperties(array_filter([
                ...$properties,
                'ip_address' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
            ], fn (mixed $value): bool => $value !== null));

        if ($user instanceof Model) {
            $logger->performedOn($user)->causedBy($user);
        }

        $logger->log("auth.{$event}");
    }
}
