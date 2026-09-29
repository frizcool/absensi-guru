<?php

namespace Tests\Feature;

use App\Filament\Pages\LaporanPresensiPage;
use App\Models\Guru;
use App\Models\Presensi;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LaporanPresensiTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $kepsek;

    protected Shift $shift;

    protected Guru $guru;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $kepsekRole = Role::firstOrCreate(['name' => 'kepala_sekolah', 'guard_name' => 'web']);
        $guruRole = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);

        $viewLaporanPermission = Permission::firstOrCreate(['name' => 'View:LaporanPresensiPage', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo($viewLaporanPermission);
        $kepsekRole->givePermissionTo($viewLaporanPermission);

        // Super Admin
        $this->superAdmin = User::factory()->create(['email' => 'super@test.com']);
        $this->superAdmin->assignRole($superAdminRole);

        // Admin
        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        $this->admin->assignRole($adminRole);

        // Kepsek
        $this->kepsek = User::factory()->create(['email' => 'kepsek@test.com']);
        $this->kepsek->assignRole($kepsekRole);

        // Data Master
        $this->shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);

        $this->guru = Guru::create([
            'nama' => 'Budi Santoso, S.Pd',
            'nip' => '198501012010011001',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'pns',
            'shift_id' => $this->shift->id,
            'aktif' => true,
        ]);
    }

    public function test_super_admin_can_access_laporan_presensi(): void
    {
        $this->actingAs($this->superAdmin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->get('/sekolahku/panel/laporan-presensi-page')
            ->assertStatus(200);
    }

    public function test_kepala_sekolah_can_access_laporan_presensi(): void
    {
        $this->actingAs($this->kepsek);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->get('/sekolahku/panel/laporan-presensi-page')
            ->assertStatus(200);
    }

    public function test_admin_can_access_laporan_presensi(): void
    {
        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->get('/sekolahku/panel/laporan-presensi-page')
            ->assertStatus(200);
    }

    public function test_export_excel_can_be_downloaded(): void
    {
        $this->actingAs($this->superAdmin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(LaporanPresensiPage::class)
            ->call('exportExcel')
            ->assertFileDownloaded();
    }

    public function test_cetak_kedinasan_pdf_route_accessible(): void
    {
        $this->actingAs($this->superAdmin);

        $this->get(route('laporan.cetak-bulanan', [
            'bulan' => 9,
            'tahun' => 2026,
            'status_kepegawaian' => 'semua',
        ]))->assertStatus(200)
            ->assertSee('SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK');
    }

    public function test_filtering_and_search_and_reset(): void
    {
        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $shiftSiang = Shift::create([
            'nama' => 'Shift Siang',
            'jam_masuk' => '13:00:00',
            'jam_pulang' => '18:00:00',
            'toleransi_menit' => 10,
        ]);

        $guruHonorer = Guru::create([
            'nama' => 'Dewi Sartika, S.Kom',
            'nip' => null,
            'nuptk' => '9876543210',
            'jenis_kelamin' => 'P',
            'status_kepegawaian' => 'non_pns',
            'shift_id' => $shiftSiang->id,
            'aktif' => true,
        ]);

        Livewire::test(LaporanPresensiPage::class)
            ->assertSee('Budi Santoso, S.Pd')
            ->assertSee('Dewi Sartika, S.Kom')
            // Filter status non_pns
            ->set('statusKepegawaian', 'non_pns')
            ->assertSee('Dewi Sartika, S.Kom')
            ->assertDontSee('Budi Santoso, S.Pd')
            // Filter shift
            ->set('statusKepegawaian', 'semua')
            ->set('shiftId', $this->shift->id)
            ->assertSee('Budi Santoso, S.Pd')
            ->assertDontSee('Dewi Sartika, S.Kom')
            // Search
            ->set('shiftId', null)
            ->set('search', 'Dewi')
            ->assertSee('Dewi Sartika, S.Kom')
            ->assertDontSee('Budi Santoso, S.Pd')
            // Reset filter
            ->call('resetFilter')
            ->assertSet('search', '')
            ->assertSet('statusKepegawaian', 'semua')
            ->assertSet('shiftId', null)
            ->assertSee('Budi Santoso, S.Pd')
            ->assertSee('Dewi Sartika, S.Kom');
    }

    public function test_presence_matrix_calculation_and_tooltips(): void
    {
        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        // Buat data presensi hadir
        Presensi::create([
            'guru_id' => $this->guru->id,
            'shift_id' => $this->shift->id,
            'tanggal' => Carbon::create(2026, 9, 1)->toDateString(),
            'jam_masuk' => Carbon::create(2026, 9, 1, 6, 55),
            'jam_pulang' => Carbon::create(2026, 9, 1, 14, 5),
            'status_masuk' => 'tepat_waktu',
            'status_kehadiran' => 'hadir',
        ]);

        // Buat data presensi terlambat
        Presensi::create([
            'guru_id' => $this->guru->id,
            'shift_id' => $this->shift->id,
            'tanggal' => Carbon::create(2026, 9, 2)->toDateString(),
            'jam_masuk' => Carbon::create(2026, 9, 2, 7, 25),
            'jam_pulang' => Carbon::create(2026, 9, 2, 14, 0),
            'status_masuk' => 'terlambat',
            'status_kehadiran' => 'hadir',
        ]);

        Livewire::test(LaporanPresensiPage::class)
            ->set('bulan', 9)
            ->set('tahun', 2026)
            ->assertSeeHtml('badge-H')
            ->assertSeeHtml('badge-T')
            ->assertSeeHtml('Hadir Tepat Waktu')
            ->assertSeeHtml('Terlambat');
    }

    public function test_rincian_presensi_shows_filtered_daily_entry_and_exit_times(): void
    {
        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Presensi::create([
            'guru_id' => $this->guru->id,
            'shift_id' => $this->shift->id,
            'tanggal' => '2026-09-01',
            'jam_masuk' => '2026-09-01 06:55:00',
            'jam_pulang' => '2026-09-01 14:05:00',
            'status_masuk' => 'tepat_waktu',
            'status_kehadiran' => 'hadir',
        ]);

        Presensi::create([
            'guru_id' => $this->guru->id,
            'shift_id' => $this->shift->id,
            'tanggal' => '2026-10-01',
            'jam_masuk' => '2026-10-01 07:10:00',
            'status_masuk' => 'terlambat',
            'status_kehadiran' => 'hadir',
        ]);

        $this->get('/sekolahku/panel/rincian-presensi-page?bulan=9&tahun=2026&statusKepegawaian=pns&shiftId='.$this->shift->id.'&search=Budi')
            ->assertOk()
            ->assertSee('Budi Santoso, S.Pd')
            ->assertSee('06:55')
            ->assertSee('14:05')
            ->assertDontSee('07:10');
    }

    public function test_matrix_links_to_details_with_active_filters(): void
    {
        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(LaporanPresensiPage::class)
            ->set('bulan', 9)
            ->set('tahun', 2026)
            ->set('statusKepegawaian', 'pns')
            ->set('shiftId', $this->shift->id)
            ->set('search', 'Budi')
            ->assertSee('Rincian Jam Harian')
            ->assertSee('bulan=9')
            ->assertSee('tahun=2026')
            ->assertSee('statusKepegawaian=pns')
            ->assertSee('shiftId='.$this->shift->id)
            ->assertSee('search=Budi');
    }

    public function test_print_view_includes_all_filtered_rows_beyond_the_current_page(): void
    {
        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $guruKedua = Guru::create([
            'nama' => 'Guru Kedua',
            'nip' => '198501012010011002',
            'jenis_kelamin' => 'P',
            'status_kepegawaian' => 'pns',
            'shift_id' => $this->shift->id,
            'aktif' => true,
        ]);

        for ($hari = 1; $hari <= 30; $hari++) {
            foreach ([$this->guru, $guruKedua] as $guru) {
                Presensi::create([
                    'guru_id' => $guru->id,
                    'shift_id' => $this->shift->id,
                    'tanggal' => sprintf('2026-09-%02d', $hari),
                    'status_kehadiran' => 'hadir',
                ]);
            }
        }

        $this->assertSame(60, Presensi::query()->count());
        $this->assertSame(60, Presensi::query()
            ->whereDate('tanggal', '>=', '2026-09-01')
            ->whereDate('tanggal', '<=', '2026-09-30')
            ->count());
        $this->assertSame(60, Presensi::query()->whereHas('guru', fn ($query) => $query->where('aktif', true))->count());
        $this->assertSame(60, Presensi::query()->whereHas('guru', fn ($query) => $query->where('status_kepegawaian', 'pns'))->count());

        $response = $this->get('/sekolahku/panel/rincian-presensi-page?bulan=9&tahun=2026&statusKepegawaian=pns&cetak=1');

        $response->assertOk();
        $this->assertSame(60, substr_count($response->getContent(), 'class="detail-row"'));
    }
}
