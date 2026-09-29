<?php

namespace Tests\Feature;

use App\Filament\Guru\Pages\PengajuanIzinGuruPage;
use App\Filament\Guru\Pages\PresensiSaya;
use App\Filament\Guru\Pages\RiwayatPresensiGuruPage;
use App\Filament\Guru\Widgets\GuruRiwayatTerbaruWidget;
use App\Filament\Guru\Widgets\GuruStatistikRingkasanWidget;
use App\Filament\Guru\Widgets\GuruStatusPresensiHariIniWidget;
use App\Filament\Pages\LaporanPresensiPage;
use App\Filament\Pages\PengaturanSekolahPage;
use App\Filament\Widgets\AdminExecutiveOverviewWidget;
use App\Filament\Widgets\AnomaliPresensiWidget;
use App\Filament\Widgets\GuruTerbaruPresensiWidget;
use App\Filament\Widgets\LeaderboardDisiplinWidget;
use App\Filament\Widgets\PeringatanKeterlambatanWidget;
use App\Filament\Widgets\PresensiStatistikChartWidget;
use App\Filament\Widgets\PresensiTrenChartWidget;
use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\PengajuanIzin;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PresensiMandiriTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        PengaturanSekolah::create([
            'nama_sekolah' => 'UPTD SPF SD Inpres Rappojawa',
            'latitude' => -5.147665,
            'longitude' => 119.432731,
            'radius_meter' => 150,
            'wajib_validasi_lokasi' => false,
        ]);
    }

    public function test_halaman_utama_mengarahkan_ke_login_tunggal(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/sekolahku');
    }

    public function test_guru_dapat_melakukan_check_in_tepat_waktu(): void
    {
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 30,
        ]);

        $user = User::factory()->create();
        $guru = Guru::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'nama' => 'Budi Santoso, S.Pd',
            'status_kepegawaian' => 'pns',
            'aktif' => true,
        ]);

        Carbon::setTestNow(Carbon::today()->setTime(7, 15, 0));

        $presensi = Presensi::checkIn($guru, -5.147665, 119.432731);

        $this->assertNotNull($presensi);
        $this->assertEquals('hadir', $presensi->status_kehadiran);
        $this->assertEquals('tepat_waktu', $presensi->status_masuk);

        Carbon::setTestNow(); // Reset time
    }

    public function test_guru_check_in_terlambat_jika_melebihi_toleransi(): void
    {
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);

        $user = User::factory()->create();
        $guru = Guru::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'nama' => 'Siti Nurhaliza, S.Pd',
            'status_kepegawaian' => 'pppk',
            'aktif' => true,
        ]);

        Carbon::setTestNow(Carbon::today()->setTime(7, 30, 0));

        $presensi = Presensi::checkIn($guru, -5.147665, 119.432731);

        $this->assertNotNull($presensi);
        $this->assertEquals('hadir', $presensi->status_kehadiran);
        $this->assertEquals('terlambat', $presensi->status_masuk);
        $this->assertStringContainsString('Terlambat 15 menit', $presensi->keterangan);

        Carbon::setTestNow(); // Reset time
    }

    public function test_guru_dapat_melakukan_check_out(): void
    {
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);

        $user = User::factory()->create();
        $guru = Guru::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'nama' => 'Ahmad Dahlan, S.Pd',
            'status_kepegawaian' => 'pns',
            'aktif' => true,
        ]);

        Carbon::setTestNow(Carbon::today()->setTime(6, 50, 0));
        $presensi = Presensi::checkIn($guru, -5.147665, 119.432731);

        Carbon::setTestNow(Carbon::today()->setTime(14, 5, 0));
        $presensi->checkOut(-5.147665, 119.432731);

        $this->assertNotNull($presensi->jam_pulang);
        $this->assertEquals('normal', $presensi->status_pulang);

        Carbon::setTestNow(); // Reset time
    }

    public function test_check_in_ditolak_jika_posisi_guru_melebihi_radius_yang_ditentukan_admin(): void
    {
        PengaturanSekolah::first()->update([
            'wajib_validasi_lokasi' => true,
            'latitude' => -5.147665,
            'longitude' => 119.432731,
            'radius_meter' => 100,
        ]);

        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 30,
        ]);

        $user = User::factory()->create();
        $guru = Guru::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'nama' => 'Guru Jauh, S.Pd',
            'status_kepegawaian' => 'pns',
            'aktif' => true,
        ]);

        Carbon::setTestNow(Carbon::today()->setTime(7, 0, 0));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Presensi Ditolak! Posisi Anda berada di luar radius sekolah');

        // Koordinat berjarak ~1.1 km dari sekolah
        Presensi::checkIn($guru, -5.155000, 119.440000);

        Carbon::setTestNow();
    }

    public function test_check_out_ditolak_jika_posisi_guru_melebihi_radius_yang_ditentukan_admin(): void
    {
        PengaturanSekolah::first()->update([
            'wajib_validasi_lokasi' => true,
            'latitude' => -5.147665,
            'longitude' => 119.432731,
            'radius_meter' => 100,
        ]);

        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 30,
        ]);

        $user = User::factory()->create();
        $guru = Guru::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'nama' => 'Guru Pulang Jauh, S.Pd',
            'status_kepegawaian' => 'pns',
            'aktif' => true,
        ]);

        Carbon::setTestNow(Carbon::today()->setTime(7, 0, 0));
        // Check-in dalam radius
        $presensi = Presensi::checkIn($guru, -5.147665, 119.432731);

        Carbon::setTestNow(Carbon::today()->setTime(14, 0, 0));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Presensi Ditolak! Posisi Anda berada di luar radius sekolah');

        // Check-out di luar radius (~1.1 km)
        $presensi->checkOut(-5.155000, 119.440000);

        Carbon::setTestNow();
    }

    public function test_check_in_wajib_gps_jika_validasi_lokasi_aktif(): void
    {
        PengaturanSekolah::first()->update([
            'wajib_validasi_lokasi' => true,
            'latitude' => -5.147665,
            'longitude' => 119.432731,
            'radius_meter' => 100,
        ]);

        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 30,
        ]);

        $user = User::factory()->create();
        $guru = Guru::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'nama' => 'Guru Tanpa GPS, S.Pd',
            'status_kepegawaian' => 'pns',
            'aktif' => true,
        ]);

        Carbon::setTestNow(Carbon::today()->setTime(7, 0, 0));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Lokasi GPS wajib diaktifkan');

        Presensi::checkIn($guru, null, null);

        Carbon::setTestNow();
    }

    public function test_presensi_berhasil_setelah_admin_memperbesar_radius(): void
    {
        // Admin awalnya mengatur radius 50m
        $pengaturan = PengaturanSekolah::first();
        $pengaturan->update([
            'wajib_validasi_lokasi' => true,
            'latitude' => -5.147665,
            'longitude' => 119.432731,
            'radius_meter' => 50,
        ]);

        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 30,
        ]);

        $user = User::factory()->create();
        $guru = Guru::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'nama' => 'Guru Penyesuaian Radius, S.Pd',
            'status_kepegawaian' => 'pns',
            'aktif' => true,
        ]);

        // Jarak ~150 meter dari sekolah (-5.149000, 119.432731)
        $latGuru = -5.149000;
        $lngGuru = 119.432731;

        Carbon::setTestNow(Carbon::today()->setTime(7, 0, 0));

        // 1. Pada radius 50m, presensi ditolak
        $gagal = false;
        try {
            Presensi::checkIn($guru, $latGuru, $lngGuru);
        } catch (\RuntimeException $e) {
            $gagal = true;
            $this->assertStringContainsString('Presensi Ditolak! Posisi Anda berada di luar radius sekolah', $e->getMessage());
        }
        $this->assertTrue($gagal, 'Presensi seharusnya ditolak saat jarak melebihi 50m');

        // 2. Admin memperbesar radius menjadi 300m
        $pengaturan->update(['radius_meter' => 300]);

        // 3. Sekarang presensi berhasil direkam!
        $presensi = Presensi::checkIn($guru, $latGuru, $lngGuru);
        $this->assertNotNull($presensi);
        $this->assertEquals('hadir', $presensi->status_kehadiran);

        Carbon::setTestNow();
    }

    public function test_approval_pengajuan_izin_otomatis_mencatat_presensi(): void
    {
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);

        $guruUser = User::factory()->create();
        $adminUser = User::factory()->create();

        $guru = Guru::create([
            'user_id' => $guruUser->id,
            'shift_id' => $shift->id,
            'nama' => 'Dewi Lestari, S.Pd',
            'status_kepegawaian' => 'pns',
            'aktif' => true,
        ]);

        $izin = PengajuanIzin::create([
            'guru_id' => $guru->id,
            'jenis' => 'sakit',
            'tanggal_mulai' => Carbon::today()->toDateString(),
            'tanggal_selesai' => Carbon::today()->toDateString(),
            'alasan' => 'Demam dan flu berat',
            'status' => 'menunggu',
        ]);

        $izin->setujui($adminUser, 'Disetujui beristirahat');

        $this->assertEquals('disetujui', $izin->status);

        $presensi = Presensi::where('guru_id', $guru->id)
            ->whereDate('tanggal', Carbon::today())
            ->first();

        $this->assertNotNull($presensi);
        $this->assertEquals('sakit', $presensi->status_kehadiran);
    }

    public function test_perintah_tutup_harian_menandai_guru_tidak_hadir_sebagai_alpa(): void
    {
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);

        $guru = Guru::create([
            'shift_id' => $shift->id,
            'nama' => 'Guru Tanpa Hadir',
            'status_kepegawaian' => 'non_pns',
            'aktif' => true,
        ]);

        $tanggalKerja = Carbon::parse('2026-09-14'); // Hari Senin (Hari Kerja Efektif)
        $this->artisan('presensi:tutup-harian', ['--tanggal' => $tanggalKerja->toDateString()])
            ->assertExitCode(0);

        $presensi = Presensi::where('guru_id', $guru->id)
            ->whereDate('tanggal', $tanggalKerja)
            ->first();

        $this->assertNotNull($presensi);
        $this->assertEquals('alpa', $presensi->status_kehadiran);
    }

    public function test_rekap_whatsapp_dilewati_pada_hari_libur(): void
    {
        HariLibur::create([
            'nama' => 'Hari Libur Uji Coba',
            'tanggal_mulai' => Carbon::today()->toDateString(),
            'tanggal_selesai' => Carbon::today()->toDateString(),
            'is_libur_nasional' => true,
        ]);

        PengaturanSekolah::first()->update([
            'notif_terlambat_aktif' => true,
            'no_wa_kepala_sekolah' => '08123456789',
            'wa_api_token' => 'dummy_token',
        ]);

        $this->artisan('presensi:kirim-rekap-wa', ['--tanggal' => Carbon::today()->toDateString()])
            ->assertExitCode(0);
    }

    public function test_guru_bisa_batalkan_pengajuan_izin_status_menunggu(): void
    {
        $guruUser = User::factory()->create();
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);

        $guru = Guru::create([
            'user_id' => $guruUser->id,
            'shift_id' => $shift->id,
            'nama' => 'Guru Pengaju Izin',
            'status_kepegawaian' => 'pns',
            'aktif' => true,
        ]);

        $izin = PengajuanIzin::create([
            'guru_id' => $guru->id,
            'jenis' => 'izin',
            'tanggal_mulai' => Carbon::today()->toDateString(),
            'tanggal_selesai' => Carbon::today()->toDateString(),
            'alasan' => 'Urusan keluarga',
            'status' => 'menunggu',
        ]);

        $this->assertDatabaseHas('pengajuan_izins', ['id' => $izin->id]);

        $izin->delete();

        $this->assertDatabaseMissing('pengajuan_izins', ['id' => $izin->id]);
    }

    public function test_halaman_presensi_saya_dapat_dirender_tanpa_error(): void
    {
        $role = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);
        $user = User::factory()->create();
        $user->assignRole($role);
        Guru::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'nama' => 'Guru Testing',
            'status_kepegawaian' => 'pns',
            'aktif' => true,
        ]);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('guru'));

        Livewire::test(PresensiSaya::class)
            ->assertSuccessful()
            ->assertSee('Presensi Mandiri Guru')
            ->assertSee('Guru Testing');

        $response = $this->get(PresensiSaya::getUrl());
        $response->assertStatus(200);
        $response->assertSee('Presensi Mandiri Guru');
        $response->assertSee('pg-leaflet-map');
        $response->assertSee('leaflet.css');
        $response->assertSee('leaflet.js');
        $response->assertSee('ensureLeaflet');
    }

    public function test_halaman_riwayat_presensi_guru_dapat_dirender(): void
    {
        $role = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);
        $user = User::factory()->create();
        $user->assignRole($role);
        Guru::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'nama' => 'Guru Riwayat Test',
            'status_kepegawaian' => 'pns',
            'aktif' => true,
        ]);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('guru'));

        Livewire::test(RiwayatPresensiGuruPage::class)
            ->assertSuccessful();

        $response = $this->get(RiwayatPresensiGuruPage::getUrl());
        $response->assertStatus(200);
    }

    public function test_halaman_laporan_presensi_admin_dapat_dirender(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'page_LaporanPresensiPage', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(LaporanPresensiPage::class)
            ->assertSuccessful();

        $response = $this->get('/sekolahku/panel/laporan-presensi-page');
        $response->assertStatus(200);
    }

    public function test_halaman_pengajuan_izin_guru_dapat_dirender(): void
    {
        $role = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);
        $user = User::factory()->create();
        $user->assignRole($role);
        Guru::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'nama' => 'Guru Izin Test',
            'status_kepegawaian' => 'pns',
            'aktif' => true,
        ]);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('guru'));

        Livewire::test(PengajuanIzinGuruPage::class)
            ->assertSuccessful();

        $response = $this->get(PengajuanIzinGuruPage::getUrl());
        $response->assertStatus(200);
    }

    public function test_halaman_pengaturan_sekolah_admin_dapat_dirender(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'page_PengaturanSekolahPage', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(PengaturanSekolahPage::class)
            ->assertSuccessful();

        $response = $this->get('/sekolahku/panel/pengaturan-sekolah-page');
        $response->assertStatus(200);
    }

    public function test_halaman_dashboard_guru_dan_widget_dapat_dirender(): void
    {
        $role = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);
        $user = User::factory()->create();
        $user->assignRole($role);
        $guru = Guru::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'nama' => 'Guru Dashboard Test',
            'status_kepegawaian' => 'pns',
            'aktif' => true,
        ]);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('guru'));

        Livewire::test(GuruStatusPresensiHariIniWidget::class)
            ->assertSuccessful();

        Livewire::test(GuruStatistikRingkasanWidget::class)
            ->assertSuccessful();

        Livewire::test(GuruRiwayatTerbaruWidget::class)
            ->assertSuccessful();

        $response = $this->get('/guru');
        $response->assertStatus(200);
        $response->assertSee('Portal Guru & Presensi');
    }

    public function test_admin_dashboard_dan_semua_widget_dapat_dirender(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(AdminExecutiveOverviewWidget::class)
            ->assertSuccessful()
            ->assertSee('PANEL UTAMA ADMINISTRATOR')
            ->call('kirimWaRekap')
            ->call('tutupPresensiHariIni');

        Livewire::test(AnomaliPresensiWidget::class)
            ->assertSuccessful();

        Livewire::test(PresensiTrenChartWidget::class)
            ->assertSuccessful();

        Livewire::test(PresensiStatistikChartWidget::class)
            ->assertSuccessful();

        Livewire::test(GuruTerbaruPresensiWidget::class)
            ->assertSuccessful();

        Livewire::test(LeaderboardDisiplinWidget::class)
            ->assertSuccessful();

        Livewire::test(PeringatanKeterlambatanWidget::class)
            ->assertSuccessful();

        $response = $this->get('/sekolahku/panel');
        $response->assertStatus(200);
    }

    public function test_riwayat_presensi_guru_mencakup_dinas_luar_dalam_rekap_dan_persentase(): void
    {
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);

        $guru = Guru::create([
            'nama' => 'Guru Dinas',
            'nip' => '198701012015011003',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'pns',
            'shift_id' => $shift->id,
            'aktif' => true,
        ]);

        $user = $guru->user;
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('guru'));

        // Buat 1 presensi dinas_luar dan 1 presensi hadir pada bulan ini
        $now = Carbon::now();
        Presensi::create([
            'guru_id' => $guru->id,
            'shift_id' => $shift->id,
            'tanggal' => $now->copy()->startOfMonth()->toDateString(),
            'jam_masuk' => '07:00:00',
            'status_kehadiran' => 'dinas_luar',
            'status_masuk' => 'tepat_waktu',
        ]);

        Presensi::create([
            'guru_id' => $guru->id,
            'shift_id' => $shift->id,
            'tanggal' => $now->copy()->startOfMonth()->addDays(1)->toDateString(),
            'jam_masuk' => '07:00:00',
            'status_kehadiran' => 'hadir',
            'status_masuk' => 'tepat_waktu',
        ]);

        $page = new RiwayatPresensiGuruPage;
        $page->mount();
        $page->bulan = $now->month;
        $page->tahun = $now->year;
        $rekap = $page->getRekapBulananProperty();

        $this->assertEquals(1, $rekap['total_dinas_luar']);
        $this->assertEquals(2, $rekap['total_hadir']); // 1 hadir + 1 dinas luar

        Livewire::test(RiwayatPresensiGuruPage::class)
            ->assertSuccessful()
            ->assertSee('Dinas Luar');
    }

    public function test_live_display_mengurangi_alpa_dari_belum_hadir_dan_menghitung_dinas_luar(): void
    {
        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);

        $g1 = Guru::create(['nama' => 'Guru 1', 'nip' => '111', 'status_kepegawaian' => 'pns', 'shift_id' => $shift->id, 'aktif' => true]);
        $g2 = Guru::create(['nama' => 'Guru 2', 'nip' => '222', 'status_kepegawaian' => 'pns', 'shift_id' => $shift->id, 'aktif' => true]);
        $g3 = Guru::create(['nama' => 'Guru 3', 'nip' => '333', 'status_kepegawaian' => 'pns', 'shift_id' => $shift->id, 'aktif' => true]);
        $g4 = Guru::create(['nama' => 'Guru 4', 'nip' => '444', 'status_kepegawaian' => 'pns', 'shift_id' => $shift->id, 'aktif' => true]);

        $today = Carbon::today()->toDateString();
        // g1: hadir
        Presensi::create(['guru_id' => $g1->id, 'shift_id' => $shift->id, 'tanggal' => $today, 'jam_masuk' => '07:00:00', 'status_kehadiran' => 'hadir', 'status_masuk' => 'tepat_waktu']);
        // g2: dinas luar
        Presensi::create(['guru_id' => $g2->id, 'shift_id' => $shift->id, 'tanggal' => $today, 'jam_masuk' => '07:00:00', 'status_kehadiran' => 'dinas_luar', 'status_masuk' => 'tepat_waktu']);
        // g3: alpa
        Presensi::create(['guru_id' => $g3->id, 'shift_id' => $shift->id, 'tanggal' => $today, 'status_kehadiran' => 'alpa']);
        // g4: belum hadir (tanpa record)

        $response = $this->get('/sekolahku/live-display');
        $response->assertStatus(200)
            ->assertViewHas('totalMasuk', 2)
            ->assertViewHas('dinasLuar', 1)
            ->assertViewHas('belumHadir', 1);

        $json = $this->getJson('/sekolahku/live-display/feed');
        $json->assertStatus(200)
            ->assertJson([
                'dinasLuar' => 1,
                'belumHadir' => 1,
            ]);
    }

    public function test_command_kirim_rekap_wa_mencakup_dinas_luar(): void
    {
        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);

        $setting = PengaturanSekolah::getSetting();
        $setting->update([
            'notif_terlambat_aktif' => true,
            'no_wa_kepala_sekolah' => '08123456789',
            'wa_api_token' => 'dummy_token',
            'wa_provider' => 'fonnte',
        ]);

        $shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);

        $g1 = Guru::create(['nama' => 'Guru Hadir', 'nip' => '555', 'status_kepegawaian' => 'pns', 'shift_id' => $shift->id, 'aktif' => true]);
        $g2 = Guru::create(['nama' => 'Guru Dinas', 'nip' => '666', 'status_kepegawaian' => 'pns', 'shift_id' => $shift->id, 'aktif' => true]);

        $today = Carbon::parse('2026-09-14')->toDateString(); // Hari Senin (Hari Kerja Efektif)
        Presensi::create(['guru_id' => $g1->id, 'shift_id' => $shift->id, 'tanggal' => $today, 'jam_masuk' => '07:00:00', 'status_kehadiran' => 'hadir', 'status_masuk' => 'tepat_waktu']);
        Presensi::create(['guru_id' => $g2->id, 'shift_id' => $shift->id, 'tanggal' => $today, 'jam_masuk' => '07:00:00', 'status_kehadiran' => 'dinas_luar', 'status_masuk' => 'tepat_waktu']);

        $this->artisan('presensi:kirim-rekap-wa', ['--tanggal' => $today])
            ->assertSuccessful();

        Http::assertSent(function ($request) {
            $message = $request['message'] ?? '';

            return str_contains($message, 'Tugas Luar / Dinas Luar: 1 orang')
                && str_contains($message, 'Persentase Kehadiran:* 100%');
        });
    }
}
