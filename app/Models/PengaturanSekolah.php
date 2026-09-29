<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PengaturanSekolah extends Model
{
    protected $fillable = [
        'nama_sekolah',
        'npsn',
        'alamat',
        'telepon',
        'email',
        'kepala_sekolah',
        'nip_kepala_sekolah',
        'no_wa_kepala_sekolah',
        'wa_provider',
        'wa_api_token',
        'wa_api_endpoint',
        'notif_terlambat_aktif',
        'ambang_keterlambatan',
        'logo',
        'latitude',
        'longitude',
        'radius_meter',
        'maksimal_akurasi_gps',
        'wajib_validasi_lokasi',
        'wajib_device_binding',
        'teks_pengumuman_display',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meter' => 'integer',
        'maksimal_akurasi_gps' => 'integer',
        'wajib_validasi_lokasi' => 'boolean',
        'wajib_device_binding' => 'boolean',
        'notif_terlambat_aktif' => 'boolean',
        'ambang_keterlambatan' => 'integer',
    ];

    public static function getSetting(): self
    {
        return static::firstOrCreate([], [
            'nama_sekolah' => 'UPTD SPF SD Inpres Rappojawa',
            'latitude' => -5.147665,
            'longitude' => 119.432731,
            'radius_meter' => 100,
            'wajib_validasi_lokasi' => true,
            'wa_provider' => 'fonnte',
            'notif_terlambat_aktif' => false,
            'ambang_keterlambatan' => 3,
        ]);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (blank($this->logo)) {
            return null;
        }

        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        $cleanPath = ltrim($this->logo, '/\\');

        if (Storage::disk('public')->exists($cleanPath)) {
            return Storage::disk('public')->url($cleanPath);
        }

        if (file_exists(public_path('storage/'.$cleanPath))) {
            return asset('storage/'.$cleanPath);
        }

        if (file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        return null;
    }
}
