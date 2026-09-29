<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class Guru extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'foto',
        'nip',
        'nuptk',
        'nama',
        'jenis_kelamin',
        'pangkat_golongan',
        'jabatan',
        'jenis_guru',
        'status_kepegawaian',
        'no_hp',
        'jumlah_jam',
        'kuota_cuti_tahunan',
        'shift_id',
        'jadwal_mingguan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'kuota_cuti_tahunan' => 'integer',
        'jadwal_mingguan' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function jadwalShifts(): HasMany
    {
        return $this->hasMany(JadwalShift::class);
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function pengajuanIzins(): HasMany
    {
        return $this->hasMany(PengajuanIzin::class);
    }

    public function jurnalPembelajarans(): HasMany
    {
        return $this->hasMany(JurnalPembelajaran::class);
    }

    /**
     * Ambil shift yang berlaku untuk guru ini pada tanggal tertentu.
     * Prioritas:
     * 1. Jadwal override di jadwal_shifts (tanggal spesifik).
     * 2. Pola jadwal mingguan guru (jadwal_mingguan).
     * 3. Shift default guru (shift_id).
     */
    public function shiftPadaTanggal(\DateTimeInterface|string $tanggal): ?Shift
    {
        $carbon = Carbon::parse($tanggal);
        $tanggalStr = $carbon->toDateString();

        // 1. Prioritas 1: Jadwal override spesifik tanggal
        $override = $this->jadwalShifts()->whereDate('tanggal', $tanggalStr)->first();
        if ($override) {
            return $override->shift;
        }

        // 2. Prioritas 2: Pola shift mingguan guru
        $jadwalMingguan = $this->jadwal_mingguan;
        if (is_array($jadwalMingguan)) {
            $dayKey = match ($carbon->dayOfWeek) {
                0 => 'minggu',
                1 => 'senin',
                2 => 'selasa',
                3 => 'rabu',
                4 => 'kamis',
                5 => 'jumat',
                6 => 'sabtu',
            };

            if (array_key_exists($dayKey, $jadwalMingguan)) {
                $val = $jadwalMingguan[$dayKey];

                if ($val === 'libur') {
                    return null;
                }

                if (! empty($val)) {
                    $shift = Shift::find($val);
                    if ($shift) {
                        return $shift;
                    }
                }
            }
        }

        // 3. Prioritas 3: Shift default guru
        return $this->shift;
    }

    /**
     * Cek apakah guru libur pada tanggal tertentu (baik karena libur sekolah atau jadwal mingguan libur)
     */
    public function isLiburPadaTanggal(\DateTimeInterface|string $tanggal): bool
    {
        $carbon = Carbon::parse($tanggal);

        // Jika libur sekolah/nasional
        if (HariLibur::isLibur($carbon)) {
            return true;
        }

        // Jika ada jadwal override di jadwal_shifts, maka tidak libur
        $override = $this->jadwalShifts()->whereDate('tanggal', $carbon->toDateString())->first();
        if ($override) {
            return false;
        }

        // Cek pola mingguan
        $jadwalMingguan = $this->jadwal_mingguan;
        if (is_array($jadwalMingguan)) {
            $dayKey = match ($carbon->dayOfWeek) {
                0 => 'minggu',
                1 => 'senin',
                2 => 'selasa',
                3 => 'rabu',
                4 => 'kamis',
                5 => 'jumat',
                6 => 'sabtu',
            };

            if (($jadwalMingguan[$dayKey] ?? null) === 'libur') {
                return true;
            }
        }

        return false;
    }

    /**
     * Ringkasan pola shift mingguan untuk tampilan tabel
     */
    public function ringkasanJadwalMingguan(): string
    {
        $jadwal = $this->jadwal_mingguan;
        if (empty($jadwal) || ! is_array($jadwal)) {
            return 'Default: '.($this->shift?->nama ?? '-');
        }

        $daftarHari = [
            'senin' => 'Sen',
            'selasa' => 'Sel',
            'rabu' => 'Rab',
            'kamis' => 'Kam',
            'jumat' => 'Jum',
            'sabtu' => 'Sab',
            'minggu' => 'Min',
        ];

        $kelompok = [];
        foreach ($daftarHari as $key => $singkatan) {
            $val = $jadwal[$key] ?? null;
            if (! empty($val)) {
                $kelompok[$val][] = $singkatan;
            }
        }

        if (empty($kelompok)) {
            return 'Default: '.($this->shift?->nama ?? '-');
        }

        $parts = [];
        foreach ($kelompok as $val => $hariList) {
            $hariStr = implode(',', $hariList);
            if ($val === 'libur') {
                $parts[] = "{$hariStr}: Libur";
            } else {
                $namaShift = Shift::find($val)?->nama ?? "Shift #{$val}";
                $parts[] = "{$hariStr}: {$namaShift}";
            }
        }

        return implode(' | ', $parts);
    }

    public function statusKepegawaianLabel(): string
    {
        return match ($this->status_kepegawaian) {
            'pns' => 'PNS',
            'pppk' => 'PPPK',
            'non_pns' => 'Non-PNS',
            default => '-',
        };
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto && Storage::disk('public')->exists($this->foto)) {
            return Storage::disk('public')->url($this->foto);
        }

        $inisial = urlencode($this->nama);

        return "https://ui-avatars.com/api/?name={$inisial}&background=10b981&color=ffffff&bold=true";
    }

    public function ensureUserAccountExists(): User
    {
        if ($this->user) {
            $user = $this->user;
            if ($this->nip && $user->nip !== $this->nip) {
                $user->nip = $this->nip;
                $user->saveQuietly();
            }
            if (! $user->hasRole('guru')) {
                $guruRole = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
                $user->assignRole($guruRole);
            }

            return $user;
        }

        $email = ! empty($this->nip)
            ? "{$this->nip}@sekolah.sch.id"
            : 'guru_'.rand(1000, 9999).'@sekolah.sch.id';

        $password = ! empty($this->nip) ? $this->nip : 'password';

        $user = User::create([
            'name' => $this->nama,
            'nip' => $this->nip,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $guruRole = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $user->assignRole($guruRole);

        $this->user_id = $user->id;
        $this->saveQuietly();

        return $user;
    }

    public function resetPasswordKeNip(): bool
    {
        $user = $this->ensureUserAccountExists();
        $password = ! empty($this->nip) ? $this->nip : 'password';
        $user->password = Hash::make($password);

        return $user->save();
    }

    public function resetDeviceId(): bool
    {
        $this->device_id = null;

        return $this->save();
    }

    public function getSisaCutiTahunIniAttribute(): int
    {
        $kuota = $this->kuota_cuti_tahunan ?: 12;
        $tahunIni = Carbon::now()->year;

        $pengajuanCuti = $this->pengajuanIzins()
            ->where('jenis', 'cuti')
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahunIni)
            ->get();

        $cutiTerpakai = 0;
        foreach ($pengajuanCuti as $izin) {
            $mulai = Carbon::parse($izin->tanggal_mulai);
            $selesai = Carbon::parse($izin->tanggal_selesai);
            $cutiTerpakai += $mulai->diffInDays($selesai) + 1;
        }

        return max(0, $kuota - $cutiTerpakai);
    }
}
