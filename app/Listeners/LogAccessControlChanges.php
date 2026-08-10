<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Events\PermissionAttachedEvent;
use Spatie\Permission\Events\PermissionDetachedEvent;
use Spatie\Permission\Events\RoleAttachedEvent;
use Spatie\Permission\Events\RoleDetachedEvent;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Records role and permission grants in the activity log.
 *
 * These live in pivot tables, so Eloquent model events never see them; the
 * package's own events are the only hook, and they are off by default
 * (`permission.events_enabled`).
 */
class LogAccessControlChanges
{
    public function handleRoleAttached(RoleAttachedEvent $event): void
    {
        $this->record('role_attached', $event->model, 'roles', $this->resolveRoles($event->rolesOrIds));
    }

    public function handleRoleDetached(RoleDetachedEvent $event): void
    {
        $this->record('role_detached', $event->model, 'roles', $this->resolveRoles($event->rolesOrIds));
    }

    public function handlePermissionAttached(PermissionAttachedEvent $event): void
    {
        $this->record('permission_attached', $event->model, 'permissions', $this->resolvePermissions($event->permissionsOrIds));
    }

    public function handlePermissionDetached(PermissionDetachedEvent $event): void
    {
        $this->record('permission_detached', $event->model, 'permissions', $this->resolvePermissions($event->permissionsOrIds));
    }

    /**
     * @param  array<int, string>  $names
     */
    private function record(string $event, Model $subject, string $key, array $names): void
    {
        if ($names === []) {
            return;
        }

        activity('access-control')
            ->performedOn($subject)
            ->causedBy(Auth::user())
            ->withProperties([$key => $names])
            ->event($event)
            ->log("access-control.{$event}");
    }

    /**
     * The package hands over ids, models or collections depending on the call site.
     *
     * @return array<int, string>
     */
    private function resolveRoles(mixed $rolesOrIds): array
    {
        return $this->resolveNames($rolesOrIds, Role::class);
    }

    /**
     * @return array<int, string>
     */
    private function resolvePermissions(mixed $permissionsOrIds): array
    {
        return $this->resolveNames($permissionsOrIds, Permission::class);
    }

    /**
     * @param  class-string<Model>  $model
     * @return array<int, string>
     */
    private function resolveNames(mixed $value, string $model): array
    {
        $values = Collection::wrap($value);

        [$models, $ids] = $values->partition(fn (mixed $item): bool => $item instanceof Model);

        $names = $models->map(fn (Model $item): string => (string) $item->getAttribute('name'));

        if ($ids->isNotEmpty()) {
            $names = $names->merge(
                $model::query()->whereKey($ids->all())->pluck('name'),
            );
        }

        return $names->filter()->values()->all();
    }
}
