<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DouaneOperation;
use Illuminate\Auth\Access\HandlesAuthorization;

class DouaneOperationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DouaneOperation');
    }

    public function view(AuthUser $authUser, DouaneOperation $douaneOperation): bool
    {
        return $authUser->can('View:DouaneOperation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DouaneOperation');
    }

    public function update(AuthUser $authUser, DouaneOperation $douaneOperation): bool
    {
        return $authUser->can('Update:DouaneOperation');
    }

    public function delete(AuthUser $authUser, DouaneOperation $douaneOperation): bool
    {
        return $authUser->can('Delete:DouaneOperation');
    }

    public function restore(AuthUser $authUser, DouaneOperation $douaneOperation): bool
    {
        return $authUser->can('Restore:DouaneOperation');
    }

    public function forceDelete(AuthUser $authUser, DouaneOperation $douaneOperation): bool
    {
        return $authUser->can('ForceDelete:DouaneOperation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DouaneOperation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DouaneOperation');
    }

    public function replicate(AuthUser $authUser, DouaneOperation $douaneOperation): bool
    {
        return $authUser->can('Replicate:DouaneOperation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DouaneOperation');
    }

}