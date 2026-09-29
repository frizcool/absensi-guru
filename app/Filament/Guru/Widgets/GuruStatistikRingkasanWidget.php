<?php

namespace App\Filament\Guru\Widgets;

use App\Models\Guru;
use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class GuruStatistikRingkasanWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $user = Auth::user();
        $guru = $user?->guru ?? Guru::where('user_id', $user?->id)->first();

        if (! $guru) {
            return [
                Stat::make('Kehadiran Bulan Ini', '0%')->description('Profil guru belum terhubung'),
            ];
        }

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $today = $now->toDateString();

        $presensis = Presensi::where('guru_id', $guru->id)
            ->whereDate('tanggal', '>=', $startOfMonth)
            ->whereDate('tanggal', '<=', $today)
            ->get();

        $totalHadir = $presensis->whereIn('status_kehadiran', ['hadir', 'dinas_luar'])->count();
        $tepatWaktu = $presensis->where('status_masuk', 'tepat_waktu')->count();
        $terlambat = $presensis->where('status_masuk', 'terlambat')->count();
        $izinSakit = $presensis->whereIn('status_kehadiran', ['sakit', 'izin', 'cuti'])->count();
        $totalPresensi = $presensis->count();

        $persentase = $totalPresensi > 0 ? round(($totalHadir / $totalPresensi) * 100, 1) : 100;

        // Hitung streak kehadiran berturut-turut
        $riwayatSemua = Presensi::where('guru_id', $guru->id)
            ->whereDate('tanggal', '<=', $today)
            ->orderBy('tanggal', 'desc')
            ->limit(30)
            ->get();

        $streak = 0;
        foreach ($riwayatSemua as $p) {
            if (in_array($p->status_kehadiran, ['hadir', 'dinas_luar'])) {
                $streak++;
            } else {
                break;
            }
        }

        // Sparkline 7 hari terakhir
        $chartHadir = [];
        $chartTepat = [];
        $chartTerlambat = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i)->toDateString();
            $pres = Presensi::where('guru_id', $guru->id)->whereDate('tanggal', $tgl)->first();
            $chartHadir[] = $pres && in_array($pres->status_kehadiran, ['hadir', 'dinas_luar']) ? 1 : 0;
            $chartTepat[] = $pres && $pres->status_masuk === 'tepat_waktu' ? 1 : 0;
            $chartTerlambat[] = $pres && $pres->status_masuk === 'terlambat' ? 1 : 0;
        }

        return [
            Stat::make('Kehadiran Bulan Ini', "{$persentase}%")
                ->description("{$totalHadir} hadir dari {$totalPresensi} hari tercatat")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->chart($chartHadir)
                ->color($persentase >= 90 ? 'success' : ($persentase >= 75 ? 'warning' : 'danger')),

            Stat::make('Hadir Tepat Waktu', "{$tepatWaktu} Hari")
                ->description($tepatWaktu > 0 ? 'Kedisiplinan luar biasa!' : 'Belum ada catatan')
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart($chartTepat)
                ->color('success'),

            Stat::make('Keterlambatan', "{$terlambat} Hari")
                ->description($terlambat === 0 ? 'Hebat, 0 keterlambatan!' : 'Usahakan hadir tepat waktu')
                ->descriptionIcon($terlambat > 0 ? 'heroicon-m-clock' : 'heroicon-m-sparkles')
                ->chart($chartTerlambat)
                ->color($terlambat > 0 ? 'danger' : 'success'),

            Stat::make('Izin / Sakit / Cuti', "{$izinSakit} Hari")
                ->description("🔥 Streak: {$streak} hari berturut-turut")
                ->descriptionIcon('heroicon-m-fire')
                ->color('info'),
        ];
    }
}
