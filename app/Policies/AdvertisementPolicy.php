<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AdvertisementPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        if ($this->isAdvertiser($authUser)) {
            return true;
        }

        return $authUser->can('ViewAny:Advertisement');
    }

    public function view(AuthUser $authUser, Advertisement $advertisement): bool
    {
        if ($this->ownsAdvertisement($authUser, $advertisement)) {
            return true;
        }

        return $authUser->can('View:Advertisement');
    }

    public function create(AuthUser $authUser): bool
    {
        if ($this->isAdvertiser($authUser)) {
            return true;
        }

        return $authUser->can('Create:Advertisement');
    }

    public function update(AuthUser $authUser, Advertisement $advertisement): bool
    {
        if ($this->ownsAdvertisement($authUser, $advertisement)) {
            return true;
        }

        return $authUser->can('Update:Advertisement');
    }

    public function delete(AuthUser $authUser, Advertisement $advertisement): bool
    {
        if ($this->ownsAdvertisement($authUser, $advertisement)) {
            return true;
        }

        return $authUser->can('Delete:Advertisement');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        if ($this->isAdvertiser($authUser)) {
            return true;
        }

        return $authUser->can('DeleteAny:Advertisement');
    }

    public function restore(AuthUser $authUser, Advertisement $advertisement): bool
    {
        return $authUser->can('Restore:Advertisement');
    }

    public function forceDelete(AuthUser $authUser, Advertisement $advertisement): bool
    {
        return $authUser->can('ForceDelete:Advertisement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Advertisement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Advertisement');
    }

    public function replicate(AuthUser $authUser, Advertisement $advertisement): bool
    {
        return $authUser->can('Replicate:Advertisement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Advertisement');
    }

    private function isAdvertiser(AuthUser $authUser): bool
    {
        return $authUser instanceof User && $authUser->isAdvertiser();
    }

    private function ownsAdvertisement(AuthUser $authUser, Advertisement $advertisement): bool
    {
        return $authUser instanceof User
            && $authUser->isAdvertiser()
            && $authUser->advertiser_id === $advertisement->advertiser_id;
    }
}
