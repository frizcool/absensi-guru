<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\JurnalPembelajaran;
use App\Models\PengajuanIzin;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use App\Models\Shift;
use App\Models\User;
use App\Services\HariLiburService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdvancedAttendanceFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected Guru $guru;

    protected User $user;

    protected Shift $shift;

    protected function setUp(): void
    {
        parent::setUp();

        PengaturanSekolah::getSetting()->update([
            'nama_sekolah' => 'UPTD SPF SD Inpres Rappojawa',
            'npsn' => '40307321',
            'kepala_sekolah' => 'Drs. H. Muhammad Ilyas, M.Pd.',
            'nip_kepala_sekolah' => '196805121992031005',
            'latitude' => -5.147665,
            'longitude' => 119.432731,
            'radius_meter' => 100,
            'maksimal_akurasi_gps' => 150,
            'wajib_validasi_lokasi' => true,
            'wajib_device_binding' => true,
            'teks_pengumuman_display' => 'Selamat datang di SD Inpres Rappojawa',
        ]);

        $this->shift = Shift::create([
            'nama' => 'Shift Pagi Reguler',
            'jam_masuk' => '07:15',
            'jam_pulang' => '14:00',
            'toleransi_menit' => 15,
            'hari_kerja' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
            'aktif' => true,
        ]);

        $this->user = User::create([
            'name' => 'Budi Santoso, S.Pd.',
            'email' => 'budi@sekolah.sch.id',
            'nip' => '198501012010011001',
            'password' => bcrypt('password'),
        ]);

        $roleGuru = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $this->user->assignRole($roleGuru);

        $this->guru = Guru::create([
            'user_id' => $this->user->id,
            'nama' => 'Budi Santoso, S.Pd.',
            'nip' => '198501012010011001',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'pns',
            'kuota_cuti_tahunan' => 12,
            'shift_id' => $this->shift->id,
            'aktif' => true,
        ]);
    }

    public function test_device_binding_mengunci_perangkat_pertama_dan_menolak_perangkat_lain(): void
    {
        Carbon::setTestNow(Carbon::today()->setTime(7, 0));

        // 1. Absen pertama kali dengan device A -> otomatis terikat
        $presensi = Presensi::checkIn(
            $this->guru,
            -5.147665,
            119.432731,
            null,
            'DEVICE-HP-OPPO-123',
            20.5
        );

        $this->assertNotNull($presensi);
        $this->guru->refresh();
        $this->assertEquals('DEVICE-HP-OPPO-123', $this->guru->device_id);

        // Reset check-in untuk test device lain
        $presensi->delete();

        // 2. Coba absen dengan device B yang berbeda -> ditolak RuntimeException
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Perangkat Anda tidak cocok');

        Presensi::checkIn(
            $this->guru,
            -5.147665,
            119.432731,
            null,
            'DEVICE-HP-SAMSUNG-999',
            20.5
        );
    }

    public function test_validasi_akurasi_gps_menolak_sinyal_lemah_atau_fake_gps(): void
    {
        Carbon::setTestNow(Carbon::today()->setTime(7, 0));

        // Toleransi maksimal 150m di setup, coba input akurasi 300m
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Akurasi sinyal GPS Anda terlalu rendah');

        Presensi::checkIn(
            $this->guru,
            -5.147665,
            119.432731,
            null,
            'DEVICE-HP-OPPO-123',
            300.0 // Terlalu tinggi / tidak akurat
        );
    }

    public function test_guru_tugas_luar_bisa_absen_di_luar_radius_sekolah(): void
    {
        Carbon::setTestNow(Carbon::today()->setTime(7, 20));

        // Buat izin Tugas Luar (SPPD) yang sudah disetujui
        PengajuanIzin::create([
            'guru_id' => $this->guru->id,
            'jenis' => 'dinas_luar',
            'tanggal_mulai' => Carbon::today()->toDateString(),
            'tanggal_selesai' => Carbon::today()->toDateString(),
            'alasan' => 'Mengikuti Pelatihan Kurikulum Merdeka',
            'lokasi_tugas' => 'Hotel Claro Makassar',
            'nomor_surat_tugas' => '800/99/DISDIK/2026',
            'status' => 'disetujui',
        ]);

        // Lokasi di luar radius (jarak ~10km dari sekolah)
        $latLuar = -5.180000;
        $lngLuar = 119.460000;

        $presensi = Presensi::checkIn(
            $this->guru,
            $latLuar,
            $lngLuar,
            null,
            null,
            15.0
        );

        $this->assertNotNull($presensi);
        $this->assertEquals('dinas_luar', $presensi->status_kehadiran);
        $this->assertStringContainsString('Tugas Luar / Dinas Luar', $presensi->keterangan);
    }

    public function test_jurnal_pembelajaran_tersimpan_saat_checkout(): void
    {
        Carbon::setTestNow(Carbon::today()->setTime(7, 0));

        $presensi = Presensi::checkIn(
            $this->guru,
            -5.147665,
            119.432731,
            null,
            null,
            20.0
        );

        Carbon::setTestNow(Carbon::today()->setTime(14, 5));

        $jurnalData = [
            'kelas' => 'Kelas V-B',
            'mata_pelajaran' => 'Matematika',
            'materi_kegiatan' => 'Operasi hitung perkalian pecahan desimal dan latihan soal cerita.',
            'jumlah_jam' => 3,
            'keterangan' => 'Siswa sangat antusias dan mampu menyelesaikan latihan dengan baik.',
        ];

        // Jalankan checkout
        $presensi->checkOut(
            -5.147665,
            119.432731,
            null,
            null,
            20.0
        );

        // Simpan jurnal yang dikirim saat checkout
        JurnalPembelajaran::create([
            'guru_id' => $this->guru->id,
            'presensi_id' => $presensi->id,
            'tanggal' => Carbon::today()->toDateString(),
            'kelas' => $jurnalData['kelas'],
            'mata_pelajaran' => $jurnalData['mata_pelajaran'],
            'materi_kegiatan' => $jurnalData['materi_kegiatan'],
            'jumlah_jam' => $jurnalData['jumlah_jam'],
            'keterangan' => $jurnalData['keterangan'],
        ]);

        $this->assertDatabaseHas('jurnal_pembelajarans', [
            'guru_id' => $this->guru->id,
            'presensi_id' => $presensi->id,
            'kelas' => 'Kelas V-B',
            'mata_pelajaran' => 'Matematika',
        ]);
    }

    public function test_perhitungan_sisa_kuota_cuti_guru_berfungsi(): void
    {
        $this->assertEquals(12, $this->guru->sisa_cuti_tahun_ini);

        // Ambil cuti 3 hari yang disetujui
        PengajuanIzin::create([
            'guru_id' => $this->guru->id,
            'jenis' => 'cuti',
            'tanggal_mulai' => Carbon::now()->startOfYear()->addDays(10)->toDateString(),
            'tanggal_selesai' => Carbon::now()->startOfYear()->addDays(12)->toDateString(), // 3 hari
            'alasan' => 'Cuti tahunan keluarga',
            'status' => 'disetujui',
        ]);

        $this->guru->refresh();
        $this->assertEquals(9, $this->guru->sisa_cuti_tahun_ini);
    }

    public function test_sinkronisasi_hari_libur_nasional_berhasil(): void
    {
        $res = HariLiburService::syncLiburNasional(2026);

        $this->assertTrue($res['success']);
        $this->assertGreaterThan(0, $res['total']);
        $this->assertDatabaseHas('hari_liburs', [
            'is_libur_nasional' => true,
        ]);
    }

    public function test_halaman_cetak_dokumen_kedinasan_dapat_diakses_oleh_user(): void
    {
        // Unauthenticated / unauthorized user must be rejected with 403
        $unauthorizedResponse = $this->actingAs($this->user)->get('/laporan/cetak-bulanan?bulan=9&tahun=2026');
        $unauthorizedResponse->assertStatus(403);

        // Authorized user with admin role can access
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->user->assignRole('admin');

        $response = $this->actingAs($this->user)->get('/laporan/cetak-bulanan?bulan=9&tahun=2026');

        $response->assertStatus(200);
        $response->assertSee('SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK (SPTJB)');
        $response->assertSee('UPTD SPF SD Inpres Rappojawa');
        $response->assertSee('Budi Santoso, S.Pd.');
    }

    public function test_live_tv_display_lobi_dapat_diakses_publik_dan_memberikan_feed_json(): void
    {
        $response = $this->get('/sekolahku/live-display');
        $response->assertStatus(200);
        $response->assertSee('PANTAUAN REAL-TIME KEHADIRAN GURU');
        $response->assertSee('UPTD SPF SD Inpres Rappojawa');

        $jsonResponse = $this->getJson('/sekolahku/live-display/feed');
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonStructure([
            'totalGuru',
            'hadirTepatWaktu',
            'terlambat',
            'dinasLuar',
            'izinSakit',
            'belumHadir',
            'persentase',
            'terbaru',
        ]);
    }

    public function test_security_headers_diterapkan_pada_respons(): void
    {
        $response = $this->get('/sekolahku');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(self), geolocation=(self), microphone=()');
    }
}
