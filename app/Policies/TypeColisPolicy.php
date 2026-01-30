<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TypeColis;
use Illuminate\Auth\Access\HandlesAuthorization;

class TypeColisPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TypeColis');
    }

    public function view(AuthUser $authUser, TypeColis $typeColis): bool
    {
        return $authUser->can('View:TypeColis');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TypeColis');
    }

    public function update(AuthUser $authUser, TypeColis $typeColis): bool
    {
        return $authUser->can('Update:TypeColis');
    }

    public function delete(AuthUser $authUser, TypeColis $typeColis): bool
    {
        return $authUser->can('Delete:TypeColis');
    }

    public function restore(AuthUser $authUser, TypeColis $typeColis): bool
    {
        return $authUser->can('Restore:TypeColis');
    }

    public function forceDelete(AuthUser $authUser, TypeColis $typeColis): bool
    {
        return $authUser->can('ForceDelete:TypeColis');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TypeColis');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TypeColis');
    }

    public function replicate(AuthUser $authUser, TypeColis $typeColis): bool
    {
        return $authUser->can('Replicate:TypeColis');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TypeColis');
    }

}