<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ColisUnite;
use Illuminate\Auth\Access\HandlesAuthorization;

class ColisUnitePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ColisUnite');
    }

    public function view(AuthUser $authUser, ColisUnite $colisUnite): bool
    {
        return $authUser->can('View:ColisUnite');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ColisUnite');
    }

    public function update(AuthUser $authUser, ColisUnite $colisUnite): bool
    {
        return $authUser->can('Update:ColisUnite');
    }

    public function delete(AuthUser $authUser, ColisUnite $colisUnite): bool
    {
        return $authUser->can('Delete:ColisUnite');
    }

    public function restore(AuthUser $authUser, ColisUnite $colisUnite): bool
    {
        return $authUser->can('Restore:ColisUnite');
    }

    public function forceDelete(AuthUser $authUser, ColisUnite $colisUnite): bool
    {
        return $authUser->can('ForceDelete:ColisUnite');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ColisUnite');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ColisUnite');
    }

    public function replicate(AuthUser $authUser, ColisUnite $colisUnite): bool
    {
        return $authUser->can('Replicate:ColisUnite');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ColisUnite');
    }

}