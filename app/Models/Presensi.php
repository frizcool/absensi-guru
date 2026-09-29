<?php

namespace App\Models;

use App\Services\WhatsAppService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Presensi extends Model
{
    protected $fillable = [
        'guru_id',
        'shift_id',
        'tanggal',
        'jam_masuk',
        'lokasi_masuk_lat',
        'lokasi_masuk_lng',
        'foto_masuk',
        'akurasi_masuk',
        'status_masuk',
        'jam_pulang',
        'lokasi_pulang_lat',
        'lokasi_pulang_lng',
        'foto_pulang',
        'akurasi_pulang',
        'status_pulang',
        'status_kehadiran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime',
        'jam_pulang' => 'datetime',
        'lokasi_masuk_lat' => 'float',
        'lokasi_masuk_lng' => 'float',
        'akurasi_masuk' => 'float',
        'lokasi_pulang_lat' => 'float',
        'lokasi_pulang_lng' => 'float',
        'akurasi_pulang' => 'float',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function jurnalPembelajarans(): HasMany
    {
        return $this->hasMany(JurnalPembelajaran::class);
    }

    /**
     * Catat absen masuk untuk guru pada hari ini, sekaligus menghitung
     * apakah tepat waktu atau terlambat berdasarkan shift yang berlaku.
     * Mendukung validasi lokasi, window time, penyimpanan foto selfie,
     * serta pemicu notifikasi WhatsApp jika terjadi keterlambatan berulang.
     */
    public static function checkIn(
        Guru $guru,
        ?float $lat = null,
        ?float $lng = null,
        ?string $fotoBase64 = null,
        ?string $deviceId = null,
        ?float $akurasi = null
    ): self {
        $setting = PengaturanSekolah::getSetting();

        // 1. Validasi Device Binding jika diaktifkan
        if ($setting->wajib_device_binding && $deviceId) {
            if (! $guru->device_id) {
                $guru->device_id = $deviceId;
                $guru->saveQuietly();
            } elseif ($guru->device_id !== $deviceId) {
                throw new \RuntimeException(
                    'Presensi Ditolak! Perangkat Anda tidak cocok dengan perangkat terdaftar untuk akun ini. Silakan hubungi Operator/Admin Sekolah untuk mereset perangkat terdaftar Anda.'
                );
            }
        }

        // 2. Validasi Akurasi GPS (Anti-Fake / Weak GPS)
        if ($akurasi !== null && $setting->maksimal_akurasi_gps && $akurasi > $setting->maksimal_akurasi_gps) {
            throw new \RuntimeException(
                "Presensi Ditolak! Akurasi sinyal GPS Anda terlalu rendah ({$akurasi} meter, toleransi maksimal {$setting->maksimal_akurasi_gps} meter). Pastikan GPS HP dalam mode Akurasi Tinggi (High Accuracy) dan tidak berada di dalam ruangan tertutup tanpa sinyal satelit."
            );
        }

        $tanggal = Carbon::today();
        $shift = $guru->shiftPadaTanggal($tanggal) ?? throw new \RuntimeException(
            'Guru belum memiliki shift kerja yang ditetapkan pada hari ini.'
        );

        $sekarang = Carbon::now();

        // 3. Cek apakah guru memiliki izin Tugas Luar / Dinas Luar (SPPD) aktif hari ini
        $dinasLuarAktif = PengajuanIzin::where('guru_id', $guru->id)
            ->where('jenis', 'dinas_luar')
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->first();

        if (! $dinasLuarAktif) {
            self::validasiLokasiJikaAktif($lat, $lng);
        }

        $presensi = static::where('guru_id', $guru->id)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if (! $presensi) {
            $presensi = new static([
                'guru_id' => $guru->id,
                'tanggal' => $tanggal->toDateString(),
            ]);
        }

        if ($presensi->exists && $presensi->jam_masuk) {
            throw new \RuntimeException('Anda sudah melakukan absen masuk hari ini pukul '.$presensi->jam_masuk->format('H:i').' WITA.');
        }

        $batasTepatWaktu = Carbon::today()
            ->setTimeFromTimeString($shift->getRawOriginal('jam_masuk'))
            ->addMinutes($shift->toleransi_menit);

        $fotoPath = self::simpanFotoBase64($fotoBase64, 'masuk', $guru->id);

        $isTerlambat = $sekarang->greaterThan($batasTepatWaktu);

        $presensi->shift_id = $shift->id;
        $presensi->jam_masuk = $sekarang;
        $presensi->lokasi_masuk_lat = $lat;
        $presensi->lokasi_masuk_lng = $lng;
        $presensi->akurasi_masuk = $akurasi;
        if ($fotoPath) {
            $presensi->foto_masuk = $fotoPath;
        }
        $presensi->status_masuk = $isTerlambat ? 'terlambat' : 'tepat_waktu';
        $presensi->status_kehadiran = $dinasLuarAktif ? 'dinas_luar' : 'hadir';

        if ($isTerlambat) {
            $menitTerlambat = abs((int) $sekarang->diffInMinutes($batasTepatWaktu));
            $keteranganTerlambat = 'Terlambat '.$menitTerlambat.' menit dari jadwal masuk ('.$shift->jam_masuk?->format('H:i').')';
            $presensi->keterangan = $presensi->keterangan ? $presensi->keterangan.' | '.$keteranganTerlambat : $keteranganTerlambat;
        }

        if ($dinasLuarAktif) {
            $infoDinas = 'Tugas Luar / Dinas Luar: '.($dinasLuarAktif->lokasi_tugas ? '['.$dinasLuarAktif->lokasi_tugas.'] ' : '').$dinasLuarAktif->alasan;
            $presensi->keterangan = $presensi->keterangan ? $presensi->keterangan.' | '.$infoDinas : $infoDinas;
        }

        $presensi->save();

        // Trigger Notifikasi WhatsApp ke Kepala Sekolah jika ada keterlambatan berulang
        if ($presensi->status_masuk === 'terlambat') {
            try {
                if ($setting->notif_terlambat_aktif) {
                    $totalTerlambatBulanIni = static::where('guru_id', $guru->id)
                        ->whereMonth('tanggal', Carbon::today()->month)
                        ->whereYear('tanggal', Carbon::today()->year)
                        ->where('status_masuk', 'terlambat')
                        ->count();

                    if ($totalTerlambatBulanIni >= ($setting->ambang_keterlambatan ?: 3)) {
                        WhatsAppService::kirimNotifikasiKeterlambatan($guru, $presensi, $totalTerlambatBulanIni);
                    }
                }
            } catch (\Throwable $e) {
                // Jangan gagalkan presensi guru jika pengiriman notifikasi WhatsApp bermasalah
                Log::warning('Gagal kirim notif WA keterlambatan: '.$e->getMessage());
            }
        }

        return $presensi;
    }

    /**
     * Catat absen pulang.
     */
    public function checkOut(
        ?float $lat = null,
        ?float $lng = null,
        ?string $fotoBase64 = null,
        ?string $deviceId = null,
        ?float $akurasi = null
    ): self {
        if (! $this->jam_masuk) {
            throw new \RuntimeException('Anda belum melakukan absen masuk hari ini. Silakan absen masuk terlebih dahulu.');
        }

        if ($this->jam_pulang) {
            throw new \RuntimeException('Anda sudah melakukan absen pulang hari ini pukul '.$this->jam_pulang->format('H:i').' WITA.');
        }

        $setting = PengaturanSekolah::getSetting();

        // 1. Validasi Device Binding jika diaktifkan
        if ($setting->wajib_device_binding && $deviceId && $this->guru) {
            if ($this->guru->device_id && $this->guru->device_id !== $deviceId) {
                throw new \RuntimeException(
                    'Presensi Pulang Ditolak! Perangkat Anda tidak cocok dengan perangkat terdaftar untuk akun ini.'
                );
            }
        }

        // 2. Validasi Akurasi GPS
        if ($akurasi !== null && $setting->maksimal_akurasi_gps && $akurasi > $setting->maksimal_akurasi_gps) {
            throw new \RuntimeException(
                "Presensi Ditolak! Akurasi sinyal GPS terlalu rendah ({$akurasi}m). Mohon pastikan GPS aktif dengan akurasi tinggi."
            );
        }

        $shift = $this->shift ?? $this->guru?->shiftPadaTanggal($this->tanggal);
        $sekarang = Carbon::now();

        // Validasi lokasi kecuali jika guru sedang berstatus dinas luar
        if ($this->status_kehadiran !== 'dinas_luar') {
            static::validasiLokasiJikaAktif($lat, $lng);
        }

        $jadwalPulang = $shift ? Carbon::today()->setTimeFromTimeString($shift->getRawOriginal('jam_pulang')) : null;
        $fotoPath = self::simpanFotoBase64($fotoBase64, 'pulang', $this->guru_id);

        $this->jam_pulang = $sekarang;
        $this->lokasi_pulang_lat = $lat;
        $this->lokasi_pulang_lng = $lng;
        $this->akurasi_pulang = $akurasi;
        if ($fotoPath) {
            $this->foto_pulang = $fotoPath;
        }

        $isPulangCepat = $jadwalPulang && $sekarang->lessThan($jadwalPulang);
        $this->status_pulang = $isPulangCepat ? 'pulang_cepat' : 'normal';

        if ($isPulangCepat && $jadwalPulang) {
            $menitAwal = abs((int) $jadwalPulang->diffInMinutes($sekarang));
            $this->keterangan = ($this->keterangan ? $this->keterangan.' | ' : '').'Pulang lebih awal '.$menitAwal.' menit';
        }

        $this->save();

        return $this;
    }

    public static function validasiLokasiJikaAktif(?float $lat, ?float $lng): void
    {
        $pengaturan = PengaturanSekolah::getSetting();

        if (! $pengaturan || ! $pengaturan->wajib_validasi_lokasi) {
            return;
        }

        if ($lat === null || $lng === null) {
            throw new \RuntimeException('Lokasi GPS wajib diaktifkan pada browser/perangkat Anda untuk merekam presensi.');
        }

        if (! $pengaturan->latitude || ! $pengaturan->longitude) {
            return;
        }

        $radiusMaksimal = (int) ($pengaturan->radius_meter ?: 100);
        $jarak = self::hitungJarakMeter($pengaturan->latitude, $pengaturan->longitude, $lat, $lng);
        $jarakBulat = round($jarak);

        if ($jarak > $radiusMaksimal) {
            throw new \RuntimeException(
                "Presensi Ditolak! Posisi Anda berada di luar radius sekolah ({$jarakBulat} meter dari sekolah). Batas maksimal yang ditentukan admin adalah {$radiusMaksimal} meter. Data presensi tidak dapat direkam."
            );
        }
    }

    public static function hitungJarakMeter(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $bumi = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $bumi * $c;
    }

    public static function simpanFotoBase64(?string $base64, string $tipe, int $guruId): ?string
    {
        if (! $base64) {
            return null;
        }

        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
            $data = substr($base64, strpos($base64, ',') + 1);
            $type = strtolower($type[1]);
            if (! in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                $type = 'jpg';
            }
            $data = base64_decode($data);

            if ($data === false) {
                return null;
            }

            $namaFile = 'presensi/'.date('Y/m').'/'.$tipe.'_'.$guruId.'_'.time().'_'.Str::random(6).'.'.$type;
            Storage::disk('public')->put($namaFile, $data);

            return $namaFile;
        }

        return null;
    }

    public function getFotoMasukUrlAttribute(): ?string
    {
        if ($this->foto_masuk && Storage::disk('public')->exists($this->foto_masuk)) {
            return Storage::disk('public')->url($this->foto_masuk);
        }

        return null;
    }

    public function getFotoPulangUrlAttribute(): ?string
    {
        if ($this->foto_pulang && Storage::disk('public')->exists($this->foto_pulang)) {
            return Storage::disk('public')->url($this->foto_pulang);
        }

        return null;
    }

    public function anomali(): bool
    {
        return $this->status_masuk === 'terlambat'
            || $this->status_pulang === 'pulang_cepat'
            || $this->status_kehadiran === 'alpa';
    }

    public function scopeHariIni(Builder $query): Builder
    {
        return $query->whereDate('tanggal', Carbon::today());
    }

    public function scopeBulanIni(Builder $query): Builder
    {
        return $query->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year);
    }
}
