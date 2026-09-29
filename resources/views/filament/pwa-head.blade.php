<!-- PWA & Security Meta Tags -->
<meta name="robots" content="noindex, nofollow">
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#0d9488">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
@php
    $pwaPengaturan = \App\Models\PengaturanSekolah::getSetting();
    $pwaLogoUrl = $pwaPengaturan->logo_url ?: asset('icons/icon.svg');
    $pwaNamaSekolah = $pwaPengaturan->nama_sekolah ?: 'Presensi Guru';
@endphp
<meta name="apple-mobile-web-app-title" content="{{ $pwaNamaSekolah }}">
<link rel="apple-touch-icon" href="{{ $pwaLogoUrl }}">
<link rel="icon" href="{{ $pwaLogoUrl }}">

<!-- Leaflet Map Assets for Global Panel SPA Navigation -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
