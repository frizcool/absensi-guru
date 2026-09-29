<?php

namespace Tests\Feature;

use App\Exports\LaporanPresensiBulananExport;
use App\Exports\LaporanRincianPresensiExport;
use App\Models\Guru;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use App\Models\Shift;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSpout\Reader\XLSX\Reader;
use Tests\TestCase;

class ExcelExportTest extends TestCase
{
    use RefreshDatabase;

    protected Shift $shift;

    protected Guru $guru;

    protected function setUp(): void
    {
        parent::setUp();

        PengaturanSekolah::query()->updateOrCreate([], [
            'nama_sekolah' => 'UPTD SPF SD Inpres Rappojawa',
            'alamat' => 'Jl. Rappojawa No. 10',
            'npsn' => '40307000',
            'telepon' => '0411-123456',
            'kepala_sekolah' => 'Dra. Hj. Maryam, M.Pd',
            'nip_kepala_sekolah' => '196512311990032001',
        ]);

        $this->shift = Shift::create([
            'nama' => 'Shift Pagi',
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '14:00:00',
            'toleransi_menit' => 15,
        ]);

        $this->guru = Guru::create([
            'nama' => 'Ahmad Dahlan, S.Pd',
            'nip' => '198801012015011002',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'pns',
            'shift_id' => $this->shift->id,
            'aktif' => true,
        ]);

        Presensi::create([
            'guru_id' => $this->guru->id,
            'shift_id' => $this->shift->id,
            'tanggal' => '2026-09-01',
            'jam_masuk' => '2026-09-01 06:45:00',
            'jam_pulang' => '2026-09-01 14:15:00',
            'status_masuk' => 'tepat_waktu',
            'status_pulang' => 'normal',
            'status_kehadiran' => 'hadir',
            'keterangan' => 'Hadir tepat waktu',
        ]);
    }

    public function test_laporan_presensi_bulanan_export_generates_valid_excel_structure(): void
    {
        $export = new LaporanPresensiBulananExport(
            bulan: 9,
            tahun: 2026,
            statusKepegawaian: 'semua',
        );

        $tempFile = tempnam(sys_get_temp_dir(), 'test_rekap_').'.xlsx';
        $export->exportToFile($tempFile);

        $this->assertFileExists($tempFile);
        $this->assertGreaterThan(1000, filesize($tempFile));

        $reader = new Reader;
        $reader->open($tempFile);

        $rows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = $row->toArray();
            }
        }
        $reader->close();
        unlink($tempFile);

        $this->assertNotEmpty($rows);

        // Check header KOP
        $allValuesFlat = array_merge(...$rows);
        $hasNamaSekolah = false;
        $hasGuruName = false;
        $hasTotalRekap = false;

        foreach ($allValuesFlat as $val) {
            if (is_string($val) && str_contains($val, 'UPTD SPF SD INPRES RAPPOJAWA')) {
                $hasNamaSekolah = true;
            }
            if (is_string($val) && str_contains($val, 'Ahmad Dahlan, S.Pd')) {
                $hasGuruName = true;
            }
            if (is_string($val) && str_contains($val, 'TOTAL REKAPITULASI')) {
                $hasTotalRekap = true;
            }
        }

        $this->assertTrue($hasNamaSekolah, 'Nama sekolah harus ada dalam export Excel rekap bulanan.');
        $this->assertTrue($hasGuruName, 'Nama guru harus ada dalam export Excel rekap bulanan.');
        $this->assertTrue($hasTotalRekap, 'Baris TOTAL REKAPITULASI harus ada dalam export Excel rekap bulanan.');
    }

    public function test_laporan_rincian_presensi_export_generates_valid_excel_structure(): void
    {
        $export = new LaporanRincianPresensiExport(
            bulan: 9,
            tahun: 2026,
            statusKepegawaian: 'semua',
        );

        $tempFile = tempnam(sys_get_temp_dir(), 'test_rincian_').'.xlsx';
        $export->exportToFile($tempFile);

        $this->assertFileExists($tempFile);
        $this->assertGreaterThan(1000, filesize($tempFile));

        $reader = new Reader;
        $reader->open($tempFile);

        $rows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = $row->toArray();
            }
        }
        $reader->close();
        unlink($tempFile);

        $this->assertNotEmpty($rows);

        $allValuesFlat = array_merge(...$rows);
        $hasJudulRincian = false;
        $hasGuruName = false;
        $hasJamMasuk = false;
        $hasRingkasan = false;

        foreach ($allValuesFlat as $val) {
            if (is_string($val) && str_contains($val, 'LAPORAN RINCIAN PRESENSI & JAM KERJA HARIAN GURU')) {
                $hasJudulRincian = true;
            }
            if (is_string($val) && str_contains($val, 'Ahmad Dahlan, S.Pd')) {
                $hasGuruName = true;
            }
            if (is_string($val) && str_contains($val, '06:45:00')) {
                $hasJamMasuk = true;
            }
            if (is_string($val) && str_contains($val, 'RINGKASAN STATISTIK PRESENSI')) {
                $hasRingkasan = true;
            }
        }

        $this->assertTrue($hasJudulRincian, 'Judul rincian harus ada dalam file Excel.');
        $this->assertTrue($hasGuruName, 'Nama guru harus ada dalam file Excel.');
        $this->assertTrue($hasJamMasuk, 'Jam masuk presensi harus ada dalam file Excel.');
        $this->assertTrue($hasRingkasan, 'Ringkasan statistik harus ada dalam file Excel.');
    }
}
