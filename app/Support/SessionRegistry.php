<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Session;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session as SessionFacade;

/**
 * Reads and revokes the rows of the database session driver.
 *
 * Every revocation is recorded in the activity log, since signing someone out
 * of a device is exactly the kind of action an audit trail exists for.
 */
final class SessionRegistry
{
    /**
     * Whether sessions can be listed at all, i.e. the database driver is in use.
     */
    public static function isSupported(): bool
    {
        return config('session.driver') === 'database';
    }

    /**
     * The sessions of one user, most recently active first.
     *
     * @return Collection<int, Session>
     */
    public static function forUser(User $user): Collection
    {
        return Session::query()
            ->forUser($user)
            ->orderByDesc('last_activity')
            ->get();
    }

    /**
     * Sign a single device out.
     */
    public static function revoke(Session $session): void
    {
        $subject = $session->user;

        $session->delete();

        activity('session')
            ->when($subject !== null, fn ($logger) => $logger->performedOn($subject))
            ->causedBy(Auth::user())
            ->withProperties([
                'ip_address' => $session->ip_address,
                'device' => $session->agent()->describe(),
            ])
            ->event('revoked')
            ->log('session.revoked');
    }

    /**
     * Sign a user out everywhere except the session making this request.
     *
     * @return int the number of sessions that were signed out
     */
    public static function revokeOthersFor(User $user): int
    {
        $revoked = Session::query()
            ->forUser($user)
            ->whereKeyNot(SessionFacade::getId())
            ->delete();

        if ($revoked > 0) {
            activity('session')
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->withProperties(['count' => $revoked])
                ->event('revoked_others')
                ->log('session.revoked_others');
        }

        return $revoked;
    }
}
