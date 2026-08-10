<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DisplayScreen;
use Illuminate\Auth\Access\HandlesAuthorization;

class DisplayScreenPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DisplayScreen');
    }

    public function view(AuthUser $authUser, DisplayScreen $displayScreen): bool
    {
        return $authUser->can('View:DisplayScreen');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DisplayScreen');
    }

    public function update(AuthUser $authUser, DisplayScreen $displayScreen): bool
    {
        return $authUser->can('Update:DisplayScreen');
    }

    public function delete(AuthUser $authUser, DisplayScreen $displayScreen): bool
    {
        return $authUser->can('Delete:DisplayScreen');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:DisplayScreen');
    }

    public function restore(AuthUser $authUser, DisplayScreen $displayScreen): bool
    {
        return $authUser->can('Restore:DisplayScreen');
    }

    public function forceDelete(AuthUser $authUser, DisplayScreen $displayScreen): bool
    {
        return $authUser->can('ForceDelete:DisplayScreen');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DisplayScreen');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DisplayScreen');
    }

    public function replicate(AuthUser $authUser, DisplayScreen $displayScreen): bool
    {
        return $authUser->can('Replicate:DisplayScreen');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DisplayScreen');
    }

}