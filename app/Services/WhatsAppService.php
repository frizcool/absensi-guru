<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\PengajuanIzin;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim pesan WhatsApp ke nomor tujuan berdasarkan provider yang aktif.
     */
    public static function kirimPesan(string $nomorHp, string $pesan): array
    {
        $setting = PengaturanSekolah::getSetting();

        if (! $setting->wa_api_token) {
            return [
                'success' => false,
                'message' => 'Token API WhatsApp belum dikonfigurasi di Pengaturan Sekolah.',
            ];
        }

        // Format nomor HP Indonesia (08xxx -> 628xxx)
        $nomorFormatted = preg_replace('/[^0-9]/', '', $nomorHp);
        if (str_starts_with($nomorFormatted, '0')) {
            $nomorFormatted = '62'.substr($nomorFormatted, 1);
        }

        try {
            $provider = strtolower($setting->wa_provider ?? 'fonnte');

            if ($provider === 'fonnte') {
                $response = Http::withHeaders([
                    'Authorization' => $setting->wa_api_token,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $nomorFormatted,
                    'message' => $pesan,
                    'countryCode' => '62',
                ]);
            } elseif ($provider === 'wablas') {
                $domain = $setting->wa_api_endpoint ?: 'https://api.wablas.com';
                $response = Http::withHeaders([
                    'Authorization' => $setting->wa_api_token,
                ])->post(rtrim($domain, '/').'/api/send-message', [
                    'phone' => $nomorFormatted,
                    'message' => $pesan,
                ]);
            } else {
                // Generic Webhook Provider
                $endpoint = $setting->wa_api_endpoint;
                if (! $endpoint) {
                    return ['success' => false, 'message' => 'Endpoint API Generic belum diisi.'];
                }

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$setting->wa_api_token,
                ])->post($endpoint, [
                    'target' => $nomorFormatted,
                    'phone' => $nomorFormatted,
                    'message' => $pesan,
                ]);
            }

            if ($response->successful()) {
                Log::info("WhatsApp berhasil dikirim ke {$nomorFormatted}", ['response' => $response->json()]);

                return ['success' => true, 'response' => $response->json()];
            }

            Log::error("Gagal mengirim WhatsApp ke {$nomorFormatted}", ['body' => $response->body()]);

            return ['success' => false, 'message' => $response->body()];
        } catch (\Throwable $e) {
            Log::error('Exception kirim WhatsApp: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Kirim notifikasi keterlambatan berulang ke WhatsApp Kepala Sekolah.
     */
    public static function kirimNotifikasiKeterlambatan(Guru $guru, Presensi $presensi, int $totalTerlambatBulanIni): ?array
    {
        $setting = PengaturanSekolah::getSetting();

        if (! $setting->notif_terlambat_aktif || ! $setting->no_wa_kepala_sekolah) {
            return null;
        }

        $namaSekolah = $setting->nama_sekolah ?: 'Sekolah';
        $waktuMasuk = $presensi->jam_masuk ? $presensi->jam_masuk->format('H:i').' WITA' : '-';
        $tanggal = $presensi->tanggal ? $presensi->tanggal->translatedFormat('l, d F Y') : now()->translatedFormat('l, d F Y');
        $bulanNama = now()->translatedFormat('F Y');

        $pesan = "🔔 *PEMBERITAHUAN KETERLAMBATAN GURU*\n";
        $pesan .= "🏛️ _{$namaSekolah}_\n\n";
        $pesan .= "Yth. Bapak/Ibu Kepala Sekolah,\n";
        $pesan .= "Sistem mencatat adanya keterlambatan berulang oleh tenaga pendidik berikut:\n\n";
        $pesan .= "👤 *Nama:* {$guru->nama}\n";
        if ($guru->nip) {
            $pesan .= "🆔 *NIP:* {$guru->nip}\n";
        }
        $pesan .= '💼 *Jabatan:* '.($guru->jabatan ?: 'Guru')."\n";
        $pesan .= "📅 *Hari/Tanggal:* {$tanggal}\n";
        $pesan .= "⏰ *Waktu Masuk:* {$waktuMasuk}\n";
        $pesan .= "⚠️ *Frekuensi:* Keterlambatan ke-{$totalTerlambatBulanIni} pada bulan {$bulanNama}\n\n";
        $pesan .= 'Status kehadiran telah tercatat di Sistem Absensi Guru.';

        return self::kirimPesan($setting->no_wa_kepala_sekolah, $pesan);
    }

    /**
     * Kirim notifikasi status pengajuan izin / dinas luar ke WhatsApp guru pemohon.
     */
    public static function kirimNotifikasiStatusIzin(PengajuanIzin $izin): ?array
    {
        $guru = $izin->guru;
        if (! $guru || ! $guru->no_hp) {
            return null;
        }

        $setting = PengaturanSekolah::getSetting();
        $statusText = $izin->status === 'disetujui' ? '✅ *DISETUJUI*' : '❌ *DITOLAK*';
        $jenisLabel = match ($izin->jenis) {
            'sakit' => 'Sakit',
            'izin' => 'Izin Dinas / Pribadi',
            'cuti' => 'Cuti Tahunan',
            'dinas_luar' => 'Tugas Luar / Dinas Luar (SPPD)',
            default => ucfirst(str_replace('_', ' ', $izin->jenis)),
        };

        $tglMulai = $izin->tanggal_mulai ? $izin->tanggal_mulai->translatedFormat('d F Y') : '-';
        $tglSelesai = $izin->tanggal_selesai ? $izin->tanggal_selesai->translatedFormat('d F Y') : '-';
        $periode = $tglMulai === $tglSelesai ? $tglMulai : "{$tglMulai} s/d {$tglSelesai}";

        $pesan = "📋 *STATUS PENGAJUAN PERIZINAN*\n";
        $pesan .= "🏛️ _{$setting->nama_sekolah}_\n\n";
        $pesan .= "Halo Bapak/Ibu *{$guru->nama}*,\n";
        $pesan .= "Pengajuan perizinan Anda telah ditinjau dengan status: {$statusText}\n\n";
        $pesan .= "• *Jenis:* {$jenisLabel}\n";
        $pesan .= "• *Periode:* {$periode}\n";
        $pesan .= "• *Keperluan:* {$izin->alasan}\n";

        if ($izin->lokasi_tugas) {
            $pesan .= "• *Lokasi Tugas:* {$izin->lokasi_tugas}\n";
        }
        if ($izin->nomor_surat_tugas) {
            $pesan .= "• *No. Surat Tugas:* {$izin->nomor_surat_tugas}\n";
        }
        if ($izin->catatan_approval) {
            $pesan .= "• *Catatan Kepala Sekolah / Penyetuju:* {$izin->catatan_approval}\n";
        }

        $pesan .= "\n_Pesan otomatis dikirim oleh Sistem Absensi Sekolah._";

        return self::kirimPesan($guru->no_hp, $pesan);
    }
}
