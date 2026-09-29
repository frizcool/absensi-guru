<?php

namespace Tests\Feature;

use App\Filament\Pages\PengaturanSekolahPage;
use App\Models\PengaturanSekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PengaturanSekolahTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'View:PengaturanSekolahPage', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permission);

        $this->adminUser = User::factory()->create([
            'email' => 'admin@sekolah.sch.id',
        ]);
        $this->adminUser->assignRole($adminRole);
    }

    public function test_halaman_pengaturan_sekolah_dapat_diakses_oleh_admin(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/sekolahku/panel/pengaturan-sekolah-page');
        $response->assertSuccessful();
        $response->assertSee('Visualisasi Peta Radius Geofence Presensi');
        $response->assertSee('ps-leaflet-map');
        $response->assertSee('wire:ignore', false);
        $response->assertSee('leaflet.css');
        $response->assertSee('leaflet.js');
        $response->assertSee('ensureLeaflet');
    }

    public function test_livewire_pengaturan_sekolah_memuat_data_awal_koordinat_dan_radius(): void
    {
        PengaturanSekolah::getSetting()->update([
            'nama_sekolah' => 'SD Negeri Contoh',
            'latitude' => -5.123456,
            'longitude' => 119.654321,
            'radius_meter' => 150,
            'wajib_validasi_lokasi' => true,
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(PengaturanSekolahPage::class)
            ->assertSet('data.latitude', -5.123456)
            ->assertSet('data.longitude', 119.654321)
            ->assertSet('data.radius_meter', 150)
            ->assertSet('data.nama_sekolah', 'SD Negeri Contoh');
    }

    public function test_metode_set_koordinat_dari_gps_mengubah_state_tanpa_error(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(PengaturanSekolahPage::class)
            ->call('setKoordinatDariGps', -6.200000, 106.816666)
            ->assertSet('data.latitude', -6.200000)
            ->assertSet('data.longitude', 106.816666)
            ->assertNotified('Koordinat GPS berhasil diperoleh');
    }

    public function test_simpan_pengaturan_berhasil_memperbarui_database(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(PengaturanSekolahPage::class)
            ->fillForm([
                'nama_sekolah' => 'SD Unggulan 1',
                'npsn' => '12345678',
                'wajib_validasi_lokasi' => true,
                'latitude' => -5.150000,
                'longitude' => 119.440000,
                'radius_meter' => 120,
                'notif_terlambat_aktif' => false,
            ])
            ->call('simpan')
            ->assertNotified('Pengaturan berhasil disimpan');

        $setting = PengaturanSekolah::getSetting();
        $this->assertEquals('SD Unggulan 1', $setting->nama_sekolah);
        $this->assertEquals(-5.150000, $setting->latitude);
        $this->assertEquals(119.440000, $setting->longitude);
        $this->assertEquals(120, $setting->radius_meter);
    }
}
