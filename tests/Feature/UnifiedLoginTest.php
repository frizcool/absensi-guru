<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UnifiedLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
    }

    public function test_login_page_is_shared_by_all_roles(): void
    {
        $this->get('/sekolahku')
            ->assertSuccessful()
            ->assertSee('Masuk ke Sistem');
    }

    public function test_guru_is_redirected_to_guru_panel_after_logging_in_with_nip(): void
    {
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);

        $guru = Guru::create([
            'nama' => 'Siti Aminah',
            'nip' => '198810202011012015',
            'jenis_kelamin' => 'P',
            'status_kepegawaian' => 'pns',
            'shift_id' => $shift->id,
            'aktif' => true,
        ]);

        $this->post('/sekolahku', [
            'identity' => $guru->nip,
            'password' => $guru->nip,
        ])->assertRedirect('/guru');

        $this->assertAuthenticatedAs($guru->user);
    }

    public function test_guru_can_login_with_password_fallback_when_db_has_nip(): void
    {
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);

        $guru = Guru::create([
            'nama' => 'Ahmad Guru',
            'nip' => '198507122010011015',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'pns',
            'shift_id' => $shift->id,
            'aktif' => true,
        ]);

        // Login using email and password 'password'
        $this->post('/sekolahku', [
            'identity' => $guru->user->email,
            'password' => 'password',
        ])->assertRedirect('/guru');

        $this->assertAuthenticatedAs($guru->user);
    }

    public function test_admin_is_redirected_to_sekolahku_after_logging_in_with_email(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@sekolah.sch.id',
            'password' => Hash::make('admin12345'),
        ]);
        $admin->assignRole('admin');

        $this->post('/sekolahku', [
            'identity' => $admin->email,
            'password' => 'admin12345',
        ])->assertRedirect('/sekolahku/panel');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_invalid_login_is_rejected(): void
    {
        $this->from('/sekolahku')
            ->post('/sekolahku', [
                'identity' => 'unknown@sekolah.sch.id',
                'password' => 'wrong-password',
            ])
            ->assertRedirect('/sekolahku')
            ->assertSessionHasErrors('identity');
    }

    public function test_admin_panel_uses_the_sekolahku_path(): void
    {
        $this->assertStringContainsString('/sekolahku/panel', route('filament.admin.pages.dashboard'));
    }

    public function test_there_are_no_separate_login_endpoints(): void
    {
        $this->get('/login')->assertNotFound();
        $this->get('/guru/login')->assertNotFound();
        $this->get('/sekolahku/panel/login')->assertNotFound();
    }

    public function test_guru_tanpa_user_dibuatkan_akun_saat_login_dengan_nip(): void
    {
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);

        // Simulasikan data Guru tanpa user_id
        $guru = new Guru([
            'nama' => 'Budi Mandiri',
            'nip' => '199001012015011002',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'pns',
            'shift_id' => $shift->id,
            'aktif' => true,
        ]);
        $guru->saveQuietly();
        $this->assertNull($guru->user_id);

        $this->post('/sekolahku', [
            'identity' => '199001012015011002',
            'password' => '199001012015011002',
        ])->assertRedirect('/guru');

        $guru->refresh();
        $this->assertNotNull($guru->user_id);
        $this->assertAuthenticatedAs($guru->user);
    }
}
