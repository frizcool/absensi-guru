<?php

namespace App\Console\Commands;

use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class KirimRekapWaKepalaSekolahCommand extends Command
{
    protected $signature = 'presensi:kirim-rekap-wa {--tanggal= : Tanggal rekap (format Y-m-d)}';

    protected $description = 'Mengirimkan pesan ringkasan presensi harian seluruh guru via WhatsApp ke nomor Kepala Sekolah';

    public function handle(): int
    {
        $inputTanggal = $this->option('tanggal');
        $tanggal = $inputTanggal ? Carbon::parse($inputTanggal) : Carbon::today();
        $tanggalString = $tanggal->toDateString();

        if (HariLibur::isLibur($tanggal)) {
            $this->info("Tanggal {$tanggalString} adalah hari libur. Pengiriman rekap WA dilewati.");

            return Command::SUCCESS;
        }

        $setting = PengaturanSekolah::getSetting();

        if (! $setting->notif_terlambat_aktif || ! $setting->no_wa_kepala_sekolah || ! $setting->wa_api_token) {
            $this->warn('Integrasi WhatsApp belum diaktifkan atau nomor Kepala Sekolah / API token masih kosong.');

            return Command::SUCCESS;
        }

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
            ."• Izin Pribadi: {$izin} orang\n"
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
                $pesan .= "- {$gt->guru->nama} (Masuk: {$jam} WITA)\n";
            }
            $pesan .= "\n";
        }

        $pesan .= '_Laporan otomatis dikirim dari Sistem Absensi Guru pada pukul '.now()->format('H:i').' WITA._';

        $this->info("Mengirim rekap WhatsApp ke {$setting->no_wa_kepala_sekolah}...");

        $res = WhatsAppService::kirimPesan($setting->no_wa_kepala_sekolah, $pesan);

        if ($res['success']) {
            $this->info('✓ Rekapitulasi WhatsApp berhasil dikirimkan!');

            return Command::SUCCESS;
        } else {
            $this->error('✗ Gagal mengirim WhatsApp: '.($res['message'] ?? 'Error provider'));

            return Command::FAILURE;
        }
    }
}
