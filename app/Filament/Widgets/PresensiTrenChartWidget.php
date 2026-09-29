<?php

namespace App\Filament\Widgets;

use App\Models\Presensi;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PresensiTrenChartWidget extends ChartWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 2,
    ];

    protected ?string $heading = '📈 Tren Kehadiran Guru (14 Hari Terakhir)';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $labels = [];
        $dataTepatWaktu = [];
        $dataTerlambat = [];
        $dataIzinSakit = [];
        $dataAlpa = [];

        for ($i = 13; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i);
            $tglStr = $tgl->toDateString();

            $labels[] = $tgl->translatedFormat('D, d M');

            $presensis = Presensi::whereDate('tanggal', $tglStr)->get();

            $dataTepatWaktu[] = $presensis->where('status_masuk', 'tepat_waktu')->where('status_kehadiran', 'hadir')->count();
            $dataTerlambat[] = $presensis->where('status_masuk', 'terlambat')->where('status_kehadiran', 'hadir')->count();
            $dataIzinSakit[] = $presensis->whereIn('status_kehadiran', ['sakit', 'izin', 'cuti'])->count();
            $dataAlpa[] = $presensis->where('status_kehadiran', 'alpa')->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tepat Waktu',
                    'data' => $dataTepatWaktu,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Terlambat',
                    'data' => $dataTerlambat,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Izin / Sakit / Cuti',
                    'data' => $dataIzinSakit,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.08)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Alpa',
                    'data' => $dataAlpa,
                    'borderColor' => '#f43f5e',
                    'backgroundColor' => 'rgba(244, 63, 94, 0.08)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
