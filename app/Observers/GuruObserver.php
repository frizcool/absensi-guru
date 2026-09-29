<?php

namespace App\Observers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class GuruObserver
{
    /**
     * Handle the Guru "saving" event.
     */
    public function saving(Guru $guru): void
    {
        // 1. Jika belum ada user_id, cari atau buatkan user akun secara otomatis
        if (empty($guru->user_id)) {
            $user = null;

            // Coba cari jika user dengan NIP atau email sudah ada
            if (! empty($guru->nip)) {
                $user = User::where('nip', $guru->nip)->first();
            }

            if (! $user && ! empty($guru->nip)) {
                $user = User::where('email', "{$guru->nip}@sekolah.sch.id")->first();
            }

            // Jika belum ada user, buat baru dengan password standar NIP
            if (! $user) {
                $email = ! empty($guru->nip)
                    ? "{$guru->nip}@sekolah.sch.id"
                    : 'guru_'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $guru->nama)).'_'.rand(100, 999).'@sekolah.sch.id';

                $password = ! empty($guru->nip) ? $guru->nip : 'password';

                $user = User::create([
                    'name' => $guru->nama,
                    'nip' => $guru->nip,
                    'email' => $email,
                    'password' => Hash::make($password),
                ]);
            }

            $guruRole = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
            if (! $user->hasRole('guru')) {
                $user->assignRole($guruRole);
            }

            $guru->user_id = $user->id;
        } else {
            // 2. Jika user_id sudah ada, sinkronkan NIP dan pastikan role guru terpasang
            $user = User::find($guru->user_id);
            if ($user) {
                $needsSave = false;

                if (! empty($guru->nip) && $user->nip !== $guru->nip) {
                    $user->nip = $guru->nip;
                    $needsSave = true;
                }

                if ($user->name !== $guru->nama) {
                    $user->name = $guru->nama;
                    $needsSave = true;
                }

                if ($needsSave) {
                    $user->saveQuietly();
                }

                $guruRole = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
                if (! $user->hasRole('guru')) {
                    $user->assignRole($guruRole);
                }
            }
        }
    }
}
