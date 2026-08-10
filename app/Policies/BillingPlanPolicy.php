<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BillingPlan;
use Illuminate\Auth\Access\HandlesAuthorization;

class BillingPlanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BillingPlan');
    }

    public function view(AuthUser $authUser, BillingPlan $billingPlan): bool
    {
        return $authUser->can('View:BillingPlan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BillingPlan');
    }

    public function update(AuthUser $authUser, BillingPlan $billingPlan): bool
    {
        return $authUser->can('Update:BillingPlan');
    }

    public function delete(AuthUser $authUser, BillingPlan $billingPlan): bool
    {
        return $authUser->can('Delete:BillingPlan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BillingPlan');
    }

    public function restore(AuthUser $authUser, BillingPlan $billingPlan): bool
    {
        return $authUser->can('Restore:BillingPlan');
    }

    public function forceDelete(AuthUser $authUser, BillingPlan $billingPlan): bool
    {
        return $authUser->can('ForceDelete:BillingPlan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BillingPlan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BillingPlan');
    }

    public function replicate(AuthUser $authUser, BillingPlan $billingPlan): bool
    {
        return $authUser->can('Replicate:BillingPlan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BillingPlan');
    }

}