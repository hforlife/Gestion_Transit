<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TypeDossier;
use Illuminate\Auth\Access\HandlesAuthorization;

class TypeDossierPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TypeDossier');
    }

    public function view(AuthUser $authUser, TypeDossier $typeDossier): bool
    {
        return $authUser->can('View:TypeDossier');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TypeDossier');
    }

    public function update(AuthUser $authUser, TypeDossier $typeDossier): bool
    {
        return $authUser->can('Update:TypeDossier');
    }

    public function delete(AuthUser $authUser, TypeDossier $typeDossier): bool
    {
        return $authUser->can('Delete:TypeDossier');
    }

    public function restore(AuthUser $authUser, TypeDossier $typeDossier): bool
    {
        return $authUser->can('Restore:TypeDossier');
    }

    public function forceDelete(AuthUser $authUser, TypeDossier $typeDossier): bool
    {
        return $authUser->can('ForceDelete:TypeDossier');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TypeDossier');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TypeDossier');
    }

    public function replicate(AuthUser $authUser, TypeDossier $typeDossier): bool
    {
        return $authUser->can('Replicate:TypeDossier');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TypeDossier');
    }

}