<?php

namespace App\Filament\Pages;

use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use App\Models\Shift;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanPresensiPage extends Page
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Presensi & Kehadiran';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $title = 'Rekapitulasi Presensi Bulanan Guru';

    protected string $view = 'filament.pages.laporan-presensi';

    public int $bulan;

    public int $tahun;

    public ?string $statusKepegawaian = 'semua';

    public ?int $shiftId = null;

    public string $search = '';

    public static function canAccess(): bool
    {
        $user = Filament::auth()?->user();

        if (! $user) {
            return false;
        }

        if ($user->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah'])) {
            return true;
        }

        $permission = static::getPagePermission();

        return $permission ? $user->can($permission) : parent::canAccess();
    }

    public function mount(): void
    {
        $this->bulan = (int) now()->month;
        $this->tahun = (int) now()->year;
    }

    public function resetFilter(): void
    {
        $this->search = '';
        $this->bulan = (int) now()->month;
        $this->tahun = (int) now()->year;
        $this->statusKepegawaian = 'semua';
        $this->shiftId = null;
    }

    public function getDaftarBulanProperty(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }

    public function getDaftarTahunProperty(): array
    {
        $currentYear = (int) now()->year;

        return [
            $currentYear - 2 => $currentYear - 2,
            $currentYear - 1 => $currentYear - 1,
            $currentYear => $currentYear,
            $currentYear + 1 => $currentYear + 1,
        ];
    }

    public function getDaftarShiftProperty()
    {
        return Shift::all();
    }

    public function getPengaturanProperty(): PengaturanSekolah
    {
        return PengaturanSekolah::getSetting();
    }

    public function exportExcel(): BinaryFileResponse
    {
        $matriks = $this->matriksLaporan;
        $namaBulan = $this->daftarBulan[$this->bulan];
        $filename = "Rekap_Presensi_Guru_{$namaBulan}_{$this->tahun}.xlsx";
        $tempPath = storage_path('app/temp_'.time().'_'.$filename);

        $options = new Options;
        $writer = new Writer($options);
        $writer->openToFile($tempPath);

        // 1. Header Informasi
        $writer->addRow(Row::fromValues([strtoupper($this->pengaturan->nama_sekolah)]));
        $writer->addRow(Row::fromValues(['LAPORAN REKAPITULASI PRESENSI GURU & TENAGA KEPENDIDIKAN']));
        $writer->addRow(Row::fromValues(["Periode: {$matriks['periode_label']}"]));
        $writer->addRow(Row::fromValues([])); // Blank row

        // 2. Header Tabel
        $headerCols = ['No', 'Nama Guru', 'NIP', 'Status'];
        for ($d = 1; $d <= $matriks['jumlah_hari']; $d++) {
            $headerCols[] = (string) $d;
        }
        $headerCols[] = 'H';
        $headerCols[] = 'T';
        $headerCols[] = 'DL';
        $headerCols[] = 'S';
        $headerCols[] = 'I';
        $headerCols[] = 'C';
        $headerCols[] = 'A';
        $headerCols[] = '% Kehadiran';

        $writer->addRow(Row::fromValues($headerCols));

        // 3. Baris Data Guru
        foreach ($matriks['rows'] as $index => $row) {
            $dataCols = [
                $index + 1,
                $row['guru']->nama,
                $row['guru']->nip ?: '-',
                strtoupper($row['guru']->status_kepegawaian),
            ];

            for ($d = 1; $d <= $matriks['jumlah_hari']; $d++) {
                $dataCols[] = $row['kehadiran'][$d]['kode'];
            }

            $dataCols[] = $row['total_hadir'];
            $dataCols[] = $row['total_terlambat'];
            $dataCols[] = $row['total_dinas_luar'];
            $dataCols[] = $row['total_sakit'];
            $dataCols[] = $row['total_izin'];
            $dataCols[] = $row['total_cuti'];
            $dataCols[] = $row['total_alpa'];
            $dataCols[] = $row['persentase'].'%';

            $writer->addRow(Row::fromValues($dataCols));
        }

        // 4. Keterangan Simbol
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['Keterangan: H = Hadir Tepat Waktu, T = Terlambat, DL = Tugas Luar/Dinas Luar, S = Sakit, I = Izin, C = Cuti, A = Alpa, L = Libur/Minggu']));

        $writer->close();

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function getMatriksLaporanProperty(): array
    {
        $jumlahHari = Carbon::createFromDate($this->tahun, $this->bulan, 1)->daysInMonth;

        $queryGuru = Guru::query()->with('shift')->where('aktif', true);

        if ($this->statusKepegawaian && $this->statusKepegawaian !== 'semua') {
            $queryGuru->where('status_kepegawaian', $this->statusKepegawaian);
        }

        if ($this->shiftId) {
            $queryGuru->where('shift_id', $this->shiftId);
        }

        if (trim($this->search) !== '') {
            $searchTerm = '%'.trim($this->search).'%';
            $queryGuru->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'like', $searchTerm)
                    ->orWhere('nip', 'like', $searchTerm)
                    ->orWhere('jabatan', 'like', $searchTerm);
            });
        }

        $gurus = $queryGuru->orderBy('nama', 'asc')->get();

        $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth()->toDateString();
        $namaBulan = $this->daftarBulan[$this->bulan] ?? '';

        $presensis = Presensi::whereBetween('tanggal', [$startDate, $endDate])->get();

        $presensiMap = [];
        foreach ($presensis as $p) {
            $hari = (int) Carbon::parse($p->tanggal)->format('j');
            $presensiMap[$p->guru_id.'_'.$hari] = $p;
        }

        $hariInfo = [];
        for ($d = 1; $d <= $jumlahHari; $d++) {
            $tgl = Carbon::createFromDate($this->tahun, $this->bulan, $d);
            $isLibur = HariLibur::isLibur($tgl);
            $namaHari = $tgl->locale('id')->isoFormat('dd');
            $hariInfo[$d] = [
                'tanggal' => $tgl->toDateString(),
                'hari' => $namaHari,
                'is_libur' => $isLibur,
                'info_libur' => HariLibur::getInfoLibur($tgl),
            ];
        }

        $rows = [];
        $grandTotalHadir = 0;
        $grandTotalTerlambat = 0;
        $grandTotalDinasLuar = 0;
        $grandTotalSakit = 0;
        $grandTotalIzin = 0;
        $grandTotalCuti = 0;
        $grandTotalAlpa = 0;

        foreach ($gurus as $guru) {
            $kehadiranPerHari = [];
            $totalHadir = 0;
            $totalTerlambat = 0;
            $totalDinasLuar = 0;
            $totalSakit = 0;
            $totalIzin = 0;
            $totalCuti = 0;
            $totalAlpa = 0;

            for ($d = 1; $d <= $jumlahHari; $d++) {
                $key = $guru->id.'_'.$d;
                $p = $presensiMap[$key] ?? null;
                $libur = $hariInfo[$d]['is_libur'] || $guru->isLiburPadaTanggal($hariInfo[$d]['tanggal']);

                $kode = '-';
                $class = 'text-gray-400';
                $tooltip = "Tgl {$d} {$namaBulan}: Belum ada presensi";

                if ($p) {
                    $jamMasuk = $p->jam_masuk ? Carbon::parse($p->jam_masuk)->format('H:i') : '-';
                    $jamPulang = $p->jam_pulang ? Carbon::parse($p->jam_pulang)->format('H:i') : '-';

                    if ($p->status_kehadiran === 'hadir') {
                        if ($p->status_masuk === 'terlambat') {
                            $kode = 'T';
                            $class = 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 font-bold';
                            $tooltip = "Tgl {$d} {$namaBulan}: Terlambat (Masuk {$jamMasuk} - Pulang {$jamPulang})";
                            $totalTerlambat++;
                        } else {
                            $kode = 'H';
                            $class = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 font-bold';
                            $tooltip = "Tgl {$d} {$namaBulan}: Hadir Tepat Waktu (Masuk {$jamMasuk} - Pulang {$jamPulang})";
                        }
                        $totalHadir++;
                    } elseif ($p->status_kehadiran === 'dinas_luar') {
                        $kode = 'DL';
                        $class = 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-400 font-bold';
                        $tooltip = "Tgl {$d} {$namaBulan}: Tugas Luar / Dinas Luar".($p->keterangan ? " ({$p->keterangan})" : '');
                        $totalDinasLuar++;
                        $totalHadir++; // Tetap dihitung hadir
                    } elseif ($p->status_kehadiran === 'sakit') {
                        $kode = 'S';
                        $class = 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 font-bold';
                        $tooltip = "Tgl {$d} {$namaBulan}: Sakit".($p->keterangan ? " ({$p->keterangan})" : '');
                        $totalSakit++;
                    } elseif ($p->status_kehadiran === 'izin') {
                        $kode = 'I';
                        $class = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 font-bold';
                        $tooltip = "Tgl {$d} {$namaBulan}: Izin".($p->keterangan ? " ({$p->keterangan})" : '');
                        $totalIzin++;
                    } elseif ($p->status_kehadiran === 'cuti') {
                        $kode = 'C';
                        $class = 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 font-bold';
                        $tooltip = "Tgl {$d} {$namaBulan}: Cuti".($p->keterangan ? " ({$p->keterangan})" : '');
                        $totalCuti++;
                    } elseif ($p->status_kehadiran === 'alpa') {
                        $kode = 'A';
                        $class = 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400 font-bold';
                        $tooltip = "Tgl {$d} {$namaBulan}: Alpa / Tanpa Keterangan";
                        $totalAlpa++;
                    }
                } elseif ($libur) {
                    $kode = 'L';
                    $class = 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500';
                    $tooltip = "Tgl {$d} {$namaBulan}: Libur (".($hariInfo[$d]['info_libur'] ?: 'Akhir Pekan').')';
                }

                $kehadiranPerHari[$d] = [
                    'kode' => $kode,
                    'class' => $class,
                    'tooltip' => $tooltip,
                    'presensi' => $p,
                ];
            }

            $totalHariEfektif = count(array_filter($hariInfo, fn ($h) => ! $h['is_libur']));
            $persentase = $totalHariEfektif > 0 ? round(($totalHadir / $totalHariEfektif) * 100, 1) : 0;

            $grandTotalHadir += $totalHadir;
            $grandTotalTerlambat += $totalTerlambat;
            $grandTotalDinasLuar += $totalDinasLuar;
            $grandTotalSakit += $totalSakit;
            $grandTotalIzin += $totalIzin;
            $grandTotalCuti += $totalCuti;
            $grandTotalAlpa += $totalAlpa;

            $rows[] = [
                'guru' => $guru,
                'kehadiran' => $kehadiranPerHari,
                'total_hadir' => $totalHadir,
                'total_terlambat' => $totalTerlambat,
                'total_dinas_luar' => $totalDinasLuar,
                'total_sakit' => $totalSakit,
                'total_izin' => $totalIzin,
                'total_cuti' => $totalCuti,
                'total_alpa' => $totalAlpa,
                'persentase' => $persentase,
            ];
        }

        $totalHariEfektif = count(array_filter($hariInfo, fn ($h) => ! $h['is_libur']));
        $avgPersentase = count($rows) > 0 ? round(array_sum(array_column($rows, 'persentase')) / count($rows), 1) : 0;

        return [
            'jumlah_hari' => $jumlahHari,
            'hari_info' => $hariInfo,
            'hari_efektif' => $totalHariEfektif,
            'rows' => $rows,
            'total_guru' => count($rows),
            'avg_persentase' => $avgPersentase,
            'grand_total_hadir' => $grandTotalHadir,
            'grand_total_terlambat' => $grandTotalTerlambat,
            'grand_total_dinas_luar' => $grandTotalDinasLuar,
            'grand_total_sakit' => $grandTotalSakit,
            'grand_total_izin' => $grandTotalIzin,
            'grand_total_cuti' => $grandTotalCuti,
            'grand_total_alpa' => $grandTotalAlpa,
            'periode_label' => $this->daftarBulan[$this->bulan].' '.$this->tahun,
        ];
    }

    public function getUrlCetakKedinasanProperty(): string
    {
        return route('laporan.cetak-bulanan', [
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'status_kepegawaian' => $this->statusKepegawaian,
            'shift_id' => $this->shiftId,
        ]);
    }

    public function getUrlRincianProperty(): string
    {
        return RincianPresensiPage::getUrl([
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'statusKepegawaian' => $this->statusKepegawaian,
            'shiftId' => $this->shiftId,
            'search' => $this->search,
        ], panel: 'admin');
    }
}
