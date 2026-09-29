<?php

namespace App\Filament\Guru\Pages;

use App\Models\Guru;
use App\Models\JurnalPembelajaran;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use App\Models\Shift;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PresensiSaya extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Menu Utama';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'presensi';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationLabel = 'Presensi Masuk & Pulang';

    protected static ?string $title = 'Presensi Mandiri Guru';

    protected string $view = 'filament.pages.presensi-saya';

    public ?Presensi $presensiHariIni = null;

    public ?Guru $guru = null;

    public ?Shift $shiftHariIni = null;

    public function mount(): void
    {
        $this->muatDataPresensi();
    }

    public function muatDataPresensi(): void
    {
        $user = Auth::user();
        $this->guru = $user?->guru ?? Guru::where('user_id', $user?->id)->first();

        if (! $this->guru) {
            $this->presensiHariIni = null;
            $this->shiftHariIni = null;

            return;
        }

        $tanggal = Carbon::today()->toDateString();
        $this->shiftHariIni = $this->guru->shiftPadaTanggal($tanggal);

        $this->presensiHariIni = Presensi::where('guru_id', $this->guru->id)
            ->whereDate('tanggal', $tanggal)
            ->first();
    }

    public function checkIn(
        ?float $lat = null,
        ?float $lng = null,
        ?string $foto = null,
        ?string $deviceId = null,
        ?float $akurasi = null
    ): void {
        $this->muatDataPresensi();

        if (! $this->guru) {
            Notification::make()
                ->title('Akun login Anda belum terhubung ke data Guru.')
                ->body('Silakan hubungi operator sekolah untuk menautkan akun Anda di menu Data Guru.')
                ->danger()
                ->send();

            return;
        }

        try {
            $this->presensiHariIni = Presensi::checkIn($this->guru, $lat, $lng, $foto, $deviceId, $akurasi);

            $statusText = match ($this->presensiHariIni->status_kehadiran) {
                'dinas_luar' => 'Presensi Masuk Berhasil (Tugas Luar / Dinas Luar)',
                default => $this->presensiHariIni->status_masuk === 'terlambat'
                    ? 'Presensi Masuk Tercatat (Terlambat)'
                    : 'Presensi Masuk Berhasil (Tepat Waktu)',
            };

            Notification::make()
                ->title($statusText)
                ->body('Waktu: '.$this->presensiHariIni->jam_masuk?->format('H:i').' WITA. Selamat bertugas!')
                ->color($this->presensiHariIni->status_masuk === 'terlambat' ? 'warning' : 'success')
                ->success()
                ->send();
        } catch (\RuntimeException $e) {
            Notification::make()
                ->title('Presensi Masuk Ditolak')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function checkOut(
        ?float $lat = null,
        ?float $lng = null,
        ?string $foto = null,
        ?string $deviceId = null,
        ?float $akurasi = null,
        ?array $jurnal = null
    ): void {
        $this->muatDataPresensi();

        if (! $this->presensiHariIni) {
            Notification::make()
                ->title('Anda belum melakukan absen masuk hari ini.')
                ->danger()
                ->send();

            return;
        }

        try {
            $this->presensiHariIni->checkOut($lat, $lng, $foto, $deviceId, $akurasi);

            // Simpan Jurnal Pembelajaran jika diisi guru
            if (! empty($jurnal) && ! empty($jurnal['materi_kegiatan'])) {
                JurnalPembelajaran::create([
                    'guru_id' => $this->guru->id,
                    'presensi_id' => $this->presensiHariIni->id,
                    'tanggal' => Carbon::today()->toDateString(),
                    'kelas' => $jurnal['kelas'] ?? 'Kelas',
                    'mata_pelajaran' => $jurnal['mata_pelajaran'] ?? 'KBM',
                    'materi_kegiatan' => $jurnal['materi_kegiatan'],
                    'jumlah_jam' => (int) ($jurnal['jumlah_jam'] ?? 2),
                    'keterangan' => $jurnal['keterangan'] ?? null,
                ]);
            }

            $statusText = $this->presensiHariIni->status_pulang === 'pulang_cepat'
                ? 'Presensi Pulang Tercatat (Pulang Lebih Awal)'
                : 'Presensi Pulang Berhasil';

            Notification::make()
                ->title($statusText)
                ->body('Waktu: '.$this->presensiHariIni->jam_pulang?->format('H:i').' WITA. Terima kasih atas dedikasi Anda!')
                ->color($this->presensiHariIni->status_pulang === 'pulang_cepat' ? 'warning' : 'success')
                ->success()
                ->send();
        } catch (\RuntimeException $e) {
            Notification::make()
                ->title('Presensi Pulang Ditolak')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function getPengaturanProperty(): PengaturanSekolah
    {
        return PengaturanSekolah::getSetting();
    }

    public function getRiwayatSemingguProperty()
    {
        if (! $this->guru) {
            return collect();
        }

        $tujuhHariLalu = Carbon::today()->subDays(6)->toDateString();

        return Presensi::where('guru_id', $this->guru->id)
            ->whereDate('tanggal', '>=', $tujuhHariLalu)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    public function getStatistikKehadiranProperty(): array
    {
        if (! $this->guru) {
            return [
                'totalHadir' => 0,
                'tepatWaktu' => 0,
                'terlambat' => 0,
                'izinSakit' => 0,
                'persentase' => 100,
            ];
        }

        $awalBulan = Carbon::now()->startOfMonth()->toDateString();
        $hariIni = Carbon::today()->toDateString();

        $presensis = Presensi::where('guru_id', $this->guru->id)
            ->whereDate('tanggal', '>=', $awalBulan)
            ->whereDate('tanggal', '<=', $hariIni)
            ->get();

        $totalHadir = $presensis->whereIn('status_kehadiran', ['hadir', 'dinas_luar'])->count();
        $tepatWaktu = $presensis->where('status_masuk', 'tepat_waktu')->count();
        $terlambat = $presensis->where('status_masuk', 'terlambat')->count();
        $izinSakit = $presensis->whereIn('status_kehadiran', ['sakit', 'izin', 'cuti'])->count();
        $totalPresensi = $presensis->count();

        $persentase = $totalPresensi > 0
            ? round(($totalHadir / $totalPresensi) * 100)
            : 100;

        return [
            'totalHadir' => $totalHadir,
            'tepatWaktu' => $tepatWaktu,
            'terlambat' => $terlambat,
            'izinSakit' => $izinSakit,
            'persentase' => $persentase,
        ];
    }

    public function getViewData(): array
    {
        return [
            'guru' => $this->guru,
            'shiftHariIni' => $this->shiftHariIni,
            'presensiHariIni' => $this->presensiHariIni,
            'pengaturan' => $this->getPengaturanProperty(),
            'statistikKehadiran' => $this->getStatistikKehadiranProperty(),
            'riwayatSeminggu' => $this->getRiwayatSemingguProperty(),
        ];
    }
}
