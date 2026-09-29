@php
    $namaSekolah = 'Sistem Presensi Sekolah';
    $logoUrl = asset('icons/icon.svg');
    $hasCustomLogo = false;
    try {
        if (class_exists(\App\Models\PengaturanSekolah::class)) {
            $pengaturan = rescue(fn () => \App\Models\PengaturanSekolah::getSetting(), null, false);
            if ($pengaturan) {
                $namaSekolah = $pengaturan->nama_sekolah ?: 'Sistem Presensi Sekolah';
                if ($pengaturan->logo_url) {
                    $logoUrl = $pengaturan->logo_url;
                    $hasCustomLogo = true;
                }
            }
        }
    } catch (\Throwable $e) {
        $namaSekolah = 'Sistem Presensi Sekolah';
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Terjadi Kesalahan') &bull; {{ $namaSekolah }}</title>
    
    <link rel="icon" href="{{ $logoUrl }}">
    <link rel="apple-touch-icon" href="{{ $logoUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-dark: #07131b;
            --bg-card: rgba(15, 23, 42, 0.75);
            --border-card: rgba(255, 255, 255, 0.12);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary-emerald: #10b981;
            --primary-teal: #14b8a6;
            --danger-rose: #f43f5e;
            --warning-amber: #f59e0b;
            --info-cyan: #06b6d4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Glow Background Orbs */
        .ambient-orb {
            position: absolute;
            border-radius: 9999px;
            pointer-events: none;
            filter: blur(80px);
            z-index: 0;
        }

        .orb-1 {
            top: -10%;
            left: -10%;
            width: 45vw;
            height: 45vw;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.22) 0%, rgba(16, 185, 129, 0) 70%);
        }

        .orb-2 {
            bottom: -10%;
            right: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.18) 0%, rgba(6, 182, 212, 0) 70%);
        }

        .orb-3 {
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 35vw;
            height: 35vw;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, rgba(99, 102, 241, 0) 70%);
        }

        /* Container Card */
        .error-card {
            position: relative;
            z-index: 10;
            max-width: 640px;
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 1.5rem;
            padding: 2.5rem 2.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Top Bar Meta */
        .top-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.8125rem;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #d1fae5;
            font-weight: 700;
        }

        .brand-icon {
            width: 1.75rem;
            height: 1.75rem;
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(52, 211, 153, 0.35);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #34d399;
        }

        .clock-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
            color: #a7f3d0;
            font-weight: 600;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        .pulse-dot {
            width: 0.45rem;
            height: 0.45rem;
            border-radius: 9999px;
            background-color: #34d399;
            box-shadow: 0 0 6px #34d399;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        /* Error Code Hero */
        .code-wrapper {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .error-code {
            font-size: clamp(4.5rem, 12vw, 6.5rem);
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.05em;
            background: linear-gradient(135deg, #ffffff 30%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            margin-top: 0.5rem;
        }

        .badge-danger {
            background: rgba(244, 63, 94, 0.15);
            border: 1px solid rgba(244, 63, 94, 0.35);
            color: #fda4af;
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.35);
            color: #fcd34d;
        }

        .badge-info {
            background: rgba(6, 182, 212, 0.15);
            border: 1px solid rgba(6, 182, 212, 0.35);
            color: #67e8f9;
        }

        /* Content Text */
        .error-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }

        .error-desc {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--text-muted);
            max-width: 480px;
            margin-bottom: 2rem;
        }

        /* Actions Hub */
        .action-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.35rem;
            border-radius: 0.85rem;
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff !important;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.5);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #f1f5f9 !important;
            border: 1px solid rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.35);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: #cbd5e1 !important;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff !important;
        }

        /* Footer Meta Info */
        .footer-info {
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            color: #64748b;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .footer-path {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 0.35rem;
            color: #94a3b8;
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 640px) {
            .error-card {
                padding: 1.75rem 1.25rem;
            }
            .top-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .action-group {
                flex-direction: column;
                width: 100%;
            }
            .action-group .btn {
                width: 100%;
            }
            .footer-info {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="ambient-orb orb-1"></div>
    <div class="ambient-orb orb-2"></div>
    <div class="ambient-orb orb-3"></div>

    <main class="error-card" role="main">
        <!-- Top Bar Meta -->
        <div class="top-meta">
            <div class="brand-badge">
                <div class="brand-icon" style="overflow: hidden; padding: 2px;">
                    @if($hasCustomLogo)
                        <img src="{{ $logoUrl }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
                    @else
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    @endif
                </div>
                <span>{{ $namaSekolah }}</span>
            </div>

            <div class="clock-pill">
                <span class="pulse-dot"></span>
                <span id="liveClockWita">{{ now()->format('H:i:s') }} WITA</span>
            </div>
        </div>

        <!-- Error Code and Badge -->
        <div class="code-wrapper">
            <div class="error-code">@yield('code', '404')</div>
            <div class="badge-status @yield('badge-class', 'badge-info')">
                @yield('badge-text', 'Status Kesalahan')
            </div>
        </div>

        <!-- Title and Empathetic Description -->
        <h1 class="error-title">@yield('title', 'Terjadi Kendala')</h1>
        <p class="error-desc">
            @yield('message', 'Permintaan Anda saat ini tidak dapat diproses oleh sistem.')
        </p>

        <!-- Navigation Buttons -->
        <div class="action-group">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Halaman Utama</span>
            </a>

            <a href="{{ route('school.login') }}" class="btn btn-secondary">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <span>Portal Login</span>
            </a>

            <button type="button" onclick="window.location.reload()" class="btn btn-outline" title="Muat ulang halaman saat ini">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Muat Ulang</span>
            </button>

            <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='/'" class="btn btn-outline" title="Kembali ke halaman sebelumnya">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali</span>
            </button>
        </div>

        <!-- Footer Meta -->
        <div class="footer-info">
            <span>Sistem Presensi &bull; {{ $namaSekolah }}</span>
            @if(request()->path())
                <span class="footer-path" title="URL yang diakses: /{{ request()->path() }}">
                    /{{ request()->path() }}
                </span>
            @endif
        </div>
    </main>

    <script>
        // Live Real-Time Clock in WITA (UTC+8)
        function updateErrorClock() {
            const now = new Date();
            const timeStr = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Makassar',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).format(now);
            const el = document.getElementById('liveClockWita');
            if (el) {
                el.textContent = `${timeStr} WITA`;
            }
        }
        setInterval(updateErrorClock, 1000);
        updateErrorClock();
    </script>
</body>
</html>
