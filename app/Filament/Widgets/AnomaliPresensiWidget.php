<?php

namespace App\Filament\Widgets;

use App\Models\Guru;
use App\Models\PengajuanIzin;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnomaliPresensiWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $today = Carbon::today()->toDateString();
        $totalGuruAktif = Guru::where('aktif', true)->count();

        $hadir = Presensi::whereDate('tanggal', $today)->where('status_kehadiran', 'hadir')->count();
        $tepatWaktu = Presensi::whereDate('tanggal', $today)->where('status_masuk', 'tepat_waktu')->count();
        $terlambat = Presensi::whereDate('tanggal', $today)->where('status_masuk', 'terlambat')->count();
        $izinSakit = Presensi::whereDate('tanggal', $today)->whereIn('status_kehadiran', ['sakit', 'izin', 'cuti'])->count();
        $sudahPresensi = Presensi::whereDate('tanggal', $today)->count();
        $belumAbsen = max($totalGuruAktif - $sudahPresensi, 0);

        $persentaseHadir = $totalGuruAktif > 0 ? round(($hadir / $totalGuruAktif) * 100, 1) : 0;
        $izinMenunggu = PengajuanIzin::where('status', 'menunggu')->count();

        // Cek guru yang mencapai ambang keterlambatan bulan ini
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        $setting = PengaturanSekolah::getSetting();
        $ambang = $setting->ambang_keterlambatan ?: 3;
        $guruTerlambatBulanIni = Guru::where('aktif', true)
            ->whereHas('presensis', fn ($q) => $q->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->where('status_masuk', 'terlambat'), '>=', $ambang)
            ->count();

        // Generate tren kehadiran 7 hari terakhir untuk sparkline chart
        $sparklineHadir = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i)->toDateString();
            $sparklineHadir[] = Presensi::whereDate('tanggal', $tgl)->where('status_kehadiran', 'hadir')->count();
        }

        $sparklineTerlambat = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i)->toDateString();
            $sparklineTerlambat[] = Presensi::whereDate('tanggal', $tgl)->where('status_masuk', 'terlambat')->count();
        }

        return [
            Stat::make('Tingkat Kehadiran Hari Ini', "{$hadir} / {$totalGuruAktif} Guru ({$persentaseHadir}%)")
                ->description("{$tepatWaktu} tepat waktu, {$terlambat} terlambat")
                ->descriptionIcon('heroicon-m-user-group')
                ->chart($sparklineHadir)
                ->color($persentaseHadir >= 80 ? 'success' : ($persentaseHadir >= 60 ? 'warning' : 'danger')),

            Stat::make('Keterlambatan Hari Ini', "{$terlambat} Guru")
                ->description($terlambat > 0 ? 'Perlu evaluasi kedisiplinan jadwal shift' : 'Seluruh guru hadir tepat waktu!')
                ->descriptionIcon($terlambat > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->chart($sparklineTerlambat)
                ->color($terlambat > 0 ? 'warning' : 'success'),

            Stat::make('Izin / Sakit / Cuti', "{$izinSakit} Guru")
                ->description($izinMenunggu > 0 ? "{$izinMenunggu} pengajuan izin menunggu verifikasi" : 'Semua pengajuan telah diproses')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($izinMenunggu > 0 ? 'warning' : 'info'),

            Stat::make('Peringatan Keterlambatan (WA Watch)', "{$guruTerlambatBulanIni} Guru")
                ->description($guruTerlambatBulanIni > 0 ? "Mencapai ambang batas ≥{$ambang}x terlambat bulan ini" : "Tidak ada guru melampaui ambang ({$ambang}x)")
                ->descriptionIcon($guruTerlambatBulanIni > 0 ? 'heroicon-m-bell-alert' : 'heroicon-m-shield-check')
                ->color($guruTerlambatBulanIni > 0 ? 'danger' : 'success'),
        ];
    }
}
