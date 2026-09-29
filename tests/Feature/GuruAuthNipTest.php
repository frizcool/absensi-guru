<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\Login;
use App\Models\Guru;
use App\Models\Shift;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GuruAuthNipTest extends TestCase
{
    use RefreshDatabase;

    protected Shift $shift;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);

        $this->shift = Shift::create([
            'nama' => 'Shift Pagi Reguler',
            'jam_masuk' => '07:15:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);
    }

    public function test_pembuatan_guru_otomatis_membuat_user_dengan_nip_dan_password_nip(): void
    {
        $guru = Guru::create([
            'nama' => 'Drs. H. Mulyadi, M.Pd.',
            'nip' => '197505122005011003',
            'nuptk' => '1234567890123456',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'pns',
            'shift_id' => $this->shift->id,
            'aktif' => true,
        ]);

        $this->assertNotNull($guru->user_id);

        $user = User::find($guru->user_id);
        $this->assertNotNull($user);
        $this->assertEquals('197505122005011003', $user->nip);
        $this->assertEquals('197505122005011003@sekolah.sch.id', $user->email);
        $this->assertTrue(Hash::check('197505122005011003', $user->password));
        $this->assertTrue($user->hasRole('guru'));
    }

    public function test_guru_dapat_login_menggunakan_nip_dan_password_nip_pada_panel_guru(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('guru'));

        $guru = Guru::create([
            'nama' => 'Siti Aminah, S.Pd.',
            'nip' => '198810202011012015',
            'jenis_kelamin' => 'P',
            'status_kepegawaian' => 'pns',
            'shift_id' => $this->shift->id,
            'aktif' => true,
        ]);

        // Login menggunakan NIP sebagai identitas dan NIP sebagai password
        Livewire::test(Login::class)
            ->fillForm([
                'email' => '198810202011012015',
                'password' => '198810202011012015',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($guru->user);
    }

    public function test_guru_dapat_login_menggunakan_email_dan_password_nip(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('guru'));

        $guru = Guru::create([
            'nama' => 'Bambang Pamungkas, S.Pd.',
            'nip' => '198302142008011009',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'pns',
            'shift_id' => $this->shift->id,
            'aktif' => true,
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => '198302142008011009@sekolah.sch.id',
                'password' => '198302142008011009',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($guru->user);
    }

    public function test_reset_password_guru_mengembalikan_password_ke_nip(): void
    {
        $guru = Guru::create([
            'nama' => 'Ratna Sari, S.Pd.',
            'nip' => '199201012020122001',
            'jenis_kelamin' => 'P',
            'status_kepegawaian' => 'pppk',
            'shift_id' => $this->shift->id,
            'aktif' => true,
        ]);

        // Ubah password ke password kustom
        $user = $guru->user;
        $user->password = Hash::make('password_baru_123');
        $user->save();

        $this->assertFalse(Hash::check('199201012020122001', $user->fresh()->password));

        // Panggil reset password ke NIP
        $guru->resetPasswordKeNip();

        $this->assertTrue(Hash::check('199201012020122001', $user->fresh()->password));
    }

    public function test_admin_dapat_login_pada_panel_admin(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $adminUser = User::create([
            'name' => 'Administrator',
            'email' => 'admin@sekolah.sch.id',
            'password' => Hash::make('admin12345'),
        ]);
        $adminUser->assignRole('admin');

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'admin@sekolah.sch.id',
                'password' => 'admin12345',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($adminUser);
    }

    public function test_halaman_daftar_guru_dapat_diakses_dan_merender_tabel_aksi(): void
    {
        $adminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@sekolah.sch.id',
            'password' => Hash::make('password'),
        ]);
        $adminUser->assignRole('super_admin');

        $this->actingAs($adminUser);

        $response = $this->get('/sekolahku/panel/gurus');
        $response->assertSuccessful();
        $response->assertSee('Daftar Guru & Tendik');
        $response->assertSee('Akun Login');
    }
}
