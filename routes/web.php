<?php

use App\Http\Controllers\LaporanCetakController;
use App\Http\Controllers\LiveDisplayController;
use App\Http\Controllers\UnifiedLoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->hasRole('guru') || $user->guru !== null) {
            return redirect('/guru');
        }

        return redirect('/sekolahku/panel');
    }

    return redirect('/sekolahku');
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/sekolahku', [UnifiedLoginController::class, 'create'])->name('school.login');
    Route::post('/sekolahku', [UnifiedLoginController::class, 'store'])->name('school.login.store');
});

// Live Display Layar TV Lobi Sekolah (Kiosk Mode)
Route::get('/sekolahku/live-display', [LiveDisplayController::class, 'index'])->name('school.live-display');
Route::get('/sekolahku/live-display/feed', [LiveDisplayController::class, 'feed'])->middleware('throttle:60,1')->name('school.live-display.feed');

// Cetak & Export Laporan Bulanan & Rincian Kedinasan (PDF & Excel)
Route::middleware('auth')->group(function (): void {
    Route::get('/laporan/cetak-bulanan', [LaporanCetakController::class, 'cetakBulanan'])->name('laporan.cetak-bulanan');
    Route::get('/laporan/cetak-rincian', [LaporanCetakController::class, 'cetakRincian'])->name('laporan.cetak-rincian');
    Route::get('/laporan/export-bulanan', [LaporanCetakController::class, 'exportBulanan'])->name('laporan.export-bulanan');
    Route::get('/laporan/export-rincian', [LaporanCetakController::class, 'exportRincian'])->name('laporan.export-rincian');
});

// Peta Situs XML (Sitemap untuk Mesin Pencari)
Route::get('/sitemap.xml', function () {
    $urls = [
        [
            'loc' => url('/'),
            'lastmod' => now()->startOfDay()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ],
        [
            'loc' => route('school.login'),
            'lastmod' => now()->startOfDay()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ],
        [
            'loc' => route('school.live-display'),
            'lastmod' => now()->startOfDay()->toAtomString(),
            'changefreq' => 'always',
            'priority' => '0.8',
        ],
    ];

    return response()
        ->view('sitemap', compact('urls'))
        ->header('Content-Type', 'application/xml; charset=utf-8');
})->name('sitemap');

Route::redirect('/admin', '/sekolahku', 301);

Route::get('/admin/{path?}', function (?string $path = null) {
    return redirect('/sekolahku/panel'.($path ? '/'.$path : ''), 301);
})->where('path', '.*');
