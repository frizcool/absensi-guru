<?php

namespace App\Filament\Widgets;

use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\PengajuanIzin;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use App\Models\Shift;
use App\Services\WhatsAppService;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class AdminExecutiveOverviewWidget extends Widget
{
    use HasWidgetShield;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.admin-executive-overview-widget';

    public function getPengaturanProperty(): PengaturanSekolah
    {
        return PengaturanSekolah::getSetting();
    }

    public function getStatistikProperty(): array
    {
        $today = Carbon::today()->toDateString();
        $totalGuruAktif = Guru::where('aktif', true)->count();

        $hadir = Presensi::whereDate('tanggal', $today)->where('status_kehadiran', 'hadir')->count();
        $tepatWaktu = Presensi::whereDate('tanggal', $today)->where('status_masuk', 'tepat_waktu')->count();
        $terlambat = Presensi::whereDate('tanggal', $today)->where('status_masuk', 'terlambat')->count();
        $dinasLuar = Presensi::whereDate('tanggal', $today)->where('status_kehadiran', 'dinas_luar')->count();
        $izinSakit = Presensi::whereDate('tanggal', $today)->whereIn('status_kehadiran', ['sakit', 'izin', 'cuti'])->count();
        $sudahPresensi = Presensi::whereDate('tanggal', $today)->count();
        $belumAbsen = max($totalGuruAktif - $sudahPresensi, 0);

        $persenHadir = $totalGuruAktif > 0 ? round((($hadir + $dinasLuar) / $totalGuruAktif) * 100) : 0;
        $izinMenunggu = PengajuanIzin::where('status', 'menunggu')->count();
        $totalShift = Shift::count();

        return [
            'totalGuruAktif' => $totalGuruAktif,
            'hadir' => $hadir,
            'tepatWaktu' => $tepatWaktu,
            'terlambat' => $terlambat,
            'dinasLuar' => $dinasLuar,
            'izinSakit' => $izinSakit,
            'belumAbsen' => $belumAbsen,
            'persenHadir' => $persenHadir,
            'izinMenunggu' => $izinMenunggu,
            'totalShift' => $totalShift,
            'isLibur' => HariLibur::isLibur(Carbon::today()),
            'infoLibur' => HariLibur::getInfoLibur(Carbon::today()),
        ];
    }

    public function kirimWaRekap(): void
    {
        $setting = $this->getPengaturanProperty();

        if (! $setting->no_wa_kepala_sekolah || ! $setting->wa_api_token) {
            Notification::make()
                ->title('Konfigurasi WhatsApp Belum Lengkap')
                ->body('Pastikan nomor WhatsApp Kepala Sekolah dan API Token telah diisi pada menu Pengaturan Sekolah.')
                ->warning()
                ->send();

            return;
        }

        $tanggal = Carbon::today();
        $tanggalString = $tanggal->toDateString();
        $totalGuru = Guru::where('aktif', true)->count();
        $hadir = Presensi::whereDate('tanggal', $tanggalString)->where('status_kehadiran', 'hadir')->count();
        $tepatWaktu = Presensi::whereDate('tanggal', $tanggalString)->where('status_masuk', 'tepat_waktu')->count();
        $terlambat = Presensi::whereDate('tanggal', $tanggalString)->where('status_masuk', 'terlambat')->count();
        $dinasLuar = Presensi::whereDate('tanggal', $tanggalString)->where('status_kehadiran', 'dinas_luar')->count();
        $sakit = Presensi::whereDate('tanggal', $tanggalString)->where('status_kehadiran', 'sakit')->count();
        $izin = Presensi::whereDate('tanggal', $tanggalString)->where('status_kehadiran', 'izin')->count();
        $cuti = Presensi::whereDate('tanggal', $tanggalString)->where('status_kehadiran', 'cuti')->count();
        $alpa = Presensi::whereDate('tanggal', $tanggalString)->where('status_kehadiran', 'alpa')->count();
        $totalHadir = $hadir + $dinasLuar;
        $persen = $totalGuru > 0 ? round(($totalHadir / $totalGuru) * 100, 1) : 0;

        $pesan = "📊 *REKAPITULASI PRESENSI HARIAN GURU*\n"
            ."🏫 *{$setting->nama_sekolah}*\n"
            .'📅 Hari/Tanggal: '.$tanggal->translatedFormat('l, d F Y')."\n\n"
            ."📈 *Ringkasan Kehadiran:*\n"
            ."• Total Guru Aktif: {$totalGuru} orang\n"
            ."• Hadir Tepat Waktu: {$tepatWaktu} orang\n"
            ."• Hadir Terlambat: {$terlambat} orang\n"
            ."• Tugas Luar / Dinas Luar: {$dinasLuar} orang\n"
            ."• Izin Sakit: {$sakit} orang\n"
            ."• Izin Dinas/Pribadi: {$izin} orang\n"
            ."• Cuti: {$cuti} orang\n"
            ."• Tanpa Keterangan (Alpa): {$alpa} orang\n"
            ."• *Persentase Kehadiran:* {$persen}%\n\n";

        if ($terlambat > 0) {
            $guruTerlambat = Presensi::with('guru')
                ->whereDate('tanggal', $tanggalString)
                ->where('status_masuk', 'terlambat')
                ->get();

            $pesan .= "⚠️ *Daftar Guru Terlambat:*\n";
            foreach ($guruTerlambat as $gt) {
                $jam = $gt->jam_masuk ? $gt->jam_masuk->format('H:i') : '-';
                $pesan .= "- {$gt->guru?->nama} (Masuk: {$jam} WITA)\n";
            }
            $pesan .= "\n";
        }

        $pesan .= '_Laporan otomatis dikirim dari Panel Administrator Presensi Guru pada pukul '.now()->format('H:i').' WITA._';

        $result = WhatsAppService::kirimPesan($setting->no_wa_kepala_sekolah, $pesan);

        if ($result['success']) {
            Notification::make()
                ->title('Rekap WhatsApp Berhasil Dikirim!')
                ->body('Pesan ringkasan presensi harian telah terkirim ke WhatsApp Kepala Sekolah ('.$setting->no_wa_kepala_sekolah.').')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Gagal Mengirim Rekap WhatsApp')
                ->body($result['message'] ?? 'Terjadi kesalahan saat menghubungkan ke gateway WhatsApp.')
                ->danger()
                ->send();
        }
    }

    public function tutupPresensiHariIni(): void
    {
        $tanggal = Carbon::today();
        $tanggalString = $tanggal->toDateString();

        if (HariLibur::isLibur($tanggal)) {
            Notification::make()
                ->title('Hari Ini Adalah Hari Libur')
                ->body('Penutupan presensi dan penetapan alpa dilewati pada hari libur.')
                ->info()
                ->send();

            return;
        }

        $gurus = Guru::where('aktif', true)->get();
        $totalAlpa = 0;
        $totalSudahTercatat = 0;

        foreach ($gurus as $guru) {
            $presensi = Presensi::where('guru_id', $guru->id)
                ->whereDate('tanggal', $tanggalString)
                ->first();

            if (! $presensi) {
                if ($guru->isLiburPadaTanggal($tanggal)) {
                    continue;
                }

                $shift = $guru->shiftPadaTanggal($tanggal) ?? $guru->shift;

                Presensi::updateOrCreate(
                    [
                        'guru_id' => $guru->id,
                        'tanggal' => $tanggalString,
                    ],
                    [
                        'shift_id' => $shift?->id ?? 1,
                        'status_kehadiran' => 'alpa',
                        'keterangan' => 'Tidak hadir tanpa keterangan (Ditutup oleh Admin)',
                    ]
                );

                $totalAlpa++;
            } else {
                $totalSudahTercatat++;
            }
        }

        Notification::make()
            ->title('Presensi Hari Ini Berhasil Ditutup')
            ->body("{$totalAlpa} guru ditandai sebagai Alpa. Total {$totalSudahTercatat} guru telah terdata.")
            ->success()
            ->send();
    }
}
