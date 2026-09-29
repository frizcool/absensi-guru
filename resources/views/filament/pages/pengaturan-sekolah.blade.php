<x-filament-panels::page>
    <!-- Leaflet CDN Assets (Statically loaded) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        /* Scoped CSS Design System untuk Pengaturan Sekolah */
        :root {
            --ps-bg-card: #ffffff;
            --ps-border: #e2e8f0;
            --ps-text-main: #0f172a;
            --ps-text-muted: #64748b;
            --ps-text-sub: #475569;
            --ps-toolbar-bg: #f8fafc;
            --ps-wa-bg: #efeae2;
            --ps-wa-bubble: #ffffff;
            --ps-wa-text: #111b21;
            --ps-wa-time: #667781;
            --ps-action-bar-bg: rgba(255, 255, 255, 0.95);
        }

        .dark, html.dark, .fi-theme-dark {
            --ps-bg-card: #18181b;
            --ps-border: #27272a;
            --ps-text-main: #f4f4f5;
            --ps-text-muted: #a1a1aa;
            --ps-text-sub: #cbd5e1;
            --ps-toolbar-bg: #1f1f23;
            --ps-wa-bg: #0b141a;
            --ps-wa-bubble: #1f2c34;
            --ps-wa-text: #e9edef;
            --ps-wa-time: #8696a0;
            --ps-action-bar-bg: rgba(24, 24, 27, 0.95);
        }

        .ps-wrapper {
            display: flex;
            flex-direction: column;
            gap: 24px;
            font-family: inherit;
        }

        /* 1. Hero Overview Banner */
        .ps-hero-banner {
            position: relative;
            background: linear-gradient(135deg, #059669 0%, #0d9488 45%, #0f172a 100%);
            border-radius: 18px;
            padding: 28px 32px;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.25);
            overflow: hidden;
        }
        .ps-hero-banner::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .ps-hero-banner::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: 20%;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(52, 211, 153, 0.25) 0%, rgba(52, 211, 153, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .ps-hero-header {
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
            z-index: 2;
        }
        .ps-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            width: fit-content;
            color: #ffffff;
        }
        .ps-hero-pulse {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #6ee7b7;
            box-shadow: 0 0 8px #6ee7b7;
        }
        .ps-hero-title {
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            line-height: 1.25;
            color: #ffffff;
        }
        .ps-hero-desc {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.88);
            margin: 0;
            max-width: 650px;
            line-height: 1.5;
        }

        /* 3 Stat Cards Grid */
        .ps-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin-top: 22px;
            position: relative;
            z-index: 2;
        }
        .ps-stat-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: transform 0.2s, background 0.2s;
        }
        .ps-stat-card:hover {
            background: rgba(255, 255, 255, 0.18);
            transform: translateY(-2px);
        }
        .ps-stat-icon-wrapper {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .ps-stat-label {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .ps-stat-val {
            font-size: 15px;
            font-weight: 800;
            color: #ffffff;
            margin: 2px 0 0 0;
        }

        /* 2. Map Container Box */
        .ps-card-box {
            background: var(--ps-bg-card);
            border: 1px solid var(--ps-border);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .ps-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .ps-card-title-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .ps-card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--ps-text-main);
            margin: 0;
        }
        .ps-card-desc {
            font-size: 12px;
            color: var(--ps-text-muted);
            margin: 2px 0 0 0;
        }
        .ps-map-frame {
            position: relative;
            width: 100%;
            height: 420px;
            min-height: 420px;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--ps-border);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
            background: #f1f5f9;
        }
        #ps-leaflet-map {
            width: 100%;
            height: 100%;
            min-height: 420px;
            z-index: 1;
        }
        .ps-map-frame .leaflet-pane {
            z-index: 10 !important;
        }
        .ps-map-frame .leaflet-top,
        .ps-map-frame .leaflet-bottom {
            z-index: 20 !important;
        }
        .ps-map-overlay-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            z-index: 500;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 11px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
            color: #0f172a;
            pointer-events: none;
        }
        .dark .ps-map-overlay-badge, html.dark .ps-map-overlay-badge, .fi-theme-dark .ps-map-overlay-badge {
            background: rgba(24, 24, 27, 0.95);
            border-color: #3f3f46;
            color: #f4f4f5;
        }
        .ps-map-toolbar {
            background: var(--ps-toolbar-bg);
            border: 1px solid var(--ps-border);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .ps-map-tips {
            font-size: 12px;
            color: var(--ps-text-sub);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .ps-btn-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .ps-btn-custom {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s;
            text-decoration: none;
        }
        .ps-btn-primary {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            border-color: #059669;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
        }
        .ps-btn-primary:hover {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            transform: translateY(-1px);
        }
        .ps-btn-secondary {
            background: #ffffff;
            color: #334155;
            border-color: #cbd5e1;
        }
        .dark .ps-btn-secondary, html.dark .ps-btn-secondary, .fi-theme-dark .ps-btn-secondary {
            background: #27272a;
            color: #e4e4e7;
            border-color: #3f3f46;
        }
        .ps-btn-secondary:hover {
            background: #f1f5f9;
            transform: translateY(-1px);
        }
        .dark .ps-btn-secondary:hover {
            background: #3f3f46;
        }

        /* Custom Leaflet Pin Styling */
        .ps-custom-pin {
            background: transparent;
            border: none;
        }
        .ps-pin-bubble {
            width: 40px;
            height: 40px;
            border-radius: 50% 50% 50% 0;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            border: 3px solid #ffffff;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.45);
            transform: rotate(-45deg);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: grab;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .ps-pin-bubble:active {
            cursor: grabbing;
            transform: rotate(-45deg) scale(1.1);
        }
        .ps-pin-emoji {
            transform: rotate(45deg);
            font-size: 17px;
            user-select: none;
        }
        .ps-pin-shadow {
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 16px;
            height: 6px;
            background: rgba(0,0,0,0.3);
            border-radius: 50%;
            filter: blur(1px);
        }

        /* 3. WhatsApp Simulator UI */
        .ps-wa-simulator {
            background: var(--ps-wa-bg);
            border: 1px solid var(--ps-border);
            border-radius: 14px;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        .ps-wa-bubble {
            background: var(--ps-wa-bubble);
            border-radius: 12px;
            border-top-left-radius: 0px;
            padding: 14px 16px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 1px 2px rgba(0,0,0,0.15);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 12.5px;
            line-height: 1.5;
            color: var(--ps-wa-text);
            position: relative;
        }
        .ps-wa-bubble-header {
            font-size: 11.5px;
            font-weight: 700;
            color: #059669;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .ps-wa-time {
            text-align: right;
            font-size: 10px;
            color: var(--ps-wa-time);
            margin-top: 6px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
        }

        /* 4. Action Bar Sticky */
        .ps-action-bar {
            position: sticky;
            bottom: 16px;
            z-index: 100;
            background: var(--ps-action-bar-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--ps-border);
            border-radius: 16px;
            padding: 14px 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
    </style>

    <div
        x-data="pengaturanSekolahMap({
            initialLat: {{ (float) ($this->data['latitude'] ?? ($this->pengaturan->latitude ?: -5.147665)) }},
            initialLng: {{ (float) ($this->data['longitude'] ?? ($this->pengaturan->longitude ?: 119.432731)) }},
            initialRadius: {{ (int) ($this->data['radius_meter'] ?? ($this->pengaturan->radius_meter ?: 100)) }}
        })"
        class="ps-wrapper"
    >
        <!-- 1. Hero Overview Banner -->
        <div class="ps-hero-banner">
            <div class="ps-hero-header">
                <div class="ps-hero-badge">
                    <span class="ps-hero-pulse"></span>
                    <span>KONFIGURASI SISTEM UTAMA</span>
                </div>
                <h1 class="ps-hero-title">
                    {{ $this->pengaturan->nama_sekolah ?: 'Pengaturan Sekolah & Presensi' }}
                </h1>
                <p class="ps-hero-desc">
                    Kelola parameter radius validasi geofencing GPS, integrasi WhatsApp Gateway Kepala Sekolah, dan profil kop surat resmi instansi.
                </p>
            </div>

            <!-- 3 Stat Cards -->
            <div class="ps-stats-grid">
                <!-- Card 1: Geofence GPS -->
                <div class="ps-stat-card">
                    <div class="ps-stat-icon-wrapper" style="color: #6ee7b7;">📍</div>
                    <div>
                        <p class="ps-stat-label">Geofencing GPS</p>
                        <p class="ps-stat-val">
                            @if ($this->pengaturan->wajib_validasi_lokasi)
                                <span style="color: #6ee7b7;">Aktif</span> ({{ $this->pengaturan->radius_meter ?? 100 }}m)
                            @else
                                <span style="color: #fde68a;">Non-Aktif</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Card 2: WhatsApp Gateway -->
                <div class="ps-stat-card">
                    <div class="ps-stat-icon-wrapper" style="color: #93c5fd;">💬</div>
                    <div>
                        <p class="ps-stat-label">WhatsApp Gateway</p>
                        <p class="ps-stat-val">
                            @if ($this->pengaturan->notif_terlambat_aktif)
                                <span style="color: #6ee7b7;">Aktif</span> (≥{{ $this->pengaturan->ambang_keterlambatan ?? 3 }}x)
                            @else
                                <span style="color: #cbd5e1;">Non-Aktif</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Card 3: NPSN & Profil -->
                <div class="ps-stat-card">
                    <div class="ps-stat-icon-wrapper" style="color: #fbcfe8;">🏫</div>
                    <div>
                        <p class="ps-stat-label">NPSN Sekolah</p>
                        <p class="ps-stat-val">{{ $this->pengaturan->npsn ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Peta Interaktif Leaflet (Visualisasi Radius Geofence) -->
        <div class="ps-card-box">
            <div class="ps-card-header">
                <div class="ps-card-title-group">
                    <span style="font-size: 22px;">🗺️</span>
                    <div>
                        <h3 class="ps-card-title">Visualisasi Peta Radius Geofence Presensi</h3>
                        <p class="ps-card-desc">Lingkaran hijau menandai area toleransi di mana guru diizinkan melakukan presensi masuk & pulang.</p>
                    </div>
                </div>
            </div>

            <!-- Frame Peta with wire:ignore -->
            <div class="ps-map-frame" wire:ignore>
                <div id="ps-leaflet-map"></div>

                <!-- Floating Info Badge -->
                <div class="ps-map-overlay-badge">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block; box-shadow: 0 0 8px #10b981;"></span>
                    <span><b>Pusat Sekolah:</b> Lat: <b id="ps-badge-lat">{{ $this->data['latitude'] ?? ($this->pengaturan->latitude ?: -5.147665) }}</b>, Lng: <b id="ps-badge-lng">{{ $this->data['longitude'] ?? ($this->pengaturan->longitude ?: 119.432731) }}</b> (<span id="ps-badge-radius">{{ $this->data['radius_meter'] ?? ($this->pengaturan->radius_meter ?: 100) }}m</span>)</span>
                </div>
            </div>

            <!-- Toolbar di Bawah Peta -->
            <div class="ps-map-toolbar">
                <div class="ps-map-tips">
                    <span>💡</span>
                    <span><b>Tips:</b> Klik pada peta atau geser pin sekolah untuk mengubah koordinat secara presisi.</span>
                </div>

                <div class="ps-btn-group">
                    <button
                        type="button"
                        x-on:click="ambilGpsSaatIni"
                        class="ps-btn-custom ps-btn-primary"
                        x-bind:disabled="loadingGps"
                    >
                        <span>📍</span>
                        <span x-show="!loadingGps">Ambil Lokasi Saya</span>
                        <span x-show="loadingGps">Mengambil GPS...</span>
                    </button>

                    <button
                        type="button"
                        x-on:click="pusatkanPeta"
                        class="ps-btn-custom ps-btn-secondary"
                        title="Fokuskan kembali kamera peta ke titik sekolah"
                    >
                        <span>🎯</span>
                        <span>Pusatkan Peta</span>
                    </button>

                    <button
                        type="button"
                        x-on:click="resetKoordinat"
                        class="ps-btn-custom ps-btn-secondary"
                        title="Kembalikan koordinat ke nilai yang tersimpan di sistem"
                    >
                        <span>🔄</span>
                        <span>Reset Koordinat</span>
                    </button>

                    <button
                        type="button"
                        x-on:click="bukaGoogleMaps"
                        class="ps-btn-custom ps-btn-secondary"
                        title="Buka titik koordinat ini di tab Google Maps"
                    >
                        <span>↗️</span>
                        <span>Buka di Google Maps</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. Form Input Filament (Tabs) -->
        <form wire:submit="simpan" style="display: flex; flex-direction: column; gap: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; background: var(--ps-bg-card); padding: 14px 20px; border-radius: 14px; border: 1px solid var(--ps-border); box-shadow: 0 2px 8px rgba(0,0,0,0.04); flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 20px;">⚙️</span>
                    <div>
                        <div style="font-weight: 700; font-size: 15px; color: var(--ps-text-main);">Formulir Pengaturan Sistem</div>
                        <div style="font-size: 12px; color: var(--ps-text-muted);">Pilih tab di bawah untuk mengubah Radius Geofencing, Notifikasi WhatsApp, atau Profil Sekolah.</div>
                    </div>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <x-filament::button
                        type="button"
                        wire:click="testKirimWa"
                        color="warning"
                        size="sm"
                        icon="heroicon-o-paper-airplane"
                    >
                        Uji Coba WA
                    </x-filament::button>

                    <x-filament::button
                        type="submit"
                        color="primary"
                        icon="heroicon-o-check"
                    >
                        Simpan Perubahan
                    </x-filament::button>
                </div>
            </div>

            {{ $this->form }}

            <!-- 4. Preview Chat WhatsApp Simulator -->
            <div class="ps-card-box">
                <div class="ps-card-title-group">
                    <span style="font-size: 20px;">📱</span>
                    <div>
                        <h4 class="ps-card-title">Preview Format Notifikasi WhatsApp Kepala Sekolah</h4>
                        <p class="ps-card-desc">Ilustrasi tampilan pesan otomatis yang diterima oleh Kepala Sekolah saat terdeteksi keterlambatan guru berulang.</p>
                    </div>
                </div>

                <div class="ps-wa-simulator">
                    <div class="ps-wa-bubble">
                        <div class="ps-wa-bubble-header">
                            <span>🤖</span>
                            <span>{{ $this->pengaturan->nama_sekolah ?: 'Sistem Absensi Sekolah' }}</span>
                        </div>
                        <div style="font-weight: 700; color: #dc2626; margin-bottom: 6px;">
                            ⚠️ PEMBERITAHUAN KETERLAMBATAN GURU
                        </div>
                        <div>
                            Yth. Bapak/Ibu Kepala Sekolah,<br>
                            Diinformasikan bahwa guru berikut telah mencapai batas keterlambatan presensi:<br><br>
                            • <b>Nama:</b> Ahmad Fajar, S.Pd<br>
                            • <b>NIP:</b> 198507122010011002<br>
                            • <b>Shift:</b> Shift Pagi (07:15 WITA)<br>
                            • <b>Frekuensi Terlambat:</b> {{ $this->pengaturan->ambang_keterlambatan ?? 3 }} kali pada bulan ini<br>
                            • <b>Waktu Terakhir:</b> {{ now()->translatedFormat('d F Y, H:i') }} WITA<br><br>
                            Mohon menjadi perhatian dan bahan evaluasi kedisiplinan. Terima kasih.<br>
                            <i>_Pesan otomatis dari Sistem Absensi Guru_</i>
                        </div>
                        <div class="ps-wa-time">
                            <span>{{ now()->format('H:i') }}</span>
                            <span style="color: #53bdeb; font-weight: bold;">✓✓</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Floating Action Buttons Bar -->
            <div class="ps-action-bar">
                <div style="font-size: 12px; color: var(--ps-text-muted); display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 16px;">🛡️</span>
                    <span>Perubahan parameter disimpan langsung ke database dan aktif secara <i>real-time</i>.</span>
                </div>

                <div class="ps-btn-group">
                    <x-filament::button
                        type="button"
                        wire:click="testKirimWa"
                        color="warning"
                        icon="heroicon-o-paper-airplane"
                    >
                        Uji Coba Kirim WA ke Kepsek
                    </x-filament::button>

                    <x-filament::button
                        type="submit"
                        size="lg"
                        color="primary"
                        icon="heroicon-o-check"
                    >
                        Simpan Semua Pengaturan
                    </x-filament::button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pengaturanSekolahMap', (config) => ({
                loadingGps: false,
                map: null,
                marker: null,
                circle: null,
                lat: parseFloat(config.initialLat) || -5.147665,
                lng: parseFloat(config.initialLng) || 119.432731,
                radius: parseInt(config.initialRadius) || 100,
                savedLat: parseFloat(config.initialLat) || -5.147665,
                savedLng: parseFloat(config.initialLng) || 119.432731,
                savedRadius: parseInt(config.initialRadius) || 100,

                init() {
                    this.$nextTick(() => {
                        this.initLeaflet();
                    });

                    document.addEventListener('livewire:navigated', () => {
                        if (document.getElementById('ps-leaflet-map')) {
                            this.initLeaflet();
                        }
                    });

                    // Reaktivitas jika data form berubah via Livewire
                    this.$watch('$wire.data.latitude', (val) => {
                        if (val !== undefined && val !== null && val !== '') {
                            const num = parseFloat(val);
                            if (!isNaN(num) && Math.abs(num - this.lat) > 0.000001) {
                                this.lat = num;
                                this.syncMapVisuals();
                            }
                        }
                    });

                    this.$watch('$wire.data.longitude', (val) => {
                        if (val !== undefined && val !== null && val !== '') {
                            const num = parseFloat(val);
                            if (!isNaN(num) && Math.abs(num - this.lng) > 0.000001) {
                                this.lng = num;
                                this.syncMapVisuals();
                            }
                        }
                    });

                    this.$watch('$wire.data.radius_meter', (val) => {
                        if (val !== undefined && val !== null && val !== '') {
                            const num = parseInt(val);
                            if (!isNaN(num) && num !== this.radius) {
                                this.radius = num;
                                this.syncCircleRadius();
                            }
                        }
                    });
                },

                ensureLeaflet(callback) {
                    if (typeof L !== 'undefined') {
                        callback();
                        return;
                    }

                    let script = document.querySelector('script[src*="leaflet.js"]');
                    if (!script) {
                        if (!document.querySelector('link[href*="leaflet.css"]')) {
                            const link = document.createElement('link');
                            link.rel = 'stylesheet';
                            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                            document.head.appendChild(link);
                        }
                        script = document.createElement('script');
                        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        script.onload = () => {
                            callback();
                        };
                        document.head.appendChild(script);
                    } else {
                        const check = setInterval(() => {
                            if (typeof L !== 'undefined') {
                                clearInterval(check);
                                callback();
                            }
                        }, 50);
                        setTimeout(() => clearInterval(check), 10000);
                    }
                },

                initLeaflet() {
                    this.ensureLeaflet(() => {
                        const mapElem = document.getElementById('ps-leaflet-map');
                        if (!mapElem) {
                            setTimeout(() => this.initLeaflet(), 150);
                            return;
                        }

                        if (this.map) {
                            try {
                                this.map.remove();
                            } catch (e) {}
                            this.map = null;
                        }

                        if (mapElem._leaflet_id) {
                            mapElem._leaflet_id = null;
                        }

                        // Fix default icons Leaflet
                        delete L.Icon.Default.prototype._getIconUrl;
                        L.Icon.Default.mergeOptions({
                            iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                        });

                        const schoolIcon = L.divIcon({
                            className: 'ps-custom-pin',
                            html: `
                                <div style="position: relative; width: 40px; height: 40px;">
                                    <div class="ps-pin-bubble">
                                        <span class="ps-pin-emoji">🏫</span>
                                    </div>
                                    <div class="ps-pin-shadow"></div>
                                </div>
                            `,
                            iconSize: [40, 40],
                            iconAnchor: [20, 40],
                            popupAnchor: [0, -40]
                        });

                        this.map = L.map(mapElem, {
                            center: [this.lat, this.lng],
                            zoom: 17,
                            zoomControl: false
                        });

                        L.control.zoom({ position: 'bottomright' }).addTo(this.map);

                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                        }).addTo(this.map);

                        // Geofence Circle
                        this.circle = L.circle([this.lat, this.lng], {
                            color: '#10b981',
                            fillColor: '#10b981',
                            fillOpacity: 0.20,
                            radius: this.radius,
                            weight: 2,
                            dashArray: '6, 6'
                        }).addTo(this.map);

                        // Draggable School Marker
                        this.marker = L.marker([this.lat, this.lng], {
                            draggable: true,
                            icon: schoolIcon,
                            title: 'Geser pin ini untuk memindahkan koordinat sekolah'
                        }).addTo(this.map);

                        this.updatePopupContent();
                        this.marker.openPopup();

                        // Marker Drag Event
                        this.marker.on('dragend', (e) => {
                            const pos = e.target.getLatLng();
                            this.updatePosisiFromMap(pos.lat, pos.lng);
                        });

                        // Map Click Event
                        this.map.on('click', (e) => {
                            this.updatePosisiFromMap(e.latlng.lat, e.latlng.lng);
                        });

                        this.updateBadge();

                        setTimeout(() => {
                            if (this.map) this.map.invalidateSize();
                        }, 200);

                        setTimeout(() => {
                            if (this.map) this.map.invalidateSize();
                        }, 600);
                    });
                },

                updatePosisiFromMap(lat, lng) {
                    this.lat = parseFloat(lat.toFixed(6));
                    this.lng = parseFloat(lng.toFixed(6));
                    this.syncMapVisuals();

                    // Push to Livewire
                    this.$wire.set('data.latitude', this.lat);
                    this.$wire.set('data.longitude', this.lng);
                },

                syncMapVisuals() {
                    if (!this.map) return;
                    const latLng = [this.lat, this.lng];
                    if (this.marker) {
                        this.marker.setLatLng(latLng);
                        this.updatePopupContent();
                    }
                    if (this.circle) {
                        this.circle.setLatLng(latLng);
                    }
                    this.updateBadge();
                },

                syncCircleRadius() {
                    if (this.circle) {
                        this.circle.setRadius(this.radius);
                    }
                    this.updatePopupContent();
                    this.updateBadge();
                },

                updatePopupContent() {
                    if (!this.marker) return;
                    this.marker.bindPopup(`
                        <div style="font-family: inherit; font-size: 12px; line-height: 1.5; padding: 2px;">
                            <div style="font-weight: 800; font-size: 13px; color: #065f46; margin-bottom: 4px; display: flex; align-items: center; gap: 4px;">
                                <span>🏫</span>
                                <span>Titik Pusat Sekolah</span>
                            </div>
                            <div style="color: #334155; margin-bottom: 2px;">
                                <b>Lat:</b> ${this.lat}<br>
                                <b>Lng:</b> ${this.lng}
                            </div>
                            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 4px 8px; color: #047857; font-size: 11px; margin-top: 4px;">
                                🎯 <b>Radius Geofence:</b> ${this.radius} meter
                            </div>
                            <div style="color: #64748b; font-size: 10.5px; margin-top: 4px;">
                                💡 Geser pin ini atau klik di peta untuk mengubah lokasi.
                            </div>
                        </div>
                    `);
                },

                updateBadge() {
                    const latElem = document.getElementById('ps-badge-lat');
                    const lngElem = document.getElementById('ps-badge-lng');
                    const radElem = document.getElementById('ps-badge-radius');
                    if (latElem) latElem.innerText = this.lat;
                    if (lngElem) lngElem.innerText = this.lng;
                    if (radElem) radElem.innerText = this.radius + 'm';
                },

                pusatkanPeta() {
                    if (this.map && this.lat && this.lng) {
                        this.map.setView([this.lat, this.lng], 17, { animate: true });
                        if (this.marker) this.marker.openPopup();
                        this.map.invalidateSize();
                    }
                },

                resetKoordinat() {
                    this.lat = this.savedLat;
                    this.lng = this.savedLng;
                    this.radius = this.savedRadius;
                    this.syncMapVisuals();
                    this.syncCircleRadius();
                    this.pusatkanPeta();
                    this.$wire.set('data.latitude', this.lat);
                    this.$wire.set('data.longitude', this.lng);
                    this.$wire.set('data.radius_meter', this.radius);
                },

                bukaGoogleMaps() {
                    if (this.lat && this.lng) {
                        window.open(`https://www.google.com/maps?q=${this.lat},${this.lng}`, '_blank');
                    }
                },

                ambilGpsSaatIni() {
                    if (!navigator.geolocation) {
                        alert('Browser Anda tidak mendukung Geolocation.');
                        return;
                    }
                    this.loadingGps = true;
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            this.loadingGps = false;
                            const userLat = parseFloat(pos.coords.latitude.toFixed(6));
                            const userLng = parseFloat(pos.coords.longitude.toFixed(6));
                            this.lat = userLat;
                            this.lng = userLng;
                            this.syncMapVisuals();
                            if (this.map) {
                                this.map.flyTo([this.lat, this.lng], 18, { animate: true, duration: 1.2 });
                                setTimeout(() => {
                                    if (this.marker) this.marker.openPopup();
                                    if (this.map) this.map.invalidateSize();
                                }, 1300);
                            }
                            this.$wire.set('data.latitude', this.lat);
                            this.$wire.set('data.longitude', this.lng);
                            this.$wire.notifikasiGpsBerhasil(this.lat, this.lng);
                        },
                        (err) => {
                            this.loadingGps = false;
                            let pesan = 'Gagal mengambil lokasi GPS.';
                            if (err.code === 1) pesan = 'Izin akses lokasi GPS ditolak oleh browser. Harap izinkan akses lokasi.';
                            else if (err.code === 2) pesan = 'Posisi GPS tidak tersedia saat ini.';
                            else if (err.code === 3) pesan = 'Waktu permintaan lokasi habis (timeout).';
                            alert(pesan);
                        },
                        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                    );
                }
            }));
        });
    </script>
</x-filament-panels::page>
