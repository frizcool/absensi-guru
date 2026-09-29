<?php

namespace Tests\Feature;

use App\Models\PengaturanSekolah;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LogoSekolahIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $this->adminUser = User::factory()->create([
            'email' => 'admin@sekolah.sch.id',
        ]);
        $this->adminUser->assignRole($adminRole);

        Storage::fake('public');
    }

    public function test_logo_dan_favicon_halaman_login_menggunakan_logo_sekolah(): void
    {
        $file = UploadedFile::fake()->image('custom-logo.png', 200, 200);
        $path = $file->store('sekolah-logo', 'public');

        PengaturanSekolah::getSetting()->update([
            'nama_sekolah' => 'SD Negeri Harapan Bangsa',
            'logo' => $path,
        ]);

        $logoUrl = Storage::disk('public')->url($path);

        $response = $this->get('/sekolahku');
        $response->assertSuccessful();

        $response->assertSee($logoUrl, false);
        $response->assertSee('<link rel="icon" href="'.$logoUrl.'">', false);
        $response->assertSee('<link rel="apple-touch-icon" href="'.$logoUrl.'">', false);
        $response->assertSee('alt="Logo SD Negeri Harapan Bangsa"', false);
    }

    public function test_kop_surat_dan_favicon_laporan_cetak_menggunakan_logo_sekolah(): void
    {
        $file = UploadedFile::fake()->image('logo-kop.png', 200, 200);
        $path = $file->store('sekolah-logo', 'public');

        PengaturanSekolah::getSetting()->update([
            'nama_sekolah' => 'SD Negeri Harapan Bangsa',
            'logo' => $path,
        ]);

        $logoUrl = Storage::disk('public')->url($path);

        $response = $this->actingAs($this->adminUser)->get('/laporan/cetak-bulanan?bulan='.now()->month.'&tahun='.now()->year);
        $response->assertSuccessful();

        $response->assertSee('<link rel="icon" href="'.$logoUrl.'">', false);
        $response->assertSee('<img src="'.$logoUrl.'" alt="Logo Sekolah">', false);
    }

    public function test_panel_admin_dan_guru_menyematkan_brand_logo_dan_favicon_sekolah(): void
    {
        $file = UploadedFile::fake()->image('logo-panel.png', 200, 200);
        $path = $file->store('sekolah-logo', 'public');

        PengaturanSekolah::getSetting()->update([
            'nama_sekolah' => 'SD Negeri Harapan Bangsa',
            'logo' => $path,
        ]);

        $logoUrl = Storage::disk('public')->url($path);

        $adminPanel = Filament::getPanel('admin');
        $this->assertEquals($logoUrl, $adminPanel->getFavicon());
        $this->assertEquals('SD Negeri Harapan Bangsa', $adminPanel->getBrandName());

        $guruPanel = Filament::getPanel('guru');
        $this->assertEquals($logoUrl, $guruPanel->getFavicon());
        $this->assertEquals('SD Negeri Harapan Bangsa', $guruPanel->getBrandName());

        $adminBrandLogoHtml = (string) $adminPanel->getBrandLogo();
        $this->assertStringContainsString($logoUrl, $adminBrandLogoHtml);
        $this->assertStringContainsString('SD Negeri Harapan Bangsa', $adminBrandLogoHtml);
        $this->assertStringContainsString('Panel Manajemen Sekolah', $adminBrandLogoHtml);

        $guruBrandLogoHtml = (string) $guruPanel->getBrandLogo();
        $this->assertStringContainsString($logoUrl, $guruBrandLogoHtml);
        $this->assertStringContainsString('SD Negeri Harapan Bangsa', $guruBrandLogoHtml);
        $this->assertStringContainsString('Portal Guru &amp; Presensi', $guruBrandLogoHtml);
    }

    public function test_fallback_icon_default_ketika_logo_sekolah_kosong(): void
    {
        PengaturanSekolah::getSetting()->update([
            'nama_sekolah' => 'SD Inpres Contoh',
            'logo' => null,
        ]);

        $response = $this->get('/sekolahku');
        $response->assertSuccessful();

        $defaultIcon = asset('icons/icon.svg');
        $response->assertSee('<link rel="icon" href="'.$defaultIcon.'">', false);
        $response->assertSee('<link rel="apple-touch-icon" href="'.$defaultIcon.'">', false);

        $adminPanel = Filament::getPanel('admin');
        $this->assertEquals($defaultIcon, $adminPanel->getFavicon());

        $adminBrandLogoHtml = (string) $adminPanel->getBrandLogo();
        $this->assertStringContainsString('SD Inpres Contoh', $adminBrandLogoHtml);
        $this->assertStringContainsString('🏫', $adminBrandLogoHtml);
    }
}
