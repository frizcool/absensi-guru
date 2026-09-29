<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = [
        'nama',
        'jam_masuk',
        'jam_buka_masuk',
        'jam_tutup_masuk',
        'jam_pulang',
        'jam_buka_pulang',
        'jam_tutup_pulang',
        'toleransi_menit',
    ];

    protected $casts = [
        'jam_masuk' => 'datetime:H:i',
        'jam_buka_masuk' => 'datetime:H:i',
        'jam_tutup_masuk' => 'datetime:H:i',
        'jam_pulang' => 'datetime:H:i',
        'jam_buka_pulang' => 'datetime:H:i',
        'jam_tutup_pulang' => 'datetime:H:i',
    ];

    public function gurus(): HasMany
    {
        return $this->hasMany(Guru::class);
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function jadwalShifts(): HasMany
    {
        return $this->hasMany(JadwalShift::class);
    }
}
