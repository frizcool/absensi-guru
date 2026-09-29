@php
    $pengaturan = \App\Models\PengaturanSekolah::getSetting();
    $namaSekolah = $pengaturan->nama_sekolah ?: 'UPTD SPF SD Inpres Rappojawa';
    $logoUrl = $pengaturan->logo_url ?: asset('icons/icon.svg');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Presensi & Absensi Guru Pintar &bull; {{ $namaSekolah }}</title>

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d9488">
    <link rel="icon" href="{{ $logoUrl }}">
    <link rel="apple-touch-icon" href="{{ $logoUrl }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <style>
        *, ::before, ::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #070a12;
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Glow & Grid Background */
        .bg-grid {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            opacity: 0.6;
        }
        .glow-bg {
            position: absolute;
            width: 550px;
            height: 550px;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            filter: blur(80px);
        }
        .glow-1 {
            top: -180px;
            right: -100px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.28) 0%, rgba(13, 148, 136, 0.1) 60%, rgba(0,0,0,0) 80%);
        }
        .glow-2 {
            bottom: -180px;
            left: -100px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.22) 0%, rgba(14, 165, 233, 0.08) 60%, rgba(0,0,0,0) 80%);
        }
        .glow-3 {
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, rgba(0,0,0,0) 70%);
        }

        .container {
            width: 100%;
            max-width: 1160px;
            margin: 0 auto;
            padding: 24px;
            position: relative;
            z-index: 10;
        }

        /* Navbar */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(8px);
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: #ffffff;
        }
        .brand-logo {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, #10b981 0%, #0d9488 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .brand-text h1 {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -0.3px;
        }
        .brand-text p {
            font-size: 11.5px;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Live Clock Badge */
        .live-clock-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 8px 16px;
            border-radius: 9999px;
            font-size: 12.5px;
            font-weight: 700;
            color: #e2e8f0;
            backdrop-filter: blur(12px);
        }
        .clock-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 10px #10b981;
            animation: pulse 1.8s infinite;
        }

        /* Hero */
        .hero {
            text-align: center;
            padding: 54px 0 36px 0;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 18px;
            border-radius: 9999px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #34d399;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin-bottom: 22px;
            text-transform: uppercase;
        }
        .hero-title {
            font-size: clamp(30px, 5.5vw, 50px);
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: -1px;
            color: #ffffff;
            margin-bottom: 18px;
        }
        .hero-title span {
            background: linear-gradient(135deg, #34d399 0%, #2dd4bf 50%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-desc {
            font-size: 15.5px;
            color: #94a3b8;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.65;
            font-weight: 400;
        }

        /* Portal Cards Grid */
        .portal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 26px;
            margin: 38px 0;
        }
        .portal-card-admin, .demo-box {
            display: none;
        }
        .portal-card-guru {
            max-width: 760px;
            margin: 0 auto;
            width: 100%;
        }
        .portal-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 34px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .portal-card:hover {
            transform: translateY(-6px);
            border-color: rgba(16, 185, 129, 0.45);
            box-shadow: 0 25px 45px -10px rgba(16, 185, 129, 0.2);
        }
        .portal-card-guru {
            border-color: rgba(16, 185, 129, 0.3);
            background: linear-gradient(180deg, rgba(16, 185, 129, 0.1) 0%, rgba(15, 23, 42, 0.75) 100%);
        }
        .portal-card-admin {
            border-color: rgba(56, 189, 248, 0.3);
            background: linear-gradient(180deg, rgba(56, 189, 248, 0.1) 0%, rgba(15, 23, 42, 0.75) 100%);
        }
        .portal-card-admin:hover {
            border-color: rgba(56, 189, 248, 0.45);
            box-shadow: 0 25px 45px -10px rgba(56, 189, 248, 0.2);
        }
        .portal-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin-bottom: 22px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .portal-icon-guru {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.5);
            color: #34d399;
        }
        .portal-icon-admin {
            background: rgba(56, 189, 248, 0.2);
            border: 1px solid rgba(56, 189, 248, 0.5);
            color: #38bdf8;
        }
        .portal-title {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 10px;
            letter-spacing: -0.4px;
        }
        .portal-text {
            font-size: 13.5px;
            color: #94a3b8;
            line-height: 1.55;
            margin-bottom: 24px;
        }
        .portal-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 30px;
        }
        .portal-feature {
            font-size: 13px;
            color: #cbd5e1;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }
        .portal-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 26px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .portal-btn-guru {
            background: linear-gradient(135deg, #10b981 0%, #0d9488 100%);
            color: #ffffff;
            box-shadow: 0 4px 18px rgba(16, 185, 129, 0.35);
        }
        .portal-btn-guru:hover {
            background: linear-gradient(135deg, #059669 0%, #0f766e 100%);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
        }
        .portal-btn-admin {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            box-shadow: 0 4px 18px rgba(2, 132, 199, 0.35);
        }
        .portal-btn-admin:hover {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
            box-shadow: 0 8px 25px rgba(2, 132, 199, 0.5);
        }

        /* Demo Accounts Box */
        .demo-box {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 22px;
            padding: 26px;
            margin-top: 14px;
            backdrop-filter: blur(16px);
        }
        .demo-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }
        .demo-title {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .demo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 14px;
        }
        .demo-item {
            background: rgba(255, 255, 255, 0.035);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 14px;
            padding: 14px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }
        .demo-item:hover {
            background: rgba(16, 185, 129, 0.08);
            border-color: rgba(16, 185, 129, 0.35);
            transform: translateY(-2px);
        }
        .demo-role {
            font-size: 11.5px;
            font-weight: 800;
            color: #34d399;
            margin-bottom: 3px;
        }
        .demo-email {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        }
        .demo-pwd {
            font-size: 11.5px;
            color: #94a3b8;
            margin-top: 2px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .demo-copy-tag {
            font-size: 10px;
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            padding: 2px 6px;
            border-radius: 6px;
            font-weight: 700;
        }

        /* Features Section */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
            margin: 32px 0;
        }
        .feature-card {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 18px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }
        .feature-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .feature-h4 {
            font-size: 15px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 6px;
        }
        .feature-p {
            font-size: 12.5px;
            color: #94a3b8;
            line-height: 1.5;
        }

        /* Toast Alert */
        .toast-copied {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #10b981;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            display: none;
            z-index: 100;
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 28px 0;
            font-size: 12.5px;
            color: #64748b;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.85); }
        }
    </style>
</head>
<body>
    <div class="bg-grid"></div>
    <div class="glow-bg glow-1"></div>
    <div class="glow-bg glow-2"></div>
    <div class="glow-bg glow-3"></div>

    <div class="container">
        <!-- Header / Navbar -->
        <header class="navbar">
            <a href="/" class="nav-brand">
                <div class="brand-logo" style="overflow: hidden; padding: 4px;">
                    @if($pengaturan->logo_url)
                        <img src="{{ $pengaturan->logo_url }}" alt="Logo {{ $namaSekolah }}" style="width: 100%; height: 100%; object-fit: contain;">
                    @else
                        🏫
                    @endif
                </div>
                <div class="brand-text">
                    <h1>{{ $namaSekolah }}</h1>
                    <p>Ruang kerja digital untuk presensi dan administrasi sekolah</p>
                </div>
            </a>

            <div class="live-clock-badge">
                <span class="clock-indicator"></span>
                <span id="live-time-display">--:--:-- WITA</span>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-badge">
                <span class="clock-indicator"></span>
                <span>SMART BIOMETRIC & GEOFENCE ATTENDANCE</span>
            </div>
            <h2 class="hero-title">
                Satu pintu untuk seluruh aktivitas sekolah<br>
                <span>Presensi, laporan, dan pengelolaan yang terhubung</span>
            </h2>
            <p class="hero-desc">
                Kelola kehadiran guru dengan alur yang ringkas, data yang terukur, dan akses yang sesuai peran setiap pengguna.
            </p>
        </section>

        <!-- Portal Cards Grid -->
        <div class="portal-grid">
            <!-- Card 1: Portal Guru -->
            <div class="portal-card portal-card-guru">
                <div>
                    <div class="portal-icon portal-icon-guru">↗</div>
                    <h3 class="portal-title">Masuk ke ruang kerja Anda</h3>
                    <p class="portal-text">
                        Satu halaman login untuk Guru, Admin, Kepala Sekolah, dan Super Admin. Sistem otomatis membuka panel sesuai hak akses Anda.
                    </p>
                    <ul class="portal-features">
                        <li class="portal-feature"><span>01</span> Presensi dan riwayat kehadiran</li>
                        <li class="portal-feature"><span>02</span> Pengajuan izin dan persetujuan</li>
                        <li class="portal-feature"><span>03</span> Laporan dan pemantauan terpusat</li>
                        <li class="portal-feature"><span>04</span> Akses nyaman dari ponsel maupun desktop</li>
                    </ul>
                </div>
                <a href="{{ route('school.login') }}" class="portal-btn portal-btn-guru">
                    <span>Masuk ke Sistem</span>
                    <span>→</span>
                </a>
            </div>

            <!-- Card 2: Panel Admin & Kepsek -->
            <div class="portal-card portal-card-admin">
                <div>
                    <div class="portal-icon portal-icon-admin">⚙️</div>
                    <h3 class="portal-title">Panel Admin & Kepala Sekolah</h3>
                    <p class="portal-text">
                        Pusat monitoring kedisiplinan eksekutif, rekapitulasi kehadiran bulanan (Excel/PDF), approval pengajuan izin, dan pengaturan radius sekolah.
                    </p>
                    <ul class="portal-features">
                        <li class="portal-feature"><span>📊</span> Dashboard Realtime KPI & Leaderboard Kedisiplinan</li>
                        <li class="portal-feature"><span>📑</span> Matriks Laporan Bulanan Lengkap (PDF & OpenSpout Excel)</li>
                        <li class="portal-feature"><span>🗺️</span> Peta Interaktif Geofencing GPS Radius Sekolah</li>
                        <li class="portal-feature"><span>💬</span> Notifikasi WhatsApp Otomatis ke Kepala Sekolah</li>
                        <li class="portal-feature"><span>🕒</span> Manajemen Multi-Shift & Jadwal Khusus Guru</li>
                    </ul>
                </div>
                <a href="{{ route('school.login') }}" class="portal-btn portal-btn-admin">
                    <span>Masuk ke Panel Admin</span>
                    <span>→</span>
                </a>
            </div>
        </div>

        <!-- Highlight Features Grid -->
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📸</div>
                <h4 class="feature-h4">Verifikasi Wajah Biometrik</h4>
                <p class="feature-p">Kamera selfie HUD laser scanner memastikan presensi dilakukan secara otentik oleh guru bersangkutan.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h4 class="feature-h4">Geofencing Akurat</h4>
                <p class="feature-p">Peta koordinat Leaflet GPS membatasi radius presensi agar check-in hanya sah saat berada di lingkungan sekolah.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💬</div>
                <h4 class="feature-h4">Notifikasi WhatsApp</h4>
                <p class="feature-p">Pengiriman rekap otomatis harian dan peringatan keterlambatan berulang ke nomor WhatsApp Kepala Sekolah.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h4 class="feature-h4">Rekapitulasi Siap Cetak</h4>
                <p class="feature-p">Ekspor matriks kehadiran 31 hari ke Excel berkecepatan tinggi dan slip cetak resmi berkop sekolah.</p>
            </div>
        </div>

        <!-- Akun Demo untuk Pengujian Cepat -->
        <div class="demo-box">
            <div class="demo-header">
                <div class="demo-title">
                    <span>🔑</span>
                    <span>Akun Demo Login Pengujian Sistem (Klik untuk Salin Akun & Password):</span>
                </div>
                <span class="demo-copy-tag">1-Klik Salin</span>
            </div>
            <div class="demo-grid">
                <div class="demo-item" onclick="copyAccount('guru@sekolah.sch.id', 'password', 'Portal Guru')">
                    <div class="demo-role">👨‍🏫 Guru (Portal Guru)</div>
                    <div class="demo-email">guru@sekolah.sch.id</div>
                    <div class="demo-pwd">
                        <span>Password: <b style="color: #e2e8f0;">password</b></span>
                        <span class="demo-copy-tag">Salin</span>
                    </div>
                </div>
                <div class="demo-item" onclick="copyAccount('kepsek@sekolah.sch.id', 'password', 'Kepala Sekolah')">
                    <div class="demo-role">🎓 Kepala Sekolah (Panel Admin)</div>
                    <div class="demo-email">kepsek@sekolah.sch.id</div>
                    <div class="demo-pwd">
                        <span>Password: <b style="color: #e2e8f0;">password</b></span>
                        <span class="demo-copy-tag">Salin</span>
                    </div>
                </div>
                <div class="demo-item" onclick="copyAccount('admin@sekolah.sch.id', 'password', 'Admin Sekolah')">
                    <div class="demo-role">⚙️ Admin Sekolah (Panel Admin)</div>
                    <div class="demo-email">admin@sekolah.sch.id</div>
                    <div class="demo-pwd">
                        <span>Password: <b style="color: #e2e8f0;">password</b></span>
                        <span class="demo-copy-tag">Salin</span>
                    </div>
                </div>
                <div class="demo-item" onclick="copyAccount('superadmin@sekolah.sch.id', 'password', 'Super Admin')">
                    <div class="demo-role">👑 Super Admin (Semua Akses)</div>
                    <div class="demo-email">superadmin@sekolah.sch.id</div>
                    <div class="demo-pwd">
                        <span>Password: <b style="color: #e2e8f0;">password</b></span>
                        <span class="demo-copy-tag">Salin</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast-copied">
        ✓ Akun berhasil disalin ke clipboard!
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            &copy; {{ date('Y') }} Sistem Presensi Guru Pintar &bull; UPTD SPF SD Inpres Rappojawa Makassar &bull; Biometric & GPS Platform
        </div>
    </footer>

    <script>
        // Realtime Clock
        function updateClock() {
            const now = new Date();
            const timeStr = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Makassar',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).format(now);
            const dateStr = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Makassar',
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            }).format(now);
            const el = document.getElementById('live-time-display');
            if (el) {
                el.innerText = `${dateStr} • ${timeStr} WITA`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 1-Click Copy Credential
        function copyAccount(email, password, role) {
            navigator.clipboard.writeText(email).then(() => {
                const toast = document.getElementById('toast');
                toast.innerText = `✓ Email ${role} (${email}) disalin! Password: ${password}`;
                toast.style.display = 'block';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 3500);
            });
        }
    </script>
</body>
</html>
