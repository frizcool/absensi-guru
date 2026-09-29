<?php

namespace App\Models;

use App\Services\WhatsAppService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class PengajuanIzin extends Model
{
    protected $fillable = [
        'guru_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'lokasi_tugas',
        'nomor_surat_tugas',
        'lampiran',
        'status',
        'disetujui_oleh',
        'catatan_approval',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    /**
     * Setujui pengajuan lalu tandai setiap tanggal dalam rentang sebagai
     * status_kehadiran sesuai jenis izin (sakit/izin/cuti/dinas_luar),
     * supaya tidak tercatat sebagai alpa.
     */
    public function setujui(User $penyetuju, ?string $catatan = null): void
    {
        $this->update([
            'status' => 'disetujui',
            'disetujui_oleh' => $penyetuju->id,
            'catatan_approval' => $catatan,
        ]);

        $periode = Carbon::parse($this->tanggal_mulai)->daysUntil(Carbon::parse($this->tanggal_selesai));

        $keteranganPresensi = match ($this->jenis) {
            'dinas_luar' => 'Dinas Luar / SPPD: '.($this->lokasi_tugas ? '['.$this->lokasi_tugas.'] ' : '').$this->alasan.($this->nomor_surat_tugas ? ' (No. '.$this->nomor_surat_tugas.')' : ''),
            default => $this->alasan,
        };

        foreach ($periode as $tanggal) {
            Presensi::updateOrCreate(
                ['guru_id' => $this->guru_id, 'tanggal' => $tanggal->toDateString()],
                [
                    'shift_id' => $this->guru->shiftPadaTanggal($tanggal)?->id ?? $this->guru->shift_id,
                    'status_kehadiran' => $this->jenis, // sakit / izin / cuti / dinas_luar
                    'keterangan' => $keteranganPresensi,
                ]
            );
        }

        try {
            WhatsAppService::kirimNotifikasiStatusIzin($this);
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim WhatsApp approval izin: '.$e->getMessage());
        }
    }

    public function tolak(User $penyetuju, ?string $catatan = null): void
    {
        $this->update([
            'status' => 'ditolak',
            'disetujui_oleh' => $penyetuju->id,
            'catatan_approval' => $catatan,
        ]);

        try {
            WhatsAppService::kirimNotifikasiStatusIzin($this);
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim WhatsApp penolakan izin: '.$e->getMessage());
        }
    }
}
