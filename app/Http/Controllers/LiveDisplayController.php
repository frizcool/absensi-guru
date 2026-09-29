<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class LiveDisplayController extends Controller
{
    public function index()
    {
        $pengaturan = PengaturanSekolah::getSetting();
        $hariIni = Carbon::today()->toDateString();

        $totalGuru = Guru::where('aktif', true)->count();
        $presensisHariIni = Presensi::with('guru', 'shift')
            ->whereDate('tanggal', $hariIni)
            ->get();

        $hadirTepatWaktu = $presensisHariIni->where('status_masuk', 'tepat_waktu')->where('status_kehadiran', 'hadir')->count();
        $terlambat = $presensisHariIni->where('status_masuk', 'terlambat')->where('status_kehadiran', 'hadir')->count();
        $dinasLuar = $presensisHariIni->where('status_kehadiran', 'dinas_luar')->count();
        $izinSakit = $presensisHariIni->whereIn('status_kehadiran', ['sakit', 'izin', 'cuti'])->count();
        $alpa = $presensisHariIni->where('status_kehadiran', 'alpa')->count();
        $totalMasuk = $hadirTepatWaktu + $terlambat + $dinasLuar;
        $belumHadir = max(0, $totalGuru - $totalMasuk - $izinSakit - $alpa);

        $persentase = $totalGuru > 0 ? round(($totalMasuk / $totalGuru) * 100) : 0;

        // Guru yang baru saja check-in hari ini (urut jam_masuk desc)
        $terbaruCheckIn = $presensisHariIni->whereNotNull('jam_masuk')
            ->sortByDesc('jam_masuk')
            ->take(12)
            ->values();

        return view('live-display', [
            'pengaturan' => $pengaturan,
            'totalGuru' => $totalGuru,
            'hadirTepatWaktu' => $hadirTepatWaktu,
            'terlambat' => $terlambat,
            'dinasLuar' => $dinasLuar,
            'izinSakit' => $izinSakit,
            'totalMasuk' => $totalMasuk,
            'belumHadir' => $belumHadir,
            'persentase' => $persentase,
            'terbaruCheckIn' => $terbaruCheckIn,
            'tanggalFormatted' => Carbon::today()->translatedFormat('l, d F Y'),
        ]);
    }

    public function feed(): JsonResponse
    {
        $hariIni = Carbon::today()->toDateString();
        $totalGuru = Guru::where('aktif', true)->count();
        $presensisHariIni = Presensi::with('guru', 'shift')
            ->whereDate('tanggal', $hariIni)
            ->get();

        $hadirTepatWaktu = $presensisHariIni->where('status_masuk', 'tepat_waktu')->where('status_kehadiran', 'hadir')->count();
        $terlambat = $presensisHariIni->where('status_masuk', 'terlambat')->where('status_kehadiran', 'hadir')->count();
        $dinasLuar = $presensisHariIni->where('status_kehadiran', 'dinas_luar')->count();
        $izinSakit = $presensisHariIni->whereIn('status_kehadiran', ['sakit', 'izin', 'cuti'])->count();
        $alpa = $presensisHariIni->where('status_kehadiran', 'alpa')->count();
        $totalMasuk = $hadirTepatWaktu + $terlambat + $dinasLuar;
        $belumHadir = max(0, $totalGuru - $totalMasuk - $izinSakit - $alpa);
        $persentase = $totalGuru > 0 ? round(($totalMasuk / $totalGuru) * 100) : 0;

        $terbaru = $presensisHariIni->whereNotNull('jam_masuk')
            ->sortByDesc('jam_masuk')
            ->take(12)
            ->map(function ($p) {
                return [
                    'nama' => $p->guru->nama,
                    'nip' => $p->guru->nip ?: 'Non-NIP',
                    'jabatan' => $p->guru->jabatan ?: 'Guru',
                    'jam' => $p->jam_masuk?->format('H:i').' WITA',
                    'status_masuk' => $p->status_masuk,
                    'status_kehadiran' => $p->status_kehadiran,
                    'foto' => $p->foto_masuk_url ?? $p->guru->foto_url,
                ];
            })->values();

        return response()->json([
            'totalGuru' => $totalGuru,
            'hadirTepatWaktu' => $hadirTepatWaktu,
            'terlambat' => $terlambat,
            'dinasLuar' => $dinasLuar,
            'izinSakit' => $izinSakit,
            'belumHadir' => $belumHadir,
            'persentase' => $persentase,
            'terbaru' => $terbaru,
        ]);
    }
}
