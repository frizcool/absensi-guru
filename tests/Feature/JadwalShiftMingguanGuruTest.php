<?php

namespace Tests\Feature;

use App\Filament\Resources\JadwalShiftResource\Pages\ListJadwalShifts;
use App\Models\Guru;
use App\Models\JadwalShift;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class JadwalShiftMingguanGuruTest extends TestCase
{
    use RefreshDatabase;

    protected Shift $shiftFullDay;

    protected Shift $shiftPagi;

    protected Shift $shiftSore;

    protected Guru $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shiftFullDay = Shift::create([
            'nama' => 'Full Day',
            'jam_masuk' => '07:30:00',
            'jam_pulang' => '15:30:00',
            'toleransi_menit' => 15,
        ]);

        $this->shiftPagi = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '12:30:00',
            'toleransi_menit' => 15,
        ]);

        $this->shiftSore = Shift::create([
            'nama' => 'Shift Sore',
            'jam_masuk' => '12:30:00',
            'jam_pulang' => '17:00:00',
            'toleransi_menit' => 15,
        ]);

        $user = User::factory()->create();

        $this->guru = Guru::create([
            'user_id' => $user->id,
            'nama' => 'Guru A',
            'nip' => '198501012010011001',
            'status_kepegawaian' => 'pns',
            'shift_id' => $this->shiftFullDay->id,
            'aktif' => true,
        ]);
    }

    public function test_guru_menggunakan_shift_default_jika_belum_ada_pola_mingguan(): void
    {
        // 2026-09-22 adalah hari Selasa
        $shift = $this->guru->shiftPadaTanggal('2026-09-22');

        $this->assertNotNull($shift);
        $this->assertEquals($this->shiftFullDay->id, $shift->id);
    }

    public function test_guru_shift_pagi_pada_hari_selasa_dan_rabu_selain_itu_full_day(): void
    {
        // Atur Guru A: Selasa & Rabu shift pagi, selain itu ikuti default (Full Day)
        $this->guru->update([
            'jadwal_mingguan' => [
                'selasa' => (string) $this->shiftPagi->id,
                'rabu' => (string) $this->shiftPagi->id,
            ],
        ]);

        // 2026-09-21 = Senin
        $shiftSenin = $this->guru->shiftPadaTanggal('2026-09-21');
        $this->assertEquals($this->shiftFullDay->id, $shiftSenin->id);

        // 2026-09-22 = Selasa -> Harus Shift Pagi
        $shiftSelasa = $this->guru->shiftPadaTanggal('2026-09-22');
        $this->assertEquals($this->shiftPagi->id, $shiftSelasa->id);
        $this->assertEquals('Shift Pagi', $shiftSelasa->nama);

        // 2026-09-23 = Rabu -> Harus Shift Pagi
        $shiftRabu = $this->guru->shiftPadaTanggal('2026-09-23');
        $this->assertEquals($this->shiftPagi->id, $shiftRabu->id);

        // 2026-09-24 = Kamis -> Harus Full Day
        $shiftKamis = $this->guru->shiftPadaTanggal('2026-09-24');
        $this->assertEquals($this->shiftFullDay->id, $shiftKamis->id);

        // 2026-09-25 = Jumat -> Harus Full Day
        $shiftJumat = $this->guru->shiftPadaTanggal('2026-09-25');
        $this->assertEquals($this->shiftFullDay->id, $shiftJumat->id);
    }

    public function test_jadwal_override_tanggal_spesifik_mengalahkan_pola_mingguan(): void
    {
        // Atur Selasa = Shift Pagi
        $this->guru->update([
            'jadwal_mingguan' => [
                'selasa' => (string) $this->shiftPagi->id,
            ],
        ]);

        // Pada tanggal 2026-09-22 (Selasa), buat override khusus di jadwal_shifts menjadi Shift Sore
        JadwalShift::create([
            'guru_id' => $this->guru->id,
            'shift_id' => $this->shiftSore->id,
            'tanggal' => '2026-09-22',
        ]);

        // Harus menggunakan Shift Sore (Override prioritas 1)
        $shift = $this->guru->shiftPadaTanggal('2026-09-22');
        $this->assertEquals($this->shiftSore->id, $shift->id);

        // Tapi pada Selasa pekan berikutnya (2026-09-29), tetap kembali ke Shift Pagi (Pola prioritas 2)
        $shiftNextWeek = $this->guru->shiftPadaTanggal('2026-09-29');
        $this->assertEquals($this->shiftPagi->id, $shiftNextWeek->id);
    }

    public function test_guru_bisa_diset_libur_rutin_pada_hari_tertentu(): void
    {
        // Guru diset libur rutin di hari Sabtu
        $this->guru->update([
            'jadwal_mingguan' => [
                'sabtu' => 'libur',
            ],
        ]);

        // 2026-09-26 = Sabtu
        $shiftSabtu = $this->guru->shiftPadaTanggal('2026-09-26');
        $this->assertNull($shiftSabtu);
        $this->assertTrue($this->guru->isLiburPadaTanggal('2026-09-26'));

        // 2026-09-25 = Jumat -> Tidak libur, dapat shift default
        $this->assertFalse($this->guru->isLiburPadaTanggal('2026-09-25'));
        $this->assertEquals($this->shiftFullDay->id, $this->guru->shiftPadaTanggal('2026-09-25')->id);
    }

    public function test_ringkasan_jadwal_mingguan_menampilkan_format_yang_sesuai(): void
    {
        $this->guru->update([
            'jadwal_mingguan' => [
                'selasa' => (string) $this->shiftPagi->id,
                'rabu' => (string) $this->shiftPagi->id,
                'minggu' => 'libur',
            ],
        ]);

        $ringkasan = $this->guru->ringkasanJadwalMingguan();

        $this->assertStringContainsString('Sel,Rab: Shift Pagi', $ringkasan);
        $this->assertStringContainsString('Min: Libur', $ringkasan);
    }

    public function test_batch_generator_menerapkan_shift_pada_hari_yang_dipilih_dalam_sebulan(): void
    {
        $startDate = Carbon::parse('2026-10-01'); // Kamis
        $endDate = Carbon::parse('2026-10-31');   // Sabtu

        $selectedDays = [2, 3]; // Selasa & Rabu
        $period = CarbonPeriod::create($startDate, $endDate);

        $tuesdayWednesdayCount = 0;
        foreach ($period as $date) {
            if (in_array($date->dayOfWeek, $selectedDays, true)) {
                JadwalShift::updateOrCreate(
                    [
                        'guru_id' => $this->guru->id,
                        'tanggal' => $date->toDateString(),
                    ],
                    [
                        'shift_id' => $this->shiftPagi->id,
                    ]
                );
                $tuesdayWednesdayCount++;
            }
        }

        $this->assertGreaterThan(0, $tuesdayWednesdayCount);

        // Verifikasi semua jadwal_shifts tercatat
        $countInDb = JadwalShift::where('guru_id', $this->guru->id)
            ->where('shift_id', $this->shiftPagi->id)
            ->whereBetween('tanggal', ['2026-10-01', '2026-10-31'])
            ->count();

        $this->assertEquals($tuesdayWednesdayCount, $countInDb);
    }

    public function test_halaman_list_jadwal_shifts_dapat_diakses_tanpa_error(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $adminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $adminUser = User::factory()->create();
        $adminUser->assignRole($adminRole);

        Livewire::actingAs($adminUser)
            ->test(ListJadwalShifts::class)
            ->assertSuccessful();
    }
}
