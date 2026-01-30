<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DossierTransit;
use Illuminate\Auth\Access\HandlesAuthorization;

class DossierTransitPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DossierTransit');
    }

    public function view(AuthUser $authUser, DossierTransit $dossierTransit): bool
    {
        return $authUser->can('View:DossierTransit');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DossierTransit');
    }

    public function update(AuthUser $authUser, DossierTransit $dossierTransit): bool
    {
        return $authUser->can('Update:DossierTransit');
    }

    public function delete(AuthUser $authUser, DossierTransit $dossierTransit): bool
    {
        return $authUser->can('Delete:DossierTransit');
    }

    public function restore(AuthUser $authUser, DossierTransit $dossierTransit): bool
    {
        return $authUser->can('Restore:DossierTransit');
    }

    public function forceDelete(AuthUser $authUser, DossierTransit $dossierTransit): bool
    {
        return $authUser->can('ForceDelete:DossierTransit');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DossierTransit');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DossierTransit');
    }

    public function replicate(AuthUser $authUser, DossierTransit $dossierTransit): bool
    {
        return $authUser->can('Replicate:DossierTransit');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DossierTransit');
    }

}