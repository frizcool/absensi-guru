<?php

namespace App\Console\Commands;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SyncGuruUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guru:sync-users {--reset-passwords : Reset all teacher passwords back to standard NIP}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pastikan semua guru memiliki akun User dengan standar NIP dan password NIP';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai sinkronisasi akun User untuk semua Guru...');

        $guruRole = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $gurus = Guru::all();

        $countSynced = 0;
        $countPasswordReset = 0;

        foreach ($gurus as $guru) {
            $user = $guru->user;
            $nip = $guru->nip;
            $defaultPassword = ! empty($nip) ? $nip : 'password';

            if (! $user) {
                // Cari apakah user dengan NIP atau email sudah ada
                if (! empty($nip)) {
                    $user = User::where('nip', $nip)->first();
                }

                if (! $user && ! empty($nip)) {
                    $user = User::where('email', "{$nip}@sekolah.sch.id")->first();
                }

                if (! $user) {
                    $email = ! empty($nip)
                        ? "{$nip}@sekolah.sch.id"
                        : 'guru_'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $guru->nama)).'_'.rand(100, 999).'@sekolah.sch.id';

                    $user = User::create([
                        'name' => $guru->nama,
                        'nip' => $nip,
                        'email' => $email,
                        'password' => Hash::make($defaultPassword),
                    ]);
                }

                $guru->user_id = $user->id;
                $guru->saveQuietly();
                $countSynced++;
            }

            // Pastikan NIP dan role guru tersinkronisasi
            if ($user) {
                $needsUpdate = false;
                if (! empty($nip) && $user->nip !== $nip) {
                    $user->nip = $nip;
                    $needsUpdate = true;
                }

                if ($this->option('reset-passwords')) {
                    $user->password = Hash::make($defaultPassword);
                    $needsUpdate = true;
                    $countPasswordReset++;
                }

                if ($needsUpdate) {
                    $user->saveQuietly();
                }

                if (! $user->hasRole('guru')) {
                    $user->assignRole($guruRole);
                }
            }

            $this->line("  ✓ [{$guru->nama}] NIP: ".($nip ?: '-')." -> User ID: {$guru->user_id} ({$guru->user?->email})");
        }

        $this->info("Sinkronisasi selesai! {$gurus->count()} Guru diperiksa.");
        if ($this->option('reset-passwords')) {
            $this->info("{$countPasswordReset} password guru telah direset ke standar NIP.");
        }

        return Command::SUCCESS;
    }
}
