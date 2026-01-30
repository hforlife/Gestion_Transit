<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Expertise;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpertisePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Expertise');
    }

    public function view(AuthUser $authUser, Expertise $expertise): bool
    {
        return $authUser->can('View:Expertise');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Expertise');
    }

    public function update(AuthUser $authUser, Expertise $expertise): bool
    {
        return $authUser->can('Update:Expertise');
    }

    public function delete(AuthUser $authUser, Expertise $expertise): bool
    {
        return $authUser->can('Delete:Expertise');
    }

    public function restore(AuthUser $authUser, Expertise $expertise): bool
    {
        return $authUser->can('Restore:Expertise');
    }

    public function forceDelete(AuthUser $authUser, Expertise $expertise): bool
    {
        return $authUser->can('ForceDelete:Expertise');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Expertise');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Expertise');
    }

    public function replicate(AuthUser $authUser, Expertise $expertise): bool
    {
        return $authUser->can('Replicate:Expertise');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Expertise');
    }

}