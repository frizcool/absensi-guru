<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PengajuanIzin;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PengajuanIzinPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PengajuanIzin');
    }

    public function view(AuthUser $authUser, PengajuanIzin $pengajuanIzin): bool
    {
        return $authUser->can('View:PengajuanIzin');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PengajuanIzin');
    }

    public function update(AuthUser $authUser, PengajuanIzin $pengajuanIzin): bool
    {
        return $authUser->can('Update:PengajuanIzin');
    }

    public function delete(AuthUser $authUser, PengajuanIzin $pengajuanIzin): bool
    {
        return $authUser->can('Delete:PengajuanIzin');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PengajuanIzin');
    }

    public function restore(AuthUser $authUser, PengajuanIzin $pengajuanIzin): bool
    {
        return $authUser->can('Restore:PengajuanIzin');
    }

    public function forceDelete(AuthUser $authUser, PengajuanIzin $pengajuanIzin): bool
    {
        return $authUser->can('ForceDelete:PengajuanIzin');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PengajuanIzin');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PengajuanIzin');
    }

    public function replicate(AuthUser $authUser, PengajuanIzin $pengajuanIzin): bool
    {
        return $authUser->can('Replicate:PengajuanIzin');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PengajuanIzin');
    }
}
