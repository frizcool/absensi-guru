<x-filament-widgets::widget>
    @php
        $hour = (int) now()->format('H');
        $salam = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
        $stats = $this->statistik;
        $pengaturan = $this->pengaturan;
    @endphp

    <style>
        .aew-root {
            width: 100%;
            margin-bottom: 0.75rem;
        }

        .aew-hero-card {
            position: relative;
            background: linear-gradient(135deg, #044e3a 0%, #065f46 45%, #0f172a 100%);
            border-radius: 1.25rem;
            padding: 1.75rem 2rem;
            color: #ffffff;
            box-shadow: 0 16px 36px -10px rgba(4, 78, 58, 0.4), 0 4px 12px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.15);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .aew-ambient-glow-1 {
            position: absolute;
            top: -5rem;
            right: -5rem;
            width: 20rem;
            height: 20rem;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, rgba(16, 185, 129, 0) 70%);
            pointer-events: none;
        }

        .aew-ambient-glow-2 {
            position: absolute;
            bottom: -6rem;
            left: 20%;
            width: 22rem;
            height: 22rem;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(20, 184, 166, 0.22) 0%, rgba(20, 184, 166, 0) 70%);
            pointer-events: none;
        }

        .aew-top-grid {
            position: relative;
            z-index: 10;
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 1024px) {
            .aew-top-grid {
                grid-template-columns: 1.35fr 0.85fr;
                align-items: center;
            }
        }

        .aew-left-content {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .aew-badge-panel {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            width: fit-content;
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(52, 211, 153, 0.35);
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #6ee7b7;
            letter-spacing: 0.04em;
        }

        .aew-live-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            background-color: #34d399;
            box-shadow: 0 0 8px #34d399;
            animation: aewPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes aewPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .aew-title {
            font-size: 1.65rem;
            font-weight: 900;
            line-height: 1.2;
            color: #ffffff;
            margin: 0;
            letter-spacing: -0.02em;
        }

        @media (min-width: 768px) {
            .aew-title {
                font-size: 1.95rem;
            }
        }

        .aew-desc {
            font-size: 0.875rem;
            line-height: 1.5;
            color: #d1fae5;
            margin: 0;
            opacity: 0.95;
        }

        /* Chips System Health Row */
        .aew-chips-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
            margin-top: 0.25rem;
        }

        .aew-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 0.35rem 0.85rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #ffffff;
            transition: all 0.2s ease;
        }

        .aew-chip:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(52, 211, 153, 0.4);
        }

        .aew-chip b {
            color: #a7f3d0;
            font-weight: 800;
        }

        /* Digital Clock Card */
        .aew-clock-card {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(16px);
            border-radius: 1.15rem;
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.85rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        }

        .aew-clock-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .aew-clock-label {
            font-size: 0.6875rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6ee7b7;
        }

        .aew-clock-time {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: clamp(2rem, 3.5vw, 2.75rem);
            font-weight: 900;
            letter-spacing: -0.04em;
            line-height: 1;
            color: #ffffff;
            text-shadow: 0 0 20px rgba(52, 211, 153, 0.5);
            display: flex;
            align-items: baseline;
            gap: 0.4rem;
            margin: 0.25rem 0;
        }

        .aew-clock-footer {
            font-size: 0.75rem;
            color: #a7f3d0;
            display: flex;
            align-items: center;
            gap: 0.45rem;
            margin: 0;
            font-weight: 600;
        }

        /* Quick Command Hub */
        .aew-actions-row {
            position: relative;
            z-index: 10;
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .aew-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.6rem 1.1rem;
            border-radius: 0.75rem;
            font-size: 0.8125rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }

        .aew-btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.45);
        }

        .aew-btn-primary:hover {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.6);
            color: #ffffff !important;
        }

        .aew-btn-wa {
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 15px rgba(18, 140, 126, 0.45);
        }

        .aew-btn-wa:hover {
            background: linear-gradient(135deg, #2ee672 0%, #16a090 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(18, 140, 126, 0.6);
            color: #ffffff !important;
        }

        .aew-btn-glass {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(10px);
        }

        .aew-btn-glass:hover {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.35);
            transform: translateY(-2px);
            color: #ffffff !important;
        }

        .aew-badge-pending {
            background: #ef4444;
            color: #ffffff;
            font-size: 0.6875rem;
            padding: 0.1rem 0.45rem;
            border-radius: 9999px;
            font-weight: 800;
        }
    </style>

    <div class="aew-root" x-data="{
        clockTime: '{{ now()->format('H:i:s') }}',
        updateClock() {
            const now = new Date();
            this.clockTime = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Makassar',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).format(now);
        }
    }" x-init="updateClock(); setInterval(() => updateClock(), 1000)">
        <div class="aew-hero-card">
            <!-- Glowing Background Orbs -->
            <div class="aew-ambient-glow-1"></div>
            <div class="aew-ambient-glow-2"></div>

            <!-- Top Grid: Executive Info & Cyber Clock -->
            <div class="aew-top-grid">
                <div class="aew-left-content">
                    <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                        <div class="aew-badge-panel">
                            <span class="aew-live-dot"></span>
                            <span>PANEL UTAMA ADMINISTRATOR</span>
                            <span style="opacity: 0.5;">•</span>
                            <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                        </div>

                        @if ($stats['isLibur'])
                            <span style="background: rgba(59, 130, 246, 0.25); border: 1px solid #60a5fa; color: #93c5fd; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700;">
                                📅 {{ $stats['infoLibur'] ?: 'Hari Libur Resmi' }}
                            </span>
                        @endif
                    </div>

                    <h1 class="aew-title">
                        {{ $salam }}, Administrator! 🚀
                    </h1>

                    <p class="aew-desc">
                        Selamat datang di Pusat Kendali Presensi <b>{{ $pengaturan->nama_sekolah ?: 'Sekolah' }}</b>.
                        Pantau kedisiplinan guru, validasi radius geofence, dan integrasi WhatsApp secara real-time.
                    </p>

                    <!-- System Health Indicators -->
                    <div class="aew-chips-row">
                        <div class="aew-chip" title="Status Geofencing GPS Aktif">
                            <span>📍 Geofence:</span>
                            <b>{{ $pengaturan->wajib_validasi_lokasi ? 'Aktif (' . $pengaturan->radius_meter . 'm)' : 'Nonaktif' }}</b>
                        </div>

                        <div class="aew-chip" title="WhatsApp Gateway Provider">
                            <span>📱 WhatsApp:</span>
                            <b>{{ $pengaturan->wa_provider ? strtoupper($pengaturan->wa_provider) : 'FONNTE' }} ({{ $pengaturan->notif_terlambat_aktif ? 'Notif Auto On' : 'Standby' }})</b>
                        </div>

                        <div class="aew-chip" title="Shift Kerja Terkonfigurasi">
                            <span>🕒 Shift:</span>
                            <b>{{ $stats['totalShift'] }} Shift Kerja</b>
                        </div>

                        <div class="aew-chip" title="Total Guru Aktif">
                            <span>👥 Tenaga Pendidik:</span>
                            <b>{{ $stats['totalGuruAktif'] }} Guru Aktif</b>
                        </div>
                    </div>
                </div>

                <!-- Clock & Attendance Snapshot -->
                <div class="aew-clock-card">
                    <div class="aew-clock-header">
                        <span class="aew-clock-label">Live Waktu Server</span>
                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.6875rem; color: #34d399; font-weight: 700; background: rgba(52, 211, 153, 0.15); padding: 0.15rem 0.5rem; border-radius: 0.4rem;">
                            <span style="width: 0.4rem; height: 0.4rem; border-radius: 9999px; background: #34d399;"></span>
                            WITA (UTC+8)
                        </span>
                    </div>

                    <div>
                        <div class="aew-clock-time">
                            <span x-text="clockTime">{{ now()->format('H:i:s') }}</span>
                            <span style="font-size: 0.875rem; color: #6ee7b7; font-weight: 700;">WITA</span>
                        </div>
                    </div>

                    <div class="aew-clock-footer">
                        <x-filament::icon icon="heroicon-o-academic-cap" style="width: 1rem; height: 1rem; color: #6ee7b7; flex-shrink: 0;" />
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            Kepala Sekolah: <b>{{ $pengaturan->kepala_sekolah ?: 'Belum diatur' }}</b>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Bottom Quick Command Hub -->
            <div class="aew-actions-row">
                <!-- 1. Rekap Bulanan -->
                <a href="{{ \App\Filament\Pages\LaporanPresensiPage::getUrl() }}" class="aew-btn aew-btn-primary">
                    <x-filament::icon icon="heroicon-o-document-chart-bar" style="width: 1.1rem; height: 1.1rem;" />
                    <span>Laporan & Rekap Bulanan</span>
                </a>

                <!-- 2. Pengaturan Geofence & WA -->
                <a href="{{ \App\Filament\Pages\PengaturanSekolahPage::getUrl() }}" class="aew-btn aew-btn-glass">
                    <x-filament::icon icon="heroicon-o-cog-6-tooth" style="width: 1.1rem; height: 1.1rem;" />
                    <span>Pengaturan Radius & WA</span>
                </a>

                <!-- 3. Kirim Rekap WA Manual -->
                <button
                    type="button"
                    wire:click="kirimWaRekap"
                    wire:loading.attr="disabled"
                    class="aew-btn aew-btn-wa"
                    title="Kirim pesan rekap kehadiran hari ini ke WhatsApp Kepala Sekolah sekarang"
                >
                    <x-filament::icon icon="heroicon-o-chat-bubble-left-right" style="width: 1.1rem; height: 1.1rem;" />
                    <span wire:loading.remove wire:target="kirimWaRekap">Kirim Rekap WA ke Kepsek</span>
                    <span wire:loading wire:target="kirimWaRekap">Mengirim WhatsApp...</span>
                </button>

                <!-- 4. Tutup Presensi Harian Manual -->
                <button
                    type="button"
                    wire:click="tutupPresensiHariIni"
                    wire:confirm="Apakah Anda yakin ingin menutup presensi hari ini? Guru yang belum hadir akan otomatis ditandai Alpa."
                    wire:loading.attr="disabled"
                    class="aew-btn aew-btn-glass"
                    title="Otomatis tandai Alpa bagi guru yang belum hadir hari ini"
                >
                    <x-filament::icon icon="heroicon-o-lock-closed" style="width: 1.1rem; height: 1.1rem;" />
                    <span wire:loading.remove wire:target="tutupPresensiHariIni">Tutup Presensi Hari Ini</span>
                    <span wire:loading wire:target="tutupPresensiHariIni">Memproses Alpa...</span>
                </button>

                <!-- 5. Pengajuan Izin Menunggu -->
                <a href="{{ \App\Filament\Resources\PengajuanIzinResource::getUrl() }}" class="aew-btn aew-btn-glass">
                    <x-filament::icon icon="heroicon-o-inbox-stack" style="width: 1.1rem; height: 1.1rem;" />
                    <span>Persetujuan Izin</span>
                    @if ($stats['izinMenunggu'] > 0)
                        <span class="aew-badge-pending">{{ $stats['izinMenunggu'] }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
