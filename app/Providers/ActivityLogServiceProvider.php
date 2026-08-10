<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\LogAccessControlChanges;
use App\Listeners\LogAuthenticationEvents;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Events\PermissionAttachedEvent;
use Spatie\Permission\Events\PermissionDetachedEvent;
use Spatie\Permission\Events\RoleAttachedEvent;
use Spatie\Permission\Events\RoleDetachedEvent;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Wires everything the activity log records beyond model attribute changes.
 *
 * Models we own use the `LogsActivity` trait directly; roles and permissions
 * live in the vendor namespace, so their events are registered here instead.
 */
class ActivityLogServiceProvider extends ServiceProvider
{
    /**
     * Vendor models audited through Eloquent events rather than a trait.
     *
     * @var array<int, class-string<Model>>
     */
    private const AUDITED_MODELS = [
        Role::class,
        Permission::class,
    ];

    public function boot(): void
    {
        $this->registerAuthenticationListeners();
        $this->registerAccessControlListeners();
        $this->registerVendorModelListeners();
    }

    private function registerAuthenticationListeners(): void
    {
        Event::listen(Login::class, [LogAuthenticationEvents::class, 'handleLogin']);
        Event::listen(Logout::class, [LogAuthenticationEvents::class, 'handleLogout']);
        Event::listen(Failed::class, [LogAuthenticationEvents::class, 'handleFailed']);
        Event::listen(Lockout::class, [LogAuthenticationEvents::class, 'handleLockout']);
        Event::listen(PasswordReset::class, [LogAuthenticationEvents::class, 'handlePasswordReset']);
    }

    private function registerAccessControlListeners(): void
    {
        Event::listen(RoleAttachedEvent::class, [LogAccessControlChanges::class, 'handleRoleAttached']);
        Event::listen(RoleDetachedEvent::class, [LogAccessControlChanges::class, 'handleRoleDetached']);
        Event::listen(PermissionAttachedEvent::class, [LogAccessControlChanges::class, 'handlePermissionAttached']);
        Event::listen(PermissionDetachedEvent::class, [LogAccessControlChanges::class, 'handlePermissionDetached']);
    }

    private function registerVendorModelListeners(): void
    {
        foreach (self::AUDITED_MODELS as $model) {
            foreach (['created', 'updated', 'deleted'] as $event) {
                $model::{$event}(fn (Model $record) => $this->recordModelEvent($event, $record));
            }
        }
    }

    private function recordModelEvent(string $event, Model $record): void
    {
        activity(class_basename($record))
            ->performedOn($record)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => $record->getAttributes(),
                'old' => $event === 'updated' ? $record->getOriginal() : [],
            ])
            ->event($event)
            ->log($event);
    }
}
