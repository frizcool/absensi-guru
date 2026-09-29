<?php

namespace App\Filament\Widgets;

use App\Models\Guru;
use App\Models\Presensi;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PresensiStatistikChartWidget extends ChartWidget
{
    use HasWidgetShield;

    protected ?string $heading = '📊 Distribusi Kehadiran Hari Ini';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $today = Carbon::today()->toDateString();
        $totalGuruAktif = Guru::where('aktif', true)->count();

        $tepatWaktu = Presensi::whereDate('tanggal', $today)
            ->where('status_kehadiran', 'hadir')
            ->where('status_masuk', 'tepat_waktu')
            ->count();

        $terlambat = Presensi::whereDate('tanggal', $today)
            ->where('status_kehadiran', 'hadir')
            ->where('status_masuk', 'terlambat')
            ->count();

        $izinSakitCuti = Presensi::whereDate('tanggal', $today)
            ->whereIn('status_kehadiran', ['sakit', 'izin', 'cuti'])
            ->count();

        $sudahTercatat = Presensi::whereDate('tanggal', $today)->count();
        $belumAbsen = max($totalGuruAktif - $sudahTercatat, 0);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Guru',
                    'data' => [$tepatWaktu, $terlambat, $izinSakitCuti, $belumAbsen],
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.9)', // Tepat Waktu (Hijau Emerald)
                        'rgba(245, 158, 11, 0.9)',  // Terlambat (Oranye Amber)
                        'rgba(59, 130, 246, 0.9)',  // Izin/Sakit/Cuti (Biru)
                        'rgba(244, 63, 94, 0.9)',   // Belum Absen (Merah Rose)
                    ],
                    'hoverOffset' => 6,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => [
                "Tepat Waktu ({$tepatWaktu})",
                "Terlambat ({$terlambat})",
                "Izin / Cuti ({$izinSakitCuti})",
                "Belum Hadir ({$belumAbsen})",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
