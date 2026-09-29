<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalPembelajaran extends Model
{
    protected $fillable = [
        'guru_id',
        'presensi_id',
        'tanggal',
        'kelas',
        'mata_pelajaran',
        'materi_kegiatan',
        'jumlah_jam',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_jam' => 'integer',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function presensi(): BelongsTo
    {
        return $this->belongsTo(Presensi::class);
    }
}
