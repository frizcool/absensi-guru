<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'is_libur_nasional',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_libur_nasional' => 'boolean',
    ];

    /**
     * Cek apakah tanggal tertentu merupakan hari libur (nasional/sekolah atau akhir pekan)
     */
    public static function isLibur(\DateTimeInterface|string $tanggal, bool $cekAkhirPekan = true): bool
    {
        $date = Carbon::parse($tanggal);

        // Jika cek akhir pekan (Minggu = 0)
        if ($cekAkhirPekan && $date->isSunday()) {
            return true;
        }

        $formatted = $date->toDateString();

        return static::whereDate('tanggal_mulai', '<=', $formatted)
            ->whereDate('tanggal_selesai', '>=', $formatted)
            ->exists();
    }

    /**
     * Dapatkan keterangan libur pada tanggal tertentu jika ada
     */
    public static function getInfoLibur(\DateTimeInterface|string $tanggal): ?string
    {
        $date = Carbon::parse($tanggal);

        if ($date->isSunday()) {
            return 'Hari Minggu';
        }

        $formatted = $date->toDateString();
        $libur = static::whereDate('tanggal_mulai', '<=', $formatted)
            ->whereDate('tanggal_selesai', '>=', $formatted)
            ->first();

        return $libur ? $libur->nama : null;
    }
}
