@php
    $pengaturan = $pengaturan ?? \App\Models\PengaturanSekolah::getSetting();
    $namaSekolah = $pengaturan->nama_sekolah ?? 'UPTD SPF SD Inpres Rappojawa';
    $logoSekolahUrl = $pengaturan->logo_url ?: asset('icons/icon.svg');
    $metaDescription = "Portal Presensi Resmi Guru & Tenaga Kependidikan {$namaSekolah}. Dilengkapi Geofencing GPS, Verifikasi Biometrik Wajah, Jurnal Pembelajaran, & Dokumen SPTJB Kedinasan.";
    $canonicalUrl = route('school.login');
    $ogImage = $pengaturan->logo_url ?: asset('icons/og-cover.svg');

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            array_filter([
                '@type' => 'School',
                '@id' => url('/') . '#school',
                'name' => $namaSekolah,
                'identifier' => $pengaturan->npsn ?? '40307321',
                'url' => url('/'),
                'address' => $pengaturan->alamat ? [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $pengaturan->alamat,
                    'addressCountry' => 'ID',
                ] : null,
                'telephone' => $pengaturan->telepon ?: null,
                'email' => $pengaturan->email ?: null,
                'logo' => $logoSekolahUrl,
            ]),
            [
                '@type' => 'WebApplication',
                '@id' => $canonicalUrl . '#webapp',
                'name' => 'Sistem Presensi Guru & Tendik',
                'applicationCategory' => 'EducationalApplication',
                'operatingSystem' => 'All (Web, Android, iOS)',
                'browserRequirements' => 'Requires JavaScript. Requires HTML5.',
                'url' => $canonicalUrl,
                'provider' => [
                    '@id' => url('/') . '#school',
                ],
                'description' => $metaDescription,
            ],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    
    <!-- Primary SEO Meta Tags -->
    <title>Masuk &bull; {{ $namaSekolah }} - Sistem Presensi Guru Online</title>
    <meta name="title" content="Masuk &bull; {{ $namaSekolah }} - Sistem Presensi Guru Online">
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="presensi guru, absensi online, sistem absensi sekolah, jurnal pembelajaran guru, {{ $namaSekolah }}, npsn {{ $pengaturan->npsn ?? '' }}, portal kehadiran tendik">
    <meta name="author" content="{{ $namaSekolah }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="Sistem Presensi Guru & Tendik &bull; {{ $namaSekolah }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:type" content="image/svg+xml">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Banner Sistem Presensi Guru {{ $namaSekolah }}">
    <meta property="og:site_name" content="{{ $namaSekolah }}">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $canonicalUrl }}">
    <meta name="twitter:title" content="Sistem Presensi Guru & Tendik &bull; {{ $namaSekolah }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="twitter:image:alt" content="Banner Sistem Presensi Guru {{ $namaSekolah }}">

    <!-- Icons & PWA -->
    <link rel="icon" href="{{ $logoSekolahUrl }}">
    <link rel="apple-touch-icon" href="{{ $logoSekolahUrl }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#060b13">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Schema.org JSON-LD Structured Data -->
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    <style>
        :root {
            color-scheme: dark;
            --primary: #10b981;
            --primary-light: #34d399;
            --primary-glow: rgba(16, 185, 129, 0.28);
            --teal-deep: #0f766e;
            --bg-base: #060b13;
            --bg-card: rgba(15, 23, 42, 0.82);
            --border-glass: rgba(255, 255, 255, 0.1);
            --border-glow: rgba(52, 211, 153, 0.3);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-subtle: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            padding-bottom: max(20px, env(safe-area-inset-bottom));
            padding-top: max(20px, env(safe-area-inset-top));
            color: var(--text-main);
            background-color: var(--bg-base);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Glow Mesh Background */
        .ambient-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .ambient-orb-1 {
            position: absolute;
            top: -100px;
            left: -80px;
            width: clamp(300px, 45vw, 550px);
            height: clamp(300px, 45vw, 550px);
            background: radial-gradient(circle, rgba(13, 148, 136, 0.35) 0%, rgba(6, 78, 59, 0.06) 65%, transparent 80%);
            filter: blur(50px);
            border-radius: 50%;
            animation: pulseOrb 12s ease-in-out infinite alternate;
        }

        .ambient-orb-2 {
            position: absolute;
            bottom: -120px;
            right: -80px;
            width: clamp(320px, 50vw, 600px);
            height: clamp(320px, 50vw, 600px);
            background: radial-gradient(circle, rgba(16, 185, 129, 0.26) 0%, rgba(15, 23, 42, 0.08) 65%, transparent 80%);
            filter: blur(60px);
            border-radius: 50%;
            animation: pulseOrb 15s ease-in-out infinite alternate-reverse;
        }

        .ambient-grid {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 32px 32px;
            opacity: 0.55;
            mask-image: radial-gradient(ellipse at center, black 40%, transparent 85%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 40%, transparent 85%);
        }

        @keyframes pulseOrb {
            0% { transform: scale(1) translate(0, 0); opacity: 0.7; }
            50% { transform: scale(1.08) translate(20px, -15px); opacity: 0.95; }
            100% { transform: scale(0.95) translate(-15px, 20px); opacity: 0.7; }
        }

        /* Shell Container */
        .shell {
            width: min(100%, 1080px);
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            background: var(--bg-card);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid var(--border-glass);
            border-radius: 28px;
            box-shadow: 
                0 32px 80px -16px rgba(0, 0, 0, 0.7),
                0 0 0 1px rgba(255, 255, 255, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.12);
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        /* Left Hero / Brand Section */
        .hero-section {
            padding: clamp(32px, 4vw, 54px);
            background: linear-gradient(155deg, rgba(13, 148, 136, 0.22) 0%, rgba(6, 11, 19, 0.65) 55%, rgba(6, 11, 19, 0.92) 100%);
            border-right: 1px solid var(--border-glass);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            gap: 28px;
        }

        .hero-top {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Header Brand Meta Bar */
        .brand-meta-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-emblem {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(15, 23, 42, 0.85));
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px -4px var(--primary-glow);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            flex-shrink: 0;
            overflow: hidden;
        }

        .logo-emblem img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-text-block {
            display: flex;
            flex-direction: column;
        }

        .brand-tag {
            font-family: 'Outfit', sans-serif;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #6ee7b7;
        }

        .school-title {
            font-size: 14.5px;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.01em;
            line-height: 1.25;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(52, 211, 153, 0.28);
            font-size: 11.5px;
            font-weight: 600;
            color: #6ee7b7;
            white-space: nowrap;
        }

        .pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: var(--primary-light);
            box-shadow: 0 0 8px var(--primary-light);
            animation: blink 2s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.35; transform: scale(0.8); }
        }

        /* Hero Typography */
        .hero-heading {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(26px, 3.2vw, 38px);
            font-weight: 800;
            line-height: 1.16;
            letter-spacing: -0.025em;
            color: #ffffff;
        }

        .text-gradient {
            background: linear-gradient(135deg, #34d399 0%, #10b981 60%, #2dd4bf 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 14px;
            line-height: 1.6;
            color: #cbd5e1;
            max-width: 460px;
        }

        /* Feature Cards Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 10px;
        }

        .feature-card {
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.07);
            display: flex;
            align-items: flex-start;
            gap: 10px;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .feature-card:hover {
            transform: translateY(-2px);
            border-color: rgba(52, 211, 153, 0.3);
        }

        .feature-icon-box {
            font-size: 18px;
            line-height: 1;
            padding: 6px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            flex-shrink: 0;
        }

        .feature-text h2 {
            font-size: 12.5px;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 2px;
        }

        .feature-text p {
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.35;
        }

        /* Hero Footer with Live Clock */
        .hero-footer {
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--text-subtle);
            font-size: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .clock-widget {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #a7f3d0;
            font-family: 'Outfit', monospace;
            font-weight: 700;
            font-size: 12.5px;
            background: rgba(16, 185, 129, 0.1);
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px solid rgba(52, 211, 153, 0.2);
        }

        /* Right Form Section */
        .form-section {
            padding: clamp(28px, 4.5vw, 48px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(11, 18, 30, 0.6);
            position: relative;
        }

        .form-header {
            margin-bottom: 22px;
        }

        .form-title {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .form-subtitle {
            font-size: 13.5px;
            color: var(--text-muted);
            line-height: 1.45;
        }

        /* Flash Error Message */
        .alert-error {
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(239, 68, 68, 0.14);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
            font-size: 13px;
            line-height: 1.4;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Form Fields */
        .input-group {
            margin-bottom: 16px;
        }

        .input-label {
            display: block;
            margin-bottom: 7px;
            font-size: 12.5px;
            font-weight: 600;
            color: #e2e8f0;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            width: 18px;
            height: 18px;
            color: #64748b;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        /* Input field font-size 16px prevents mobile iOS Safari auto-zoom */
        .input-field {
            width: 100%;
            height: 50px;
            padding: 0 16px 0 46px;
            border-radius: 12px;
            border: 1px solid var(--border-glass);
            background: rgba(15, 23, 42, 0.65);
            color: var(--text-main);
            font-family: inherit;
            font-size: 16px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .input-field::placeholder {
            color: #475569;
            font-size: 14px;
        }

        .input-field:focus {
            border-color: var(--primary-light);
            background: rgba(15, 23, 42, 0.95);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .input-wrapper:focus-within .input-icon {
            color: var(--primary-light);
        }

        /* Large touch-target for password toggle */
        .password-toggle-btn {
            position: absolute;
            right: 4px;
            width: 44px;
            height: 44px;
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .password-toggle-btn:hover,
        .password-toggle-btn:focus {
            color: #f1f5f9;
        }

        .password-toggle-btn svg {
            width: 20px;
            height: 20px;
        }

        /* Remember Checkbox */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 14px 0 20px;
            font-size: 13px;
        }

        .custom-checkbox {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            color: #cbd5e1;
            user-select: none;
            padding: 4px 0;
            min-height: 36px;
        }

        .custom-checkbox input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 1.5px solid rgba(255, 255, 255, 0.22);
            background: rgba(15, 23, 42, 0.8);
            cursor: pointer;
            outline: none;
            display: grid;
            place-content: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .custom-checkbox input[type="checkbox"]:checked {
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 0 10px var(--primary-glow);
        }

        .custom-checkbox input[type="checkbox"]:checked::before {
            content: "";
            width: 10px;
            height: 6px;
            border-left: 2px solid #06251e;
            border-bottom: 2px solid #06251e;
            transform: rotate(-45deg) translate(1px, -1px);
        }

        /* Submit Button with 50px touch height */
        .submit-btn {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #10b981 0%, #0d9488 100%);
            color: #04251e;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.01em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 10px 24px -4px var(--primary-glow);
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            filter: brightness(1.06);
            box-shadow: 0 14px 30px -4px var(--primary-glow);
        }

        .submit-btn:active {
            transform: scale(0.98);
        }

        /* Footer Utilities */
        .form-nav-utilities {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }

        .kiosk-link-btn {
            width: 100%;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .kiosk-link-btn:hover,
        .kiosk-link-btn:active {
            background: rgba(16, 185, 129, 0.12);
            color: #6ee7b7;
            border-color: rgba(52, 211, 153, 0.3);
        }

        .security-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            color: var(--text-subtle);
            margin-top: 4px;
            text-align: center;
        }

        .security-badge svg {
            width: 14px;
            height: 14px;
            color: #10b981;
            flex-shrink: 0;
        }

        /* ========================================================
           OPTIMALISASI KHUSUS TAMPILAN MOBILE & TABLET (<= 860px)
           ======================================================== */
        @media (max-width: 860px) {
            body {
                padding: 12px;
                padding-top: max(12px, env(safe-area-inset-top));
                padding-bottom: max(16px, env(safe-area-inset-bottom));
                align-items: flex-start;
            }

            .shell {
                display: flex;
                flex-direction: column;
                border-radius: 20px;
                width: 100%;
                margin: auto 0;
            }

            /* Pada Mobile: Form Login tampil lebih awal / prioritas utama */
            .form-section {
                order: 1;
                padding: 24px 20px 20px;
                background: rgba(11, 18, 30, 0.7);
            }

            .form-header {
                margin-bottom: 18px;
            }

            .form-title {
                font-size: 23px;
            }

            /* Compact Header di atas form pada mobile */
            .mobile-brand-header {
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 18px;
                padding-bottom: 14px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }

            /* Hero Info ditempatkan di bawah form pada Mobile agar guru tidak perlu scroll jauh */
            .hero-section {
                order: 2;
                border-right: none;
                border-top: 1px solid var(--border-glass);
                padding: 22px 20px 20px;
                background: rgba(6, 11, 19, 0.85);
                gap: 18px;
            }

            .hero-section .brand-meta-bar {
                display: none; /* Sudah ada di mobile-brand-header */
            }

            .hero-heading {
                font-size: 21px;
                line-height: 1.25;
            }

            .hero-desc {
                font-size: 13px;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 8px;
                margin-top: 4px;
            }

            .feature-card {
                padding: 10px 12px;
                border-radius: 12px;
            }

            .hero-footer {
                padding-top: 14px;
                font-size: 11.5px;
            }
        }

        /* Desktop specific styles */
        @media (min-width: 861px) {
            .mobile-brand-header {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Ambient Background Lighting -->
    <div class="ambient-bg" aria-hidden="true">
        <div class="ambient-orb-1"></div>
        <div class="ambient-orb-2"></div>
        <div class="ambient-grid"></div>
    </div>

    <!-- Main Glass Shell -->
    <main class="shell">
        <!-- Right Side on Desktop / Top Priority on Mobile: Clean Login Form -->
        <section class="form-section" aria-labelledby="form-heading">
            <!-- Mobile Compact Brand Header -->
            <div class="mobile-brand-header">
                <div class="brand-logo-wrap">
                    <div class="logo-emblem" style="width: 42px; height: 42px; padding: 4px;">
                        <img src="{{ $logoSekolahUrl }}" alt="Logo {{ $namaSekolah }}" width="32" height="32" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div class="brand-text-block">
                        <span class="brand-tag">PORTAL PRESENSI</span>
                        <span class="school-title" style="font-size: 13.5px;">{{ $namaSekolah }}</span>
                    </div>
                </div>
                <div class="status-pill" style="font-size: 10.5px; padding: 4px 10px;">
                    <span class="pulse-dot" style="width: 6px; height: 6px;"></span>
                    <span>Aktif</span>
                </div>
            </div>

            <header class="form-header">
                <h2 class="form-title" id="form-heading">Masuk ke Sistem</h2>
                <p class="form-subtitle">Gunakan NIP atau Email yang terdaftar di sekolah.</p>
            </header>

            <!-- Flash Error Message -->
            @if ($errors->any())
                <div class="alert-error" role="alert" aria-live="assertive">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Unified Login Form -->
            <form method="POST" action="{{ route('school.login.store') }}" id="loginForm" aria-label="Formulir Masuk Akun">
                @csrf

                <!-- Identity Field -->
                <div class="input-group">
                    <label class="input-label" for="identity">NIP atau Email Akun</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <input 
                            id="identity" 
                            name="identity" 
                            type="text" 
                            inputmode="text"
                            value="{{ old('identity') }}" 
                            autocomplete="username" 
                            autofocus 
                            required 
                            placeholder="NIP atau email Anda..."
                            class="input-field"
                            aria-required="true"
                        >
                    </div>
                </div>

                <!-- Password Field with Toggle -->
                <div class="input-group">
                    <label class="input-label" for="password">Password Akun</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="current-password" 
                            required 
                            placeholder="Password Anda..."
                            class="input-field"
                            aria-required="true"
                        >
                        <button type="button" class="password-toggle-btn" id="btnTogglePassword" aria-label="Lihat atau sembunyikan password">
                            <svg id="eyeIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="remember-row">
                    <label class="custom-checkbox" for="remember">
                        <input id="remember" name="remember" type="checkbox" value="1">
                        <span>Ingat perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Action Button -->
                <button type="submit" class="submit-btn" id="btn-login">
                    <span>Masuk ke Ruang Kerja</span>
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <!-- Navigation Utilities -->
            <nav class="form-nav-utilities" aria-label="Tautan Penunjang">
                <a class="kiosk-link-btn" href="{{ route('school.live-display') }}">
                    <span>📺 Buka Monitor Real-Time TV Lobi</span>
                </a>

                <div class="security-badge">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Dilindungi Enkripsi SSL 256-bit</span>
                </div>
            </nav>
        </section>

        <!-- Left Side on Desktop / Info Section on Mobile: School Branding & Features -->
        <section class="hero-section" aria-labelledby="hero-title">
            <div class="hero-top">
                <!-- Desktop Header Meta -->
                <div class="brand-meta-bar">
                    <div class="brand-logo-wrap">
                        <div class="logo-emblem">
                            <img src="{{ $logoSekolahUrl }}" alt="Logo Resmi {{ $namaSekolah }}" width="36" height="36" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <div class="brand-text-block">
                            <span class="brand-tag">PORTAL RESMI SEKOLAH</span>
                            <span class="school-title">{{ $namaSekolah }}</span>
                        </div>
                    </div>

                    <div class="status-pill" title="Server Presensi Beroperasi Normal">
                        <span class="pulse-dot"></span>
                        <span>Sistem Aktif</span>
                    </div>
                </div>

                <!-- Headline -->
                <h1 class="hero-heading" id="hero-title">
                    Presensi Cerdas, <br>
                    <span class="text-gradient">Sekolah Lebih Disiplin.</span>
                </h1>

                <p class="hero-desc">
                    Satu portal terpadu untuk Guru, Tendik, Admin, dan Kepala Sekolah. Catat kehadiran mandiri real-time dengan akurasi GPS dan foto biometrik.
                </p>

                <!-- Modern Features Grid -->
                <div class="features-grid" aria-label="Keunggulan Sistem Presensi">
                    <div class="feature-card">
                        <div class="feature-icon-box" aria-hidden="true">📍</div>
                        <div class="feature-text">
                            <h2>Geofencing GPS</h2>
                            <p>Validasi radius akurat lokasi sekolah anti fake-GPS.</p>
                        </div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon-box" aria-hidden="true">🤳</div>
                        <div class="feature-text">
                            <h2>Face Biometrics</h2>
                            <p>Selfie mandiri beresolusi tinggi dengan stempel waktu.</p>
                        </div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon-box" aria-hidden="true">📖</div>
                        <div class="feature-text">
                            <h2>Jurnal Mengajar</h2>
                            <p>Pencatatan materi ajar harian terhubung presensi.</p>
                        </div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon-box" aria-hidden="true">📄</div>
                        <div class="feature-text">
                            <h2>SPTJB Kedinasan</h2>
                            <p>Cetak rekapitulasi sah format dinas pendidikan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero Bottom: Live Clock & Date -->
            <footer class="hero-footer">
                <div>
                    <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    @if(!empty($pengaturan->npsn))
                        <span> &bull; NPSN: {{ $pengaturan->npsn }}</span>
                    @endif
                </div>
                <div class="clock-widget" id="realtimeClock" aria-label="Jam Digital Server">
                    {{ now()->format('H:i:s') }} WITA
                </div>
            </footer>
        </section>
    </main>

    <!-- Client-side Scripts -->
    <script>
        // Toggle Password Visibility
        const btnTogglePassword = document.getElementById('btnTogglePassword');
        const passwordField = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (btnTogglePassword && passwordField && eyeIcon) {
            btnTogglePassword.addEventListener('click', () => {
                const isPassword = passwordField.getAttribute('type') === 'password';
                passwordField.setAttribute('type', isPassword ? 'text' : 'password');
                
                if (isPassword) {
                    eyeIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    `;
                } else {
                    eyeIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    `;
                }
            });
        }

        // Live Clock
        function updateServerTime() {
            const now = new Date();
            const timeStr = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Makassar',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).format(now);
            const clockEl = document.getElementById('realtimeClock');
            if (clockEl) {
                clockEl.textContent = `${timeStr} WITA`;
            }
        }
        setInterval(updateServerTime, 1000);
        updateServerTime();

        // Form Submit Loading Feedback
        const loginForm = document.getElementById('loginForm');
        const btnLogin = document.getElementById('btn-login');

        if (loginForm && btnLogin) {
            loginForm.addEventListener('submit', () => {
                btnLogin.style.opacity = '0.85';
                btnLogin.style.pointerEvents = 'none';
                btnLogin.innerHTML = `
                    <svg style="animation: spin 1s linear infinite; width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Memverifikasi...</span>
                `;
            });
        }
    </script>
    <style>
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</body>
</html>