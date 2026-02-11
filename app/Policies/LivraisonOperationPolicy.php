<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LivraisonOperation;
use Illuminate\Auth\Access\HandlesAuthorization;

class LivraisonOperationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LivraisonOperation');
    }

    public function view(AuthUser $authUser, LivraisonOperation $livraisonOperation): bool
    {
        return $authUser->can('View:LivraisonOperation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LivraisonOperation');
    }

    public function update(AuthUser $authUser, LivraisonOperation $livraisonOperation): bool
    {
        return $authUser->can('Update:LivraisonOperation');
    }

    public function delete(AuthUser $authUser, LivraisonOperation $livraisonOperation): bool
    {
        return $authUser->can('Delete:LivraisonOperation');
    }

    public function restore(AuthUser $authUser, LivraisonOperation $livraisonOperation): bool
    {
        return $authUser->can('Restore:LivraisonOperation');
    }

    public function forceDelete(AuthUser $authUser, LivraisonOperation $livraisonOperation): bool
    {
        return $authUser->can('ForceDelete:LivraisonOperation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LivraisonOperation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LivraisonOperation');
    }

    public function replicate(AuthUser $authUser, LivraisonOperation $livraisonOperation): bool
    {
        return $authUser->can('Replicate:LivraisonOperation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LivraisonOperation');
    }

}