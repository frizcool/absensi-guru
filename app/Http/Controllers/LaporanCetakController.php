<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPresensiBulananExport;
use App\Exports\LaporanRincianPresensiExport;
use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanCetakController extends Controller
{
    public function cetakBulanan(Request $request)
    {
        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);
        $statusKepegawaian = $request->query('status_kepegawaian', 'semua');
        $shiftId = $request->query('shift_id');

        $user = $request->user();
        if (! $user || (! $user->can('View:LaporanPresensiPage') && ! $user->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah']))) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mencetak dokumen rekapitulasi kedinasan ini.');
        }

        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $pengaturan = PengaturanSekolah::getSetting();
        $jumlahHari = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;

        $queryGuru = Guru::query()->where('aktif', true);

        if ($statusKepegawaian && $statusKepegawaian !== 'semua') {
            $queryGuru->where('status_kepegawaian', $statusKepegawaian);
        }

        if ($shiftId) {
            $queryGuru->where('shift_id', $shiftId);
        }

        $gurus = $queryGuru->orderBy('nama', 'asc')->get();

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->toDateString();

        $presensis = Presensi::whereBetween('tanggal', [$startDate, $endDate])->get();

        $presensiMap = [];
        foreach ($presensis as $p) {
            $hari = (int) Carbon::parse($p->tanggal)->format('j');
            $presensiMap[$p->guru_id.'_'.$hari] = $p;
        }

        $hariInfo = [];
        for ($d = 1; $d <= $jumlahHari; $d++) {
            $tgl = Carbon::createFromDate($tahun, $bulan, $d);
            $isLibur = HariLibur::isLibur($tgl);
            $hariInfo[$d] = [
                'tanggal' => $tgl->toDateString(),
                'hari' => $tgl->locale('id')->isoFormat('dd'),
                'is_libur' => $isLibur,
                'info_libur' => HariLibur::getInfoLibur($tgl),
            ];
        }

        $totalHariEfektif = count(array_filter($hariInfo, fn ($h) => ! $h['is_libur']));

        $rows = [];
        foreach ($gurus as $guru) {
            $kehadiranPerHari = [];
            $totalHadir = 0;
            $totalTerlambat = 0;
            $totalSakit = 0;
            $totalIzin = 0;
            $totalCuti = 0;
            $totalDinasLuar = 0;
            $totalAlpa = 0;
            $totalMenitKerja = 0;

            for ($d = 1; $d <= $jumlahHari; $d++) {
                $key = $guru->id.'_'.$d;
                $p = $presensiMap[$key] ?? null;
                $libur = $hariInfo[$d]['is_libur'] || $guru->isLiburPadaTanggal($hariInfo[$d]['tanggal']);

                $kode = '-';

                if ($p) {
                    if ($p->status_kehadiran === 'hadir') {
                        if ($p->status_masuk === 'terlambat') {
                            $kode = 'T';
                            $totalTerlambat++;
                        } else {
                            $kode = 'H';
                        }
                        $totalHadir++;

                        if ($p->jam_masuk && $p->jam_pulang) {
                            $totalMenitKerja += abs((int) $p->jam_masuk->diffInMinutes($p->jam_pulang));
                        }
                    } elseif ($p->status_kehadiran === 'dinas_luar') {
                        $kode = 'DL';
                        $totalDinasLuar++;
                        $totalHadir++; // Dinas Luar tetap dihitung hadir
                    } elseif ($p->status_kehadiran === 'sakit') {
                        $kode = 'S';
                        $totalSakit++;
                    } elseif ($p->status_kehadiran === 'izin') {
                        $kode = 'I';
                        $totalIzin++;
                    } elseif ($p->status_kehadiran === 'cuti') {
                        $kode = 'C';
                        $totalCuti++;
                    } elseif ($p->status_kehadiran === 'alpa') {
                        $kode = 'A';
                        $totalAlpa++;
                    }
                } elseif ($libur) {
                    $kode = 'L';
                }

                $kehadiranPerHari[$d] = [
                    'kode' => $kode,
                    'is_libur' => $libur,
                ];
            }

            $persentase = $totalHariEfektif > 0 ? round(($totalHadir / $totalHariEfektif) * 100, 1) : 0;
            $jamKerjaFormatted = floor($totalMenitKerja / 60).'j '.($totalMenitKerja % 60).'m';

            $rows[] = [
                'guru' => $guru,
                'kehadiran' => $kehadiranPerHari,
                'total_hadir' => $totalHadir,
                'total_terlambat' => $totalTerlambat,
                'total_sakit' => $totalSakit,
                'total_izin' => $totalIzin,
                'total_cuti' => $totalCuti,
                'total_dinas_luar' => $totalDinasLuar,
                'total_alpa' => $totalAlpa,
                'persentase' => $persentase,
                'jam_kerja' => $jamKerjaFormatted,
            ];
        }

        return view('laporan.cetak-bulanan', [
            'pengaturan' => $pengaturan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $daftarBulan[$bulan] ?? 'Bulan',
            'jumlahHari' => $jumlahHari,
            'hariInfo' => $hariInfo,
            'totalHariEfektif' => $totalHariEfektif,
            'rows' => $rows,
            'statusKepegawaian' => $statusKepegawaian,
        ]);
    }

    public function cetakRincian(Request $request)
    {
        $user = $request->user();
        if (! $user || (! $user->can('View:RincianPresensiPage') && ! $user->can('View:LaporanPresensiPage') && ! $user->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah']))) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mencetak dokumen rincian presensi ini.');
        }

        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);
        $statusKepegawaian = $request->query('statusKepegawaian') ?: $request->query('status_kepegawaian', 'semua');
        $shiftId = $request->query('shiftId') ?: $request->query('shift_id');
        $search = (string) $request->query('search', '');

        $currentYear = (int) now()->year;
        $bulan = min(max($bulan, 1), 12);
        $tahun = min(max($tahun, $currentYear - 2), $currentYear + 1);
        $status = in_array($statusKepegawaian, ['pns', 'pppk', 'non_pns'], true) ? $statusKepegawaian : null;
        $search = mb_substr(trim($search), 0, 100);

        $shift = ($shiftId && Shift::query()->whereKey($shiftId)->exists())
            ? Shift::find($shiftId)
            : null;

        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $pengaturan = PengaturanSekolah::getSetting();

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->toDateString();

        $query = Presensi::query()
            ->with(['guru', 'shift'])
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->whereHas('guru', function ($q) use ($status, $shift, $search): void {
                $q->where('aktif', true);

                if ($status) {
                    $q->where('status_kepegawaian', $status);
                }

                if ($shift) {
                    $q->where('shift_id', $shift->id);
                }

                if ($search !== '') {
                    $searchTerm = '%'.$search.'%';
                    $q->where(function ($sub) use ($searchTerm): void {
                        $sub->where('nama', 'like', $searchTerm)
                            ->orWhere('nip', 'like', $searchTerm)
                            ->orWhere('nuptk', 'like', $searchTerm)
                            ->orWhere('jabatan', 'like', $searchTerm);
                    });
                }
            })
            ->orderBy('tanggal')
            ->orderBy('guru_id');

        $rincian = $query->get();

        $stats = [
            'total' => $rincian->count(),
            'tepat_waktu' => $rincian->where('status_kehadiran', 'hadir')->where('status_masuk', 'tepat_waktu')->count(),
            'terlambat' => $rincian->where('status_kehadiran', 'hadir')->where('status_masuk', 'terlambat')->count(),
            'dinas_luar' => $rincian->where('status_kehadiran', 'dinas_luar')->count(),
            'sakit' => $rincian->where('status_kehadiran', 'sakit')->count(),
            'izin' => $rincian->where('status_kehadiran', 'izin')->count(),
            'cuti' => $rincian->where('status_kehadiran', 'cuti')->count(),
            'alpa' => $rincian->where('status_kehadiran', 'alpa')->count(),
        ];

        return view('laporan.cetak-rincian', [
            'pengaturan' => $pengaturan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $daftarBulan[$bulan] ?? 'Bulan',
            'statusKepegawaian' => $statusKepegawaian,
            'shift' => $shift,
            'search' => $search,
            'rincian' => $rincian,
            'stats' => $stats,
        ]);
    }

    public function exportBulanan(Request $request): BinaryFileResponse
    {
        $user = $request->user();
        if (! $user || (! $user->can('View:LaporanPresensiPage') && ! $user->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah']))) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengekspor dokumen rekapitulasi kedinasan ini.');
        }

        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);
        $statusKepegawaian = $request->query('status_kepegawaian', 'semua');
        $shiftId = $request->query('shift_id');
        $search = (string) $request->query('search', '');

        return (new LaporanPresensiBulananExport(
            bulan: $bulan,
            tahun: $tahun,
            statusKepegawaian: $statusKepegawaian,
            shiftId: $shiftId ? (int) $shiftId : null,
            search: $search,
        ))->download();
    }

    public function exportRincian(Request $request): BinaryFileResponse
    {
        $user = $request->user();
        if (! $user || (! $user->can('View:RincianPresensiPage') && ! $user->can('View:LaporanPresensiPage') && ! $user->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah']))) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengekspor dokumen rincian presensi ini.');
        }

        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);
        $statusKepegawaian = $request->query('statusKepegawaian') ?: $request->query('status_kepegawaian', 'semua');
        $shiftId = $request->query('shiftId') ?: $request->query('shift_id');
        $search = (string) $request->query('search', '');

        return (new LaporanRincianPresensiExport(
            bulan: $bulan,
            tahun: $tahun,
            statusKepegawaian: $statusKepegawaian,
            shiftId: $shiftId ? (int) $shiftId : null,
            search: $search,
        ))->download();
    }
}
