<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\JadwalShift;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class JadwalShiftPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JadwalShift');
    }

    public function view(AuthUser $authUser, JadwalShift $jadwalShift): bool
    {
        return $authUser->can('View:JadwalShift');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JadwalShift');
    }

    public function update(AuthUser $authUser, JadwalShift $jadwalShift): bool
    {
        return $authUser->can('Update:JadwalShift');
    }

    public function delete(AuthUser $authUser, JadwalShift $jadwalShift): bool
    {
        return $authUser->can('Delete:JadwalShift');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:JadwalShift');
    }

    public function restore(AuthUser $authUser, JadwalShift $jadwalShift): bool
    {
        return $authUser->can('Restore:JadwalShift');
    }

    public function forceDelete(AuthUser $authUser, JadwalShift $jadwalShift): bool
    {
        return $authUser->can('ForceDelete:JadwalShift');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JadwalShift');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JadwalShift');
    }

    public function replicate(AuthUser $authUser, JadwalShift $jadwalShift): bool
    {
        return $authUser->can('Replicate:JadwalShift');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JadwalShift');
    }
}
