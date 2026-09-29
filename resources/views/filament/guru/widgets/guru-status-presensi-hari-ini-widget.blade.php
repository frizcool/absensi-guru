<x-filament-widgets::widget>
    @php
        $hour = (int) now()->format('H');
        $salam = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
        $namaGuru = $this->guru?->nama ?? auth()->user()->name;
        $nip = $this->guru?->nip ? 'NIP. ' . $this->guru->nip : ($this->guru?->status_kepegawaian ? strtoupper($this->guru->status_kepegawaian) : 'GURU');
    @endphp

    <style>
        .gw-wrapper {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .gw-hero-card {
            position: relative;
            background: linear-gradient(135deg, #044e3a 0%, #065f46 40%, #0f172a 100%);
            border-radius: 1.25rem;
            padding: 1.75rem;
            color: #ffffff;
            box-shadow: 0 16px 32px -8px rgba(4, 78, 58, 0.35), 0 4px 12px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.14);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .gw-ambient-circle-1 {
            position: absolute;
            top: -4rem;
            right: -4rem;
            width: 16rem;
            height: 16rem;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, rgba(16, 185, 129, 0) 70%);
            pointer-events: none;
        }

        .gw-ambient-circle-2 {
            position: absolute;
            bottom: -5rem;
            left: -3rem;
            width: 18rem;
            height: 18rem;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(20, 184, 166, 0.2) 0%, rgba(20, 184, 166, 0) 70%);
            pointer-events: none;
        }

        .gw-top-row {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        @media (min-width: 1024px) {
            .gw-top-row {
                flex-direction: row;
                align-items: flex-start;
                justify-content: space-between;
            }
        }

        .gw-info-section {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            flex: 1;
        }

        .gw-badge-portal {
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

        .gw-dot-pulse {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            background-color: #34d399;
            box-shadow: 0 0 8px #34d399;
            animation: gwPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes gwPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .gw-title {
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 1.25;
            color: #ffffff;
            margin: 0;
            letter-spacing: -0.02em;
        }

        @media (min-width: 768px) {
            .gw-title {
                font-size: 1.85rem;
            }
        }

        .gw-subtitle {
            font-size: 0.875rem;
            line-height: 1.5;
            color: #d1fae5;
            margin: 0;
            max-width: 44rem;
            opacity: 0.92;
        }

        /* Shift & Geofence Info Chips */
        .gw-chips-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.25rem;
        }

        .gw-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            padding: 0.3rem 0.75rem;
            border-radius: 0.6rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #ffffff;
        }

        .gw-chip b {
            color: #a7f3d0;
            font-weight: 700;
        }

        /* Right Panel: Status Card & Clock */
        .gw-status-panel {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            min-width: 18rem;
        }

        @media (min-width: 1024px) {
            .gw-status-panel {
                min-width: 20rem;
            }
        }

        .gw-status-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            border-radius: 1rem;
            padding: 1rem 1.15rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .gw-status-label {
            font-size: 0.6875rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6ee7b7;
        }

        .gw-status-value {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.9375rem;
            font-weight: 700;
        }

        .gw-status-pill-hadir {
            color: #34d399;
        }
        .gw-status-pill-active {
            color: #fbbf24;
        }
        .gw-status-pill-belum {
            color: #fb7185;
        }
        .gw-status-pill-libur {
            color: #60a5fa;
        }

        /* Action Buttons Grid */
        .gw-actions-row {
            position: relative;
            z-index: 10;
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.65rem;
            padding-top: 0.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        @media (min-width: 640px) {
            .gw-actions-row {
                grid-template-columns: 1.4fr 1fr 1fr;
            }
        }

        .gw-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.65rem 1.15rem;
            border-radius: 0.75rem;
            font-size: 0.8125rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            text-align: center;
        }

        .gw-btn svg {
            width: 1.125rem;
            height: 1.125rem;
            flex-shrink: 0;
        }

        .gw-btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.45);
        }

        .gw-btn-primary:hover {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.6);
            color: #ffffff !important;
        }

        .gw-btn-glass {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
        }

        .gw-btn-glass:hover {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            color: #ffffff !important;
        }

        /* Digital Clock Live Pill */
        .gw-live-clock {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 700;
            font-size: 0.75rem;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 0.2rem 0.55rem;
            border-radius: 0.5rem;
            color: #a7f3d0;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
    </style>

    <div class="gw-wrapper" x-data="{
        currentTime: '{{ now()->format('H:i:s') }} WITA',
        updateClock() {
            const now = new Date();
            const timeStr = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Makassar',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).format(now);
            this.currentTime = `${timeStr} WITA`;
        }
    }" x-init="updateClock(); setInterval(() => updateClock(), 1000)">
        <div class="gw-hero-card">
            <!-- Ambient Glowing Lighting -->
            <div class="gw-ambient-circle-1"></div>
            <div class="gw-ambient-circle-2"></div>

            <!-- Top Row Content -->
            <div class="gw-top-row">
                <!-- Left Details -->
                <div class="gw-info-section">
                    <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                        <div class="gw-badge-portal">
                            <span class="gw-dot-pulse"></span>
                            <span>PORTAL PRESENSI GURU</span>
                            <span style="opacity: 0.5;">•</span>
                            <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                        </div>

                        <!-- Live Clock -->
                        <div class="gw-live-clock">
                            <x-filament::icon icon="heroicon-m-clock" style="width: 0.85rem; height: 0.85rem; color: #34d399;" />
                            <span x-text="currentTime">{{ now()->format('H:i:s') }} WITA</span>
                        </div>
                    </div>

                    <h1 class="gw-title">
                        {{ $salam }}, {{ $namaGuru }}! 👋
                    </h1>

                    <p class="gw-subtitle">
                        @if ($this->isLibur)
                            📅 Hari ini adalah <b>{{ $this->infoLibur ?: 'Hari Libur Resmi' }}</b>. Tidak ada kewajiban presensi. Selamat beristirahat bersama keluarga tercinta!
                        @elseif ($this->presensiHariIni?->jam_pulang)
                            🎉 Anda telah menyelesaikan presensi kerja hari ini (Masuk: <b>{{ $this->presensiHariIni->jam_masuk?->format('H:i') }}</b>, Pulang: <b>{{ $this->presensiHariIni->jam_pulang->format('H:i') }} WITA</b>). Terima kasih atas dedikasi luar biasa Anda!
                        @elseif ($this->presensiHariIni?->jam_masuk)
                            ⏱️ Anda telah absen masuk pukul <b>{{ $this->presensiHariIni->jam_masuk->format('H:i') }} WITA</b> ({{ $this->presensiHariIni->status_masuk === 'terlambat' ? 'Terlambat' : 'Tepat Waktu' }}). Tetap semangat bertugas dan jangan lupa presensi pulang saat jam kerja berakhir.
                        @else
                            ⚡ Anda belum melakukan presensi masuk hari ini. Silakan buka menu presensi untuk check-in selfie kamera & validasi lokasi GPS sekolah.
                        @endif
                    </p>

                    <!-- Chips Shift & School Info -->
                    @if (! $this->isLibur && $this->shiftHariIni)
                        <div class="gw-chips-row">
                            <div class="gw-chip">
                                <span>🕒 Shift:</span>
                                <b>{{ $this->shiftHariIni->nama }} ({{ $this->shiftHariIni->jam_masuk?->format('H:i') }} - {{ $this->shiftHariIni->jam_pulang?->format('H:i') }} WITA)</b>
                            </div>
                            <div class="gw-chip">
                                <span>⏱️ Toleransi:</span>
                                <b>{{ $this->shiftHariIni->toleransi_menit }} Menit</b>
                            </div>
                            @if ($this->pengaturan)
                                <div class="gw-chip">
                                    <span>📍 Radius Sekolah:</span>
                                    <b>{{ $this->pengaturan->radius_meter }} m ({{ $this->pengaturan->nama_sekolah }})</b>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Right Status Box -->
                <div class="gw-status-panel">
                    <div class="gw-status-card">
                        <div class="gw-status-label">Status Kehadiran Hari Ini</div>
                        <div class="gw-status-value">
                            @if ($this->isLibur)
                                <span class="gw-status-pill-libur" style="display: flex; align-items: center; gap: 0.4rem;">
                                    <span style="width: 0.6rem; height: 0.6rem; border-radius: 9999px; background: #60a5fa; display: inline-block;"></span>
                                    Hari Libur / Tidak Ada Shift
                                </span>
                            @elseif ($this->presensiHariIni?->jam_pulang)
                                <span class="gw-status-pill-hadir" style="display: flex; align-items: center; gap: 0.4rem;">
                                    <span style="width: 0.6rem; height: 0.6rem; border-radius: 9999px; background: #34d399; display: inline-block; box-shadow: 0 0 6px #34d399;"></span>
                                    Selesai (Pulang {{ $this->presensiHariIni->jam_pulang->format('H:i') }})
                                </span>
                            @elseif ($this->presensiHariIni?->jam_masuk)
                                <span class="gw-status-pill-active" style="display: flex; align-items: center; gap: 0.4rem;">
                                    <span style="width: 0.6rem; height: 0.6rem; border-radius: 9999px; background: #fbbf24; display: inline-block; box-shadow: 0 0 6px #fbbf24;" class="gw-dot-pulse"></span>
                                    Sedang Bertugas (Masuk {{ $this->presensiHariIni->jam_masuk->format('H:i') }})
                                </span>
                            @else
                                <span class="gw-status-pill-belum" style="display: flex; align-items: center; gap: 0.4rem;">
                                    <span style="width: 0.6rem; height: 0.6rem; border-radius: 9999px; background: #fb7185; display: inline-block; box-shadow: 0 0 6px #fb7185;" class="gw-dot-pulse"></span>
                                    Belum Presensi Masuk
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Action Hub -->
            <div class="gw-actions-row">
                <a href="{{ \App\Filament\Guru\Pages\PresensiSaya::getUrl() }}" class="gw-btn gw-btn-primary">
                    <x-filament::icon icon="heroicon-o-camera" />
                    <span>Presensi Masuk / Pulang</span>
                </a>

                <a href="{{ \App\Filament\Guru\Pages\PengajuanIzinGuruPage::getUrl() }}" class="gw-btn gw-btn-glass">
                    <x-filament::icon icon="heroicon-o-document-text" />
                    <span>Ajukan Izin / Cuti</span>
                </a>

                <a href="{{ \App\Filament\Guru\Pages\RiwayatPresensiGuruPage::getUrl() }}" class="gw-btn gw-btn-glass">
                    <x-filament::icon icon="heroicon-o-calendar-days" />
                    <span>Rekap Kehadiran</span>
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
