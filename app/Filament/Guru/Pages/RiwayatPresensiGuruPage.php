<?php

namespace App\Filament\Guru\Pages;

use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class RiwayatPresensiGuruPage extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Layanan Guru';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'riwayat';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Riwayat & Rekap Bulanan';

    protected static ?string $title = 'Riwayat & Rekap Kehadiran Bulanan';

    protected string $view = 'filament.guru.pages.riwayat-presensi-guru';

    public int $bulan;

    public int $tahun;

    public string $viewMode = 'grid'; // 'grid' | 'table'

    public ?int $selectedHariIndex = null;

    public ?Guru $guru = null;

    public function mount(): void
    {
        $this->bulan = (int) now()->month;
        $this->tahun = (int) now()->year;

        $user = Auth::user();
        $this->guru = $user?->guru ?? Guru::where('user_id', $user?->id)->first();
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

    public function getPengaturanProperty(): PengaturanSekolah
    {
        return PengaturanSekolah::getSetting();
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function bukaDetail(int $dayIndex): void
    {
        $this->selectedHariIndex = $dayIndex;
    }

    public function tutupDetail(): void
    {
        $this->selectedHariIndex = null;
    }

    public function getSelectedDetailProperty(): ?array
    {
        if ($this->selectedHariIndex === null) {
            return null;
        }

        $daftarHari = $this->rekapBulanan['daftar_hari'] ?? [];

        return $daftarHari[$this->selectedHariIndex] ?? null;
    }

    public function getRekapBulananProperty(): array
    {
        if (! $this->guru) {
            return [
                'total_hadir' => 0,
                'total_tepat_waktu' => 0,
                'total_terlambat' => 0,
                'total_dinas_luar' => 0,
                'total_sakit' => 0,
                'total_izin' => 0,
                'total_cuti' => 0,
                'total_alpa' => 0,
                'persentase' => 0,
                'hari_efektif' => 0,
                'daftar_hari' => [],
            ];
        }

        $jumlahHari = Carbon::createFromDate($this->tahun, $this->bulan, 1)->daysInMonth;
        $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth()->toDateString();

        $presensis = Presensi::where('guru_id', $this->guru->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->keyBy(fn ($item) => (int) Carbon::parse($item->tanggal)->format('j'));

        $totalHadir = 0;
        $totalTepatWaktu = 0;
        $totalTerlambat = 0;
        $totalDinasLuar = 0;
        $totalSakit = 0;
        $totalIzin = 0;
        $totalCuti = 0;
        $totalAlpa = 0;
        $daftarHari = [];
        $hariEfektif = 0;

        for ($d = 1; $d <= $jumlahHari; $d++) {
            $tgl = Carbon::createFromDate($this->tahun, $this->bulan, $d);
            $isLibur = HariLibur::isLibur($tgl) || ($this->guru && $this->guru->isLiburPadaTanggal($tgl));
            $p = $presensis->get($d);

            if (! $isLibur) {
                $hariEfektif++;
            }

            if ($p) {
                if ($p->status_kehadiran === 'hadir') {
                    $totalHadir++;
                    if ($p->status_masuk === 'terlambat') {
                        $totalTerlambat++;
                    } else {
                        $totalTepatWaktu++;
                    }
                } elseif ($p->status_kehadiran === 'dinas_luar') {
                    $totalHadir++;
                    $totalDinasLuar++;
                } elseif ($p->status_kehadiran === 'sakit') {
                    $totalSakit++;
                } elseif ($p->status_kehadiran === 'izin') {
                    $totalIzin++;
                } elseif ($p->status_kehadiran === 'cuti') {
                    $totalCuti++;
                } elseif ($p->status_kehadiran === 'alpa') {
                    $totalAlpa++;
                }
            }

            $daftarHari[$d] = [
                'day' => $d,
                'tanggal' => $tgl,
                'hari' => $tgl->translatedFormat('l'),
                'is_libur' => $isLibur,
                'info_libur' => HariLibur::getInfoLibur($tgl),
                'presensi' => $p,
            ];
        }

        $persentase = $hariEfektif > 0 ? round(($totalHadir / $hariEfektif) * 100, 1) : 0;

        return [
            'total_hadir' => $totalHadir,
            'total_tepat_waktu' => $totalTepatWaktu,
            'total_terlambat' => $totalTerlambat,
            'total_dinas_luar' => $totalDinasLuar,
            'total_sakit' => $totalSakit,
            'total_izin' => $totalIzin,
            'total_cuti' => $totalCuti,
            'total_alpa' => $totalAlpa,
            'persentase' => $persentase,
            'hari_efektif' => $hariEfektif,
            'daftar_hari' => $daftarHari,
        ];
    }
}
