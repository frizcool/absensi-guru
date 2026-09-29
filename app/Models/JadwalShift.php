<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalShift extends Model
{
    protected $fillable = ['guru_id', 'shift_id', 'tanggal'];

    protected $casts = ['tanggal' => 'date'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}
