<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Primary SEO Meta Tags -->
    <title>Live Display Presensi Guru &bull; {{ $pengaturan->nama_sekolah }}</title>
    <meta name="title" content="Live Display Presensi Guru &bull; {{ $pengaturan->nama_sekolah }}">
    <meta name="description" content="Pantauan kehadiran real-time bapak/ibu guru dan tenaga kependidikan di {{ $pengaturan->nama_sekolah }}. Statistik kehadiran, status dinas, keterlambatan, dan daftar check-in harian.">
    <meta name="keywords" content="live display kehadiran, monitor presensi guru, absensi real time lobi, {{ $pengaturan->nama_sekolah }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ route('school.live-display') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('school.live-display') }}">
    <meta property="og:title" content="Live Display Presensi Guru &bull; {{ $pengaturan->nama_sekolah }}">
    <meta property="og:description" content="Pantauan kehadiran real-time bapak/ibu guru dan tenaga kependidikan di {{ $pengaturan->nama_sekolah }}.">
    <meta property="og:image" content="{{ asset('icons/og-cover.svg') }}">
    <meta property="og:image:type" content="image/svg+xml">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ $pengaturan->nama_sekolah }}">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ route('school.live-display') }}">
    <meta name="twitter:title" content="Live Display Presensi Guru &bull; {{ $pengaturan->nama_sekolah }}">
    <meta name="twitter:description" content="Pantauan real-time kehadiran bapak/ibu guru dan tenaga kependidikan.">
    <meta name="twitter:image" content="{{ asset('icons/og-cover.svg') }}">

    <!-- Favicon & PWA -->
    <link rel="icon" href="{{ $pengaturan->logo_url ?: asset('icons/icon.svg') }}">
    <link rel="apple-touch-icon" href="{{ $pengaturan->logo_url ?: asset('icons/icon.svg') }}">
    <meta name="theme-color" content="#0d9488">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #090d16 0%, #0d1527 50%, #080f1e 100%);
            color: #f8fafc;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Top Header */
        header {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 16px 36px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(16, 185, 129, 0.4);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-text h1 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }

        .brand-text p {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
        }

        .clock-container {
            text-align: right;
        }

        .digital-clock {
            font-family: 'JetBrains Mono', monospace;
            font-size: 34px;
            font-weight: 700;
            color: #34d399;
            text-shadow: 0 0 20px rgba(52, 211, 153, 0.4);
            letter-spacing: 1px;
        }

        .digital-date {
            font-size: 14px;
            color: #cbd5e1;
            font-weight: 600;
        }

        /* Main Content Grid */
        main {
            padding: 24px 36px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Stat Metrics Bar */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 18px;
        }

        .metric-card {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 18px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, border-color 0.2s;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }

        .card-emerald::before { background: linear-gradient(90deg, #10b981, #34d399); }
        .card-amber::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .card-blue::before { background: linear-gradient(90deg, #0ea5e9, #38bdf8); }
        .card-purple::before { background: linear-gradient(90deg, #a855f7, #c084fc); }
        .card-rose::before { background: linear-gradient(90deg, #f43f5e, #fb7185); }

        .metric-label {
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .metric-value {
            font-size: 40px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .metric-sub {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }

        /* Live Stream Activity */
        .activity-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #f1f5f9;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pulse-dot {
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 12px #10b981;
            animation: pulse 1.8s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .teachers-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .teacher-card {
            background: rgba(30, 41, 59, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .teacher-avatar {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            overflow: hidden;
            background: #1e293b;
            flex-shrink: 0;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .teacher-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .teacher-info {
            flex: 1;
            min-width: 0;
        }

        .teacher-name {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }

        .teacher-meta {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        .badge-ontime { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge-late { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .badge-dinas { background: rgba(14, 165, 233, 0.2); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.3); }

        /* Ticker Announcement Footer */
        footer {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(16px);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 10px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            overflow: hidden;
        }

        .ticker-label {
            background: #10b981;
            color: #064e3b;
            font-weight: 800;
            font-size: 12px;
            padding: 6px 14px;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            flex-shrink: 0;
        }

        .ticker-content {
            flex: 1;
            overflow: hidden;
            white-space: nowrap;
        }

        .ticker-text {
            display: inline-block;
            font-size: 14px;
            color: #e2e8f0;
            font-weight: 500;
            animation: marquee 25s linear infinite;
        }

        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        /* Fullscreen button */
        .btn-fullscreen {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #94a3b8;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-fullscreen:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body>

    <!-- Header Instansi & Jam Digital -->
    <header>
        <div class="brand-container">
            <div class="brand-logo">
                @if($pengaturan->logo_url)
                    <img src="{{ $pengaturan->logo_url }}" alt="Logo">
                @else
                    <span style="font-size: 28px;">🏫</span>
                @endif
            </div>
            <div class="brand-text">
                <h1>{{ $pengaturan->nama_sekolah }}</h1>
                <p>PANTAUAN REAL-TIME KEHADIRAN GURU & TENAGA KEPENDIDIKAN</p>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 20px;">
            <div class="clock-container">
                <div class="digital-clock" id="liveClock">00:00:00</div>
                <div class="digital-date">{{ $tanggalFormatted }}</div>
            </div>
            <button onclick="toggleFullscreen()" class="btn-fullscreen" title="Layar Penuh (F11)">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>
                Full Screen
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- 5 Kartu Metrik Ringkasan -->
        <div class="metrics-grid">
            <div class="metric-card card-emerald">
                <div class="metric-label">
                    <span>⚡</span> Hadir Tepat Waktu
                </div>
                <div class="metric-value">
                    <span id="statTepatWaktu">{{ $hadirTepatWaktu }}</span>
                    <span class="metric-sub">guru</span>
                </div>
            </div>

            <div class="metric-card card-amber">
                <div class="metric-label">
                    <span>⚠️</span> Hadir Terlambat
                </div>
                <div class="metric-value">
                    <span id="statTerlambat">{{ $terlambat }}</span>
                    <span class="metric-sub">guru</span>
                </div>
            </div>

            <div class="metric-card card-blue">
                <div class="metric-label">
                    <span>✈️</span> Tugas Luar (SPPD)
                </div>
                <div class="metric-value">
                    <span id="statDinasLuar">{{ $dinasLuar }}</span>
                    <span class="metric-sub">guru</span>
                </div>
            </div>

            <div class="metric-card card-purple">
                <div class="metric-label">
                    <span>📋</span> Izin / Cuti / Sakit
                </div>
                <div class="metric-value">
                    <span id="statIzinSakit">{{ $izinSakit }}</span>
                    <span class="metric-sub">guru</span>
                </div>
            </div>

            <div class="metric-card card-rose">
                <div class="metric-label">
                    <span>⏳</span> Belum Melakukan Presensi
                </div>
                <div class="metric-value">
                    <span id="statBelumHadir">{{ $belumHadir }}</span>
                    <span class="metric-sub">dari {{ $totalGuru }} guru</span>
                </div>
            </div>
        </div>

        <!-- Guru Yang Baru Presensi -->
        <div class="activity-section">
            <div class="section-header">
                <div class="section-title">
                    <div class="pulse-dot"></div>
                    Aktivitas Presensi Terkini Hari Ini
                </div>
                <div style="font-size: 13px; color: #94a3b8;">
                    Tingkat Kehadiran: <strong id="statPersentase" style="color: #34d399;">{{ $persentase }}%</strong>
                </div>
            </div>

            <div class="teachers-grid" id="teachersList">
                @forelse($terbaruCheckIn as $p)
                    <div class="teacher-card">
                        <div class="teacher-avatar">
                            <img src="{{ $p->foto_masuk_url ?? $p->guru->foto_url }}" alt="{{ $p->guru->nama }}">
                        </div>
                        <div class="teacher-info">
                            <div class="teacher-name">{{ $p->guru->nama }}</div>
                            <div class="teacher-meta">{{ $p->guru->nip ? 'NIP. '.$p->guru->nip : ($p->guru->jabatan ?: 'Guru') }}</div>
                            <div>
                                @if($p->status_kehadiran === 'dinas_luar')
                                    <span class="status-badge badge-dinas">✈️ Dinas Luar • {{ $p->jam_masuk?->format('H:i') }}</span>
                                @elseif($p->status_masuk === 'terlambat')
                                    <span class="status-badge badge-late">⚠️ Terlambat • {{ $p->jam_masuk?->format('H:i') }}</span>
                                @else
                                    <span class="status-badge badge-ontime">✓ Tepat Waktu • {{ $p->jam_masuk?->format('H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b;">
                        Belum ada data presensi yang masuk untuk hari ini.
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <!-- Footer Announcement Ticker -->
    <footer>
        <div class="ticker-label">INFO SEKOLAH</div>
        <div class="ticker-content">
            <div class="ticker-text">
                {{ $pengaturan->teks_pengumuman_display ?: 'Selamat datang di '.$pengaturan->nama_sekolah.' — Mohon seluruh Bapak/Ibu Guru dan Tenaga Kependidikan untuk melakukan presensi mandiri tepat waktu melalui smartphone masing-masing.' }}
            </div>
        </div>
    </footer>

    <script>
        // Jam Digital Real-time
        function updateClock() {
            const now = new Date();
            const timeStr = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Makassar',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).format(now);
            const el = document.getElementById('liveClock');
            if (el) {
                el.textContent = timeStr;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Polling update feed setiap 10 detik
        async function fetchLiveFeed() {
            try {
                const res = await fetch('/sekolahku/live-display/feed');
                if (!res.ok) return;
                const data = await res.json();

                document.getElementById('statTepatWaktu').textContent = data.hadirTepatWaktu;
                document.getElementById('statTerlambat').textContent = data.terlambat;
                document.getElementById('statDinasLuar').textContent = data.dinasLuar;
                document.getElementById('statIzinSakit').textContent = data.izinSakit;
                document.getElementById('statBelumHadir').textContent = data.belumHadir;
                document.getElementById('statPersentase').textContent = data.persentase + '%';

                if (data.terbaru && data.terbaru.length > 0) {
                    const escapeHtml = (str) => {
                        const d = document.createElement('div');
                        d.textContent = str || '';
                        return d.innerHTML;
                    };

                    const container = document.getElementById('teachersList');
                    container.innerHTML = data.terbaru.map(t => {
                        let badgeClass = 'badge-ontime';
                        let badgeIcon = '✓ Tepat Waktu';

                        if (t.status_kehadiran === 'dinas_luar') {
                            badgeClass = 'badge-dinas';
                            badgeIcon = '✈️ Dinas Luar';
                        } else if (t.status_masuk === 'terlambat') {
                            badgeClass = 'badge-late';
                            badgeIcon = '⚠️ Terlambat';
                        }

                        const safeNama = escapeHtml(t.nama);
                        const safeNip = escapeHtml(t.nip);
                        const safeJabatan = escapeHtml(t.jabatan);
                        const safeJam = escapeHtml(t.jam);
                        const safeFoto = encodeURI(t.foto || '');

                        return `
                            <div class="teacher-card">
                                <div class="teacher-avatar">
                                    <img src="${safeFoto}" alt="${safeNama}">
                                </div>
                                <div class="teacher-info">
                                    <div class="teacher-name">${safeNama}</div>
                                    <div class="teacher-meta">${safeNip !== 'Non-NIP' ? 'NIP. ' + safeNip : safeJabatan}</div>
                                    <div>
                                        <span class="status-badge ${badgeClass}">${badgeIcon} • ${safeJam}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                }
            } catch (err) {
                console.error('Error fetching live display feed:', err);
            }
        }
        setInterval(fetchLiveFeed, 10000);

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen().catch(() => {});
            }
        }
    </script>
</body>
</html>
