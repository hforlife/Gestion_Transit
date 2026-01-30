<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PortOperation;
use Illuminate\Auth\Access\HandlesAuthorization;

class PortOperationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PortOperation');
    }

    public function view(AuthUser $authUser, PortOperation $portOperation): bool
    {
        return $authUser->can('View:PortOperation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PortOperation');
    }

    public function update(AuthUser $authUser, PortOperation $portOperation): bool
    {
        return $authUser->can('Update:PortOperation');
    }

    public function delete(AuthUser $authUser, PortOperation $portOperation): bool
    {
        return $authUser->can('Delete:PortOperation');
    }

    public function restore(AuthUser $authUser, PortOperation $portOperation): bool
    {
        return $authUser->can('Restore:PortOperation');
    }

    public function forceDelete(AuthUser $authUser, PortOperation $portOperation): bool
    {
        return $authUser->can('ForceDelete:PortOperation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PortOperation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PortOperation');
    }

    public function replicate(AuthUser $authUser, PortOperation $portOperation): bool
    {
        return $authUser->can('Replicate:PortOperation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PortOperation');
    }

}