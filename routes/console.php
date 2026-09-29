<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Otomatisasi Penutupan Presensi Harian Setiap Hari Kerja Pukul 18:00
Schedule::command('presensi:tutup-harian')
    ->weekdays()
    ->at('18:00')
    ->timezone('Asia/Makassar')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/presensi-tutup-harian.log'));

// Otomatisasi Pengiriman Rekap WhatsApp ke Kepala Sekolah Setiap Hari Kerja Pukul 18:30
Schedule::command('presensi:kirim-rekap-wa')
    ->weekdays()
    ->at('18:30')
    ->timezone('Asia/Makassar')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/presensi-rekap-wa.log'));
