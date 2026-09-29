<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\JurnalPembelajaran;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class JurnalPembelajaranPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JurnalPembelajaran');
    }

    public function view(AuthUser $authUser, JurnalPembelajaran $jurnalPembelajaran): bool
    {
        return $authUser->can('View:JurnalPembelajaran');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JurnalPembelajaran');
    }

    public function update(AuthUser $authUser, JurnalPembelajaran $jurnalPembelajaran): bool
    {
        return $authUser->can('Update:JurnalPembelajaran');
    }

    public function delete(AuthUser $authUser, JurnalPembelajaran $jurnalPembelajaran): bool
    {
        return $authUser->can('Delete:JurnalPembelajaran');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:JurnalPembelajaran');
    }

    public function restore(AuthUser $authUser, JurnalPembelajaran $jurnalPembelajaran): bool
    {
        return $authUser->can('Restore:JurnalPembelajaran');
    }

    public function forceDelete(AuthUser $authUser, JurnalPembelajaran $jurnalPembelajaran): bool
    {
        return $authUser->can('ForceDelete:JurnalPembelajaran');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JurnalPembelajaran');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JurnalPembelajaran');
    }

    public function replicate(AuthUser $authUser, JurnalPembelajaran $jurnalPembelajaran): bool
    {
        return $authUser->can('Replicate:JurnalPembelajaran');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JurnalPembelajaran');
    }
}
