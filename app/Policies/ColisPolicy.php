<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Colis;
use Illuminate\Auth\Access\HandlesAuthorization;

class ColisPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Colis');
    }

    public function view(AuthUser $authUser, Colis $colis): bool
    {
        return $authUser->can('View:Colis');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Colis');
    }

    public function update(AuthUser $authUser, Colis $colis): bool
    {
        return $authUser->can('Update:Colis');
    }

    public function delete(AuthUser $authUser, Colis $colis): bool
    {
        return $authUser->can('Delete:Colis');
    }

    public function restore(AuthUser $authUser, Colis $colis): bool
    {
        return $authUser->can('Restore:Colis');
    }

    public function forceDelete(AuthUser $authUser, Colis $colis): bool
    {
        return $authUser->can('ForceDelete:Colis');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Colis');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Colis');
    }

    public function replicate(AuthUser $authUser, Colis $colis): bool
    {
        return $authUser->can('Replicate:Colis');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Colis');
    }

}