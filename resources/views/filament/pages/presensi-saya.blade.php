<x-filament-panels::page>
    <div class="pg-root-wrapper">
        <!-- External CDN Assets for Leaflet Map -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <style>
            /* Design System: Presensi Mandiri Guru (Self-Contained Scoped CSS) */
            :root {
                --pg-card-bg: #ffffff;
                --pg-card-border: #e2e8f0;
                --pg-text-title: #0f172a;
                --pg-text-sub: #64748b;
                --pg-primary: #059669;
                --pg-primary-light: #10b981;
                --pg-accent-glow: rgba(16, 185, 129, 0.2);
            }

            .dark, html.dark, .fi-theme-dark {
                --pg-card-bg: #111827;
                --pg-card-border: #1f2937;
                --pg-text-title: #f8fafc;
                --pg-text-sub: #94a3b8;
                --pg-primary: #10b981;
                --pg-primary-light: #34d399;
                --pg-accent-glow: rgba(16, 185, 129, 0.3);
            }

            .pg-container {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                width: 100%;
            }

            /* PWA Install Banner */
            .pg-pwa-banner {
                background: linear-gradient(135deg, #0d9488 0%, #059669 50%, #047857 100%);
                border-radius: 1rem;
                padding: 1rem 1.25rem;
                color: #ffffff;
                box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.35);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                flex-wrap: wrap;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .pg-pwa-content {
                display: flex;
                align-items: center;
                gap: 0.85rem;
            }

            .pg-pwa-icon-box {
                background: rgba(255, 255, 255, 0.2);
                border-radius: 0.75rem;
                padding: 0.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .pg-pwa-actions {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .pg-btn-pwa-install {
                background: #ffffff;
                color: #0f766e;
                font-weight: 700;
                font-size: 0.75rem;
                padding: 0.45rem 0.95rem;
                border-radius: 0.6rem;
                border: none;
                cursor: pointer;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                transition: all 0.2s ease;
            }

            .pg-btn-pwa-install:hover {
                background: #f0fdfa;
                transform: translateY(-1px);
            }

            .pg-btn-pwa-close {
                background: transparent;
                color: rgba(255, 255, 255, 0.8);
                border: none;
                cursor: pointer;
                font-size: 0.75rem;
                padding: 0.45rem 0.65rem;
                transition: color 0.2s;
            }

            .pg-btn-pwa-close:hover {
                color: #ffffff;
            }

            /* 1. Hero Grid */
            .pg-hero-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 1.25rem;
            }
            @media (min-width: 1024px) {
                .pg-hero-grid {
                    grid-template-columns: 1.15fr 0.85fr;
                }
            }

            /* Cyber Digital Clock Card */
            .pg-clock-card {
                background: linear-gradient(135deg, #044e3a 0%, #065f46 50%, #0f172a 100%);
                border-radius: 1.25rem;
                padding: 1.5rem 1.75rem;
                color: #ffffff;
                position: relative;
                overflow: hidden;
                box-shadow: 0 16px 32px -8px rgba(4, 78, 58, 0.4);
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                min-height: 200px;
                border: 1px solid rgba(255, 255, 255, 0.15);
            }

            .pg-clock-card::before {
                content: '';
                position: absolute;
                top: -60px;
                right: -60px;
                width: 220px;
                height: 220px;
                background: radial-gradient(circle, rgba(52, 211, 153, 0.35) 0%, rgba(16, 185, 129, 0) 70%);
                border-radius: 50%;
                pointer-events: none;
            }

            .pg-clock-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
                position: relative;
                z-index: 5;
            }

            .pg-live-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                padding: 0.3rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 700;
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(8px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .pg-btn-voice-toggle {
                font-size: 0.75rem;
                font-weight: 600;
                padding: 0.3rem 0.7rem;
                border-radius: 0.5rem;
                background: rgba(0, 0, 0, 0.25);
                color: rgba(255, 255, 255, 0.9);
                border: 1px solid rgba(255, 255, 255, 0.1);
                cursor: pointer;
                transition: all 0.2s;
            }

            .pg-btn-voice-toggle:hover {
                background: rgba(0, 0, 0, 0.4);
            }

            .pg-clock-time {
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
                font-size: clamp(2.2rem, 4vw, 3rem);
                font-weight: 900;
                letter-spacing: -0.04em;
                line-height: 1.1;
                text-shadow: 0 0 25px rgba(52, 211, 153, 0.65);
                color: #ffffff;
                display: flex;
                align-items: baseline;
                gap: 0.5rem;
                margin: 0.65rem 0 0.35rem 0;
            }

            .pg-clock-date {
                font-size: 0.8125rem;
                color: #a7f3d0;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 0.4rem;
                margin: 0;
            }

            .pg-clock-footer {
                padding-top: 0.75rem;
                margin-top: 0.75rem;
                border-top: 1px solid rgba(255, 255, 255, 0.15);
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-size: 0.75rem;
                flex-wrap: wrap;
                gap: 0.5rem;
                position: relative;
                z-index: 5;
            }

            /* Teacher Profile Card */
            .pg-profile-card {
                background: var(--pg-card-bg);
                border: 1px solid var(--pg-card-border);
                border-radius: 1.25rem;
                padding: 1.35rem;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 1rem;
            }

            .pg-profile-top {
                display: flex;
                align-items: flex-start;
                gap: 1rem;
            }

            .pg-avatar-wrapper {
                position: relative;
                flex-shrink: 0;
            }

            .pg-avatar-img {
                width: 4rem;
                height: 4rem;
                border-radius: 1rem;
                object-fit: cover;
                border: 2px solid var(--pg-primary);
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            .pg-avatar-badge-online {
                position: absolute;
                bottom: -0.2rem;
                right: -0.2rem;
                width: 0.95rem;
                height: 0.95rem;
                background-color: #10b981;
                border: 2px solid var(--pg-card-bg);
                border-radius: 9999px;
            }

            .pg-profile-info {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
                min-width: 0;
                flex: 1;
            }

            .pg-profile-header-row {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                flex-wrap: wrap;
            }

            .pg-profile-name {
                font-size: 1.05rem;
                font-weight: 800;
                color: var(--pg-text-title);
                margin: 0;
                line-height: 1.3;
            }

            .pg-badge-emp {
                display: inline-flex;
                padding: 0.15rem 0.55rem;
                border-radius: 0.4rem;
                font-size: 0.6875rem;
                font-weight: 800;
                text-transform: uppercase;
                background: #d1fae5;
                color: #065f46;
            }

            .dark .pg-badge-emp {
                background: rgba(6, 95, 70, 0.6);
                color: #6ee7b7;
            }

            .pg-profile-desc {
                font-size: 0.75rem;
                color: var(--pg-text-sub);
                margin: 0;
            }

            /* Live Geofence GPS Row */
            .pg-gps-status-row {
                padding-top: 0.75rem;
                border-top: 1px solid var(--pg-card-border);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.65rem;
                flex-wrap: wrap;
            }

            .pg-gps-badge-in {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0.35rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 700;
                background: #d1fae5;
                color: #065f46;
                border: 1px solid #a7f3d0;
            }

            .dark .pg-gps-badge-in {
                background: rgba(6, 95, 70, 0.4);
                color: #6ee7b7;
                border-color: rgba(52, 211, 153, 0.3);
            }

            .pg-gps-badge-out {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0.35rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 700;
                background: #ffe4e6;
                color: #9f1239;
                border: 1px solid #fecdd3;
            }

            .dark .pg-gps-badge-out {
                background: rgba(159, 18, 57, 0.4);
                color: #fda4af;
                border-color: rgba(244, 63, 94, 0.3);
            }

            .pg-btn-refresh-gps {
                font-size: 0.75rem;
                font-weight: 700;
                color: var(--pg-primary);
                background: transparent;
                border: 1px solid var(--pg-card-border);
                padding: 0.35rem 0.75rem;
                border-radius: 0.5rem;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                transition: all 0.2s;
            }

            .pg-btn-refresh-gps:hover:not(:disabled) {
                background: rgba(16, 185, 129, 0.1);
            }

            .pg-btn-refresh-gps:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }

            /* Accuracy Level Badges */
            .pg-accuracy-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                font-size: 0.6875rem;
                font-weight: 700;
                padding: 0.2rem 0.55rem;
                border-radius: 9999px;
            }

            .pg-accuracy-high {
                background: #ecfdf5;
                color: #065f46;
                border: 1px solid #a7f3d0;
            }

            .dark .pg-accuracy-high {
                background: rgba(6, 95, 70, 0.4);
                color: #6ee7b7;
                border-color: rgba(52, 211, 153, 0.3);
            }

            .pg-accuracy-medium {
                background: #fffbeb;
                color: #92400e;
                border: 1px solid #fde68a;
            }

            .dark .pg-accuracy-medium {
                background: rgba(146, 64, 14, 0.4);
                color: #fcd34d;
                border-color: rgba(245, 158, 11, 0.3);
            }

            .pg-accuracy-low {
                background: #fff7ed;
                color: #9a3412;
                border: 1px solid #fed7aa;
            }

            .dark .pg-accuracy-low {
                background: rgba(154, 52, 18, 0.4);
                color: #fdba74;
                border-color: rgba(249, 115, 22, 0.3);
            }

            /* 2. Main Interactive Grid */
            .pg-main-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 1.25rem;
            }
            @media (min-width: 1024px) {
                .pg-main-grid {
                    grid-template-columns: 1fr 1fr;
                }
            }

            .pg-panel-card {
                background: var(--pg-card-bg);
                border: 1px solid var(--pg-card-border);
                border-radius: 1.25rem;
                padding: 1.35rem;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 1rem;
            }

            .pg-panel-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
                margin-bottom: 0.75rem;
            }

            .pg-panel-title {
                font-size: 0.8125rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                color: var(--pg-text-title);
                display: flex;
                align-items: center;
                gap: 0.45rem;
                margin: 0;
            }

            .pg-btn-small {
                font-size: 0.75rem;
                font-weight: 600;
                padding: 0.25rem 0.6rem;
                border-radius: 0.5rem;
                background: #f1f5f9;
                color: #334155;
                border: 1px solid #cbd5e1;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                transition: all 0.2s;
            }

            .dark .pg-btn-small {
                background: #1e293b;
                color: #cbd5e1;
                border-color: #334155;
            }

            .pg-btn-small:hover {
                background: #e2e8f0;
                color: #0f172a;
            }

            .dark .pg-btn-small:hover {
                background: #334155;
                color: #f8fafc;
            }

            /* Camera Scanner Box */
            .pg-camera-wrapper {
                position: relative;
                width: 100%;
                aspect-ratio: 4 / 3;
                background: #020617;
                border-radius: 1rem;
                overflow: hidden;
                box-shadow: inset 0 0 35px rgba(0, 0, 0, 0.85), 0 8px 25px rgba(0, 0, 0, 0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                border: 2px solid rgba(16, 185, 129, 0.35);
            }

            .pg-camera-video {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transform: scaleX(-1);
            }

            .pg-camera-video.env-mode {
                transform: scaleX(1);
            }

            @keyframes laserScan {
                0% { top: 6%; opacity: 0.2; }
                50% { top: 88%; opacity: 1; }
                100% { top: 6%; opacity: 0.2; }
            }

            .pg-laser-line {
                position: absolute;
                left: 5%;
                width: 90%;
                height: 3px;
                background: linear-gradient(90deg, rgba(16, 185, 129, 0) 0%, #10b981 50%, rgba(16, 185, 129, 0) 100%);
                box-shadow: 0 0 15px #10b981, 0 0 30px #10b981;
                animation: laserScan 2.2s ease-in-out infinite;
                pointer-events: none;
                z-index: 15;
            }

            .pg-hud-corners {
                position: absolute;
                inset: 16px;
                border: 2px dashed rgba(52, 211, 153, 0.4);
                border-radius: 14px;
                pointer-events: none;
                z-index: 10;
            }

            .pg-hud-corners::before,
            .pg-hud-corners::after {
                content: '';
                position: absolute;
                width: 20px;
                height: 20px;
                border-color: #34d399;
                border-style: solid;
            }

            .pg-hud-corners::before {
                top: -2px;
                left: -2px;
                border-width: 3px 0 0 3px;
                border-top-left-radius: 10px;
            }

            .pg-hud-corners::after {
                bottom: -2px;
                right: -2px;
                border-width: 0 3px 3px 0;
                border-bottom-right-radius: 10px;
            }

            /* Shutter Flash Animation */
            .pg-shutter-flash {
                position: absolute;
                inset: 0;
                background-color: #ffffff;
                opacity: 0;
                pointer-events: none;
                z-index: 30;
                transition: opacity 0.15s ease-out;
            }

            .pg-shutter-flash.flash-active {
                opacity: 0.85;
            }

            .pg-btn-snap-photo {
                padding: 0.65rem 1.35rem;
                font-size: 0.8125rem;
                font-weight: 800;
                border-radius: 0.75rem;
                color: #ffffff;
                background: linear-gradient(135deg, #059669 0%, #10b981 100%);
                border: none;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
                transition: all 0.2s ease;
                width: 100%;
                max-width: 320px;
            }

            .pg-btn-snap-photo:hover:not(:disabled) {
                transform: translateY(-1px);
                box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
            }

            .pg-btn-snap-photo:disabled {
                opacity: 0.5;
                cursor: not-allowed;
                background: #64748b;
                box-shadow: none;
                transform: none;
            }

            .pg-btn-retake-photo {
                padding: 0.55rem 1rem;
                font-size: 0.75rem;
                font-weight: 700;
                border-radius: 0.65rem;
                color: #e11d48;
                background: #fff1f2;
                border: 1px solid #fda4af;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                transition: all 0.2s ease;
            }

            .pg-btn-retake-photo:hover {
                background: #ffe4e6;
                color: #be123c;
            }

            .dark .pg-btn-retake-photo {
                background: rgba(225, 29, 72, 0.15);
                border-color: rgba(244, 63, 94, 0.3);
                color: #fda4af;
            }

            /* Leaflet Interactive Map Container */
            #pg-leaflet-map {
                width: 100%;
                height: 270px;
                border-radius: 1rem;
                border: 1px solid var(--pg-card-border);
                z-index: 1;
                background-color: #f1f5f9;
            }

            .dark #pg-leaflet-map {
                background-color: #0f172a;
            }

            /* Big Action Buttons */
            .pg-btn-checkin {
                width: 100%;
                padding: 1rem 1.5rem;
                border-radius: 1rem;
                font-size: 0.95rem;
                font-weight: 800;
                letter-spacing: 0.04em;
                color: #ffffff;
                background: linear-gradient(135deg, #059669 0%, #0d9488 100%);
                border: none;
                cursor: pointer;
                box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.45);
                transition: all 0.25s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
            }

            .pg-btn-checkin:hover:not(:disabled) {
                transform: translateY(-2px);
                box-shadow: 0 15px 30px -5px rgba(5, 150, 105, 0.6);
                background: linear-gradient(135deg, #047857 0%, #0f766e 100%);
            }

            .pg-btn-checkin:disabled {
                opacity: 0.6;
                cursor: not-allowed;
                transform: none;
            }

            .pg-btn-checkout {
                width: 100%;
                padding: 1rem 1.5rem;
                border-radius: 1rem;
                font-size: 0.95rem;
                font-weight: 800;
                letter-spacing: 0.04em;
                color: #ffffff;
                background: linear-gradient(135deg, #d97706 0%, #ea580c 100%);
                border: none;
                cursor: pointer;
                box-shadow: 0 10px 25px -5px rgba(217, 119, 6, 0.45);
                transition: all 0.25s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
            }

            .pg-btn-checkout:hover:not(:disabled) {
                transform: translateY(-2px);
                box-shadow: 0 15px 30px -5px rgba(217, 119, 6, 0.6);
                background: linear-gradient(135deg, #b45309 0%, #c2410c 100%);
            }

            .pg-btn-checkout:disabled {
                opacity: 0.6;
                cursor: not-allowed;
                transform: none;
            }

            /* Completed Status Card */
            .pg-completed-box {
                background: #ecfdf5;
                border: 1px solid #a7f3d0;
                border-radius: 1rem;
                padding: 1.15rem;
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .dark .pg-completed-box {
                background: rgba(6, 95, 70, 0.35);
                border-color: rgba(52, 211, 153, 0.3);
            }

            .pg-completed-title {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                color: #065f46;
                font-weight: 800;
                font-size: 0.875rem;
            }

            .dark .pg-completed-title {
                color: #6ee7b7;
            }

            .pg-completed-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .pg-time-box {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 0.75rem;
                padding: 0.65rem 0.85rem;
            }

            .dark .pg-time-box {
                background: #0f172a;
                border-color: #1e293b;
            }

            .pg-time-box-label {
                font-size: 0.6875rem;
                color: var(--pg-text-sub);
                margin: 0;
            }

            .pg-time-box-val {
                font-size: 1rem;
                font-weight: 800;
                color: var(--pg-text-title);
                margin: 0.2rem 0;
            }

            /* Mini KPI Stats Cards */
            .pg-stats-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            @media (min-width: 768px) {
                .pg-stats-grid {
                    grid-template-columns: repeat(4, 1fr);
                }
            }

            .pg-stat-card {
                background: var(--pg-card-bg);
                border: 1px solid var(--pg-card-border);
                border-radius: 1.15rem;
                padding: 1.15rem 1.25rem;
                display: flex;
                align-items: center;
                gap: 1rem;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .pg-stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            }

            .pg-stat-icon-box {
                width: 3rem;
                height: 3rem;
                border-radius: 0.85rem;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.35rem;
                flex-shrink: 0;
            }

            .pg-stat-info {
                display: flex;
                flex-direction: column;
                gap: 0.15rem;
            }

            .pg-stat-label {
                font-size: 0.6875rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                color: var(--pg-text-sub);
                margin: 0;
            }

            .pg-stat-val {
                font-size: 1.25rem;
                font-weight: 900;
                color: var(--pg-text-title);
                margin: 0;
            }

            /* Modal Backdrop */
            .pg-modal-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.8);
                backdrop-filter: blur(8px);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.25rem;
            }

            .pg-modal-card {
                background: var(--pg-card-bg);
                border: 1px solid var(--pg-card-border);
                border-radius: 1.25rem;
                padding: 1.25rem;
                max-width: 28rem;
                width: 100%;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
            }
        </style>

        <div x-data="presensiMandiriApp({
            sekolahLat: {{ $pengaturan->latitude ? $pengaturan->latitude : 'null' }},
            sekolahLng: {{ $pengaturan->longitude ? $pengaturan->longitude : 'null' }},
            radiusSekolah: {{ $pengaturan->radius_meter ? $pengaturan->radius_meter : 120 }},
            wajibLokasi: {{ $pengaturan->wajib_validasi_lokasi ? 'true' : 'false' }},
            namaSekolah: '{{ addslashes($pengaturan->nama_sekolah ?: "Sekolah") }}',
            logoutUrl: '{{ filament()->getLogoutUrl() }}',
            csrfToken: '{{ csrf_token() }}'
        })" class="pg-container">

            <!-- PWA Install Banner -->
            <div x-show="showPwaBanner" x-transition class="pg-pwa-banner">
                <div class="pg-pwa-content">
                    <div class="pg-pwa-icon-box">
                        <x-filament::icon icon="heroicon-o-device-phone-mobile" style="width: 1.5rem; height: 1.5rem; color: #ffffff;" />
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.875rem;">Pasang Aplikasi Presensi Guru di Smartphone (PWA)</div>
                        <div style="font-size: 0.75rem; opacity: 0.9;">Buka aplikasi lebih cepat tanpa perlu membuka browser tiap hari.</div>
                    </div>
                </div>
                <div class="pg-pwa-actions">
                    <button type="button" x-on:click="installPwa" class="pg-btn-pwa-install">
                        📲 Install Sekarang
                    </button>
                    <button type="button" x-on:click="showPwaBanner = false" class="pg-btn-pwa-close">
                        ✕ Tutup
                    </button>
                </div>
            </div>

            <!-- Blocking Banner Jika Izin Kamera / GPS Ditolak -->
            <div x-show="izinDitolak" x-transition style="background: #fff1f2; border: 2px solid #fda4af; border-radius: 1rem; padding: 1.25rem; color: #9f1239; box-shadow: 0 10px 25px rgba(244, 63, 94, 0.15);">
                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="background: #ffe4e6; border-radius: 0.75rem; padding: 0.5rem; flex-shrink: 0;">
                        <x-filament::icon icon="heroicon-o-shield-exclamation" style="width: 1.75rem; height: 1.75rem; color: #e11d48;" />
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                        <div style="font-size: 0.875rem; font-weight: 800; color: #881337;">
                            ⚠️ Akses Kamera Wajah & Lokasi GPS Diperlukan
                        </div>
                        <div style="font-size: 0.75rem; line-height: 1.5; color: #9f1239;">
                            Untuk menjamin validitas presensi di lingkungan sekolah, sistem mewajibkan verifikasi foto wajah langsung dari kamera dan koordinat GPS akurat.
                            <template x-if="kameraIzin === 'denied'">
                                <div style="margin-top: 0.25rem; font-weight: 700;">❌ Akses Kamera: Belum diizinkan. Foto wajib diambil langsung dari kamera live.</div>
                            </template>
                            <template x-if="gpsIzin === 'denied'">
                                <div style="margin-top: 0.25rem; font-weight: 700;">❌ Akses Lokasi GPS: Izin ditolak di browser Anda (<span x-text="gpsError"></span>).</div>
                            </template>
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem; flex-wrap: wrap;">
                            <button
                                type="button"
                                x-on:click="kalibrasiGpsAkurat(); mulaiKamera();"
                                style="padding: 0.45rem 0.95rem; border-radius: 0.6rem; font-weight: 700; font-size: 0.75rem; color: #fff; background: #e11d48; border: none; cursor: pointer;"
                            >
                                🔄 Coba Minta Izin Ulang
                            </button>
                            <button
                                type="button"
                                x-on:click="logoutAplikasi"
                                style="padding: 0.45rem 0.95rem; border-radius: 0.6rem; font-weight: 600; font-size: 0.75rem; color: #881337; background: #ffffff; border: 1px solid #fda4af; cursor: pointer;"
                            >
                                Keluar Akun
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if (! $guru)
                <!-- Peringatan Jika Akun Belum Tertaut ke Guru -->
                <div style="padding: 1.25rem; border-radius: 1rem; background: #fffbeb; border: 1px solid #fde68a; color: #92400e; display: flex; align-items: flex-start; gap: 1rem;">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" style="width: 2rem; height: 2rem; color: #d97706; flex-shrink: 0;" />
                    <div>
                        <h3 style="font-size: 0.95rem; font-weight: 800; margin: 0 0 0.25rem 0;">Akun Login Belum Terhubung ke Data Guru</h3>
                        <p style="font-size: 0.75rem; margin: 0;">
                            Akun login Anda (<b>{{ Auth::user()->name }}</b>) belum ditautkan ke profil data guru.
                            Silakan hubungi Administrator / Operator Sekolah untuk menautkan akun Anda pada menu <b>Data Guru</b>.
                        </p>
                    </div>
                </div>
            @else

                <!-- 1. Hero Grid: Live Digital Clock & Profil Guru -->
                <div class="pg-hero-grid">
                    <!-- Card Kiri: Futuristic Cyber Clock -->
                    <div class="pg-clock-card">
                        <div class="pg-clock-header">
                            <span class="pg-live-pill">
                                <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background: #34d399; box-shadow: 0 0 6px #34d399;"></span>
                                Live Waktu Presensi
                            </span>

                            <!-- Toggle Suara Asisten -->
                            <button
                                type="button"
                                x-on:click="suaraAktif = !suaraAktif"
                                class="pg-btn-voice-toggle"
                                :title="suaraAktif ? 'Suara Asisten Aktif' : 'Suara Asisten Hening'"
                            >
                                <span x-show="suaraAktif">🔊 Asisten Suara On</span>
                                <span x-show="!suaraAktif" style="opacity: 0.6;">🔇 Mode Hening</span>
                            </button>
                        </div>

                        <div>
                            <div class="pg-clock-time">
                                <span x-text="jam">--:--:--</span>
                                <span style="font-size: 0.875rem; font-weight: 700; color: #6ee7b7; margin-left: 0.25rem;">WITA</span>
                            </div>
                            <p class="pg-clock-date">
                                <x-filament::icon icon="heroicon-o-calendar" style="width: 1rem; height: 1rem; color: #6ee7b7;" />
                                <span x-text="tanggalLengkap">Memuat tanggal...</span>
                            </p>
                        </div>

                        <div class="pg-clock-footer">
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <span style="color: #a7f3d0;">Shift Hari Ini:</span>
                                <span style="font-weight: 700; background: rgba(255, 255, 255, 0.2); padding: 0.15rem 0.5rem; border-radius: 0.4rem;">
                                    {{ $shiftHariIni?->nama ?? 'Shift Pagi Reguler' }}
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <span style="color: #a7f3d0;">Jadwal Kerja:</span>
                                <span style="font-weight: 700;">
                                    {{ $shiftHariIni?->jam_masuk?->format('H:i') ?? '07:15' }} - {{ $shiftHariIni?->jam_pulang?->format('H:i') ?? '14:30' }} WITA
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Kanan: Guru Identity & GPS Radius Badge -->
                    <div class="pg-profile-card">
                        <div class="pg-profile-top">
                            <div class="pg-avatar-wrapper">
                                <img
                                    src="{{ $guru->foto_url }}"
                                    alt="{{ $guru->nama }}"
                                    class="pg-avatar-img"
                                />
                                <span class="pg-avatar-badge-online" title="Status Aktif"></span>
                            </div>

                            <div class="pg-profile-info">
                                <div class="pg-profile-header-row">
                                    <h3 class="pg-profile-name">
                                        {{ $guru->nama }}
                                    </h3>
                                    <span class="pg-badge-emp">
                                        {{ $guru->statusKepegawaianLabel() }}
                                    </span>
                                </div>
                                <p class="pg-profile-desc">
                                    NIP: {{ $guru->nip ?: 'Non-NIP' }} | {{ $guru->jabatan ?: 'Tenaga Pengajar' }}
                                </p>
                                <p class="pg-profile-desc" style="font-weight: 600;">
                                    {{ $guru->jenis_guru ?: 'Guru Mata Pelajaran' }}
                                </p>
                            </div>
                        </div>

                        <!-- Live Geofence Status Pill -->
                        <div class="pg-gps-status-row">
                            <div>
                                <template x-if="loadingGps || kalibrasiBerjalan">
                                    <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: #64748b;">
                                        <x-filament::icon icon="heroicon-o-arrow-path" style="width: 0.95rem; height: 0.95rem; color: #10b981;" />
                                        <span x-text="kalibrasiBerjalan ? '🛰️ Mengkalibrasi GPS Presisi Tinggi...' : 'Mendeteksi Koordinat GPS...'"></span>
                                    </span>
                                </template>

                                <template x-if="!loadingGps && !kalibrasiBerjalan && lat && lng">
                                    <div style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
                                        <template x-if="jarakMeter !== null && jarakMeter <= radiusSekolah">
                                            <span class="pg-gps-badge-in">
                                                <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background: #059669;"></span>
                                                📍 Dalam Radius Sekolah (<span x-text="jarakMeter"></span>m)
                                            </span>
                                        </template>
                                        <template x-if="jarakMeter !== null && jarakMeter > radiusSekolah">
                                            <span class="pg-gps-badge-out">
                                                <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background: #e11d48;"></span>
                                                ⚠️ Di Luar Radius (<span x-text="jarakMeter"></span>m / Maks <span x-text="radiusSekolah"></span>m)
                                            </span>
                                        </template>

                                        <!-- GPS Accuracy Quality Indicator -->
                                        <template x-if="akurasi !== null">
                                            <span>
                                                <template x-if="akurasi <= 25">
                                                    <span class="pg-accuracy-pill pg-accuracy-high" title="Satelit GPS presisi tinggi terkunci">
                                                        🟢 Akurasi Tinggi (±<span x-text="akurasi"></span>m)
                                                    </span>
                                                </template>
                                                <template x-if="akurasi > 25 && akurasi <= 75">
                                                    <span class="pg-accuracy-pill pg-accuracy-medium" title="Sinyal GPS cukup baik">
                                                        🟡 Akurasi Sedang (±<span x-text="akurasi"></span>m)
                                                    </span>
                                                </template>
                                                <template x-if="akurasi > 75">
                                                    <span class="pg-accuracy-pill pg-accuracy-low" title="Sinyal GPS lemah atau estimasi jaringan. Tekan Kalibrasi GPS untuk perbaikan.">
                                                        🟠 Akurasi Rendah (±<span x-text="akurasi"></span>m)
                                                    </span>
                                                </template>
                                            </span>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="gpsError && !loadingGps && !kalibrasiBerjalan">
                                    <span style="font-size: 0.75rem; color: #e11d48; font-weight: 600;" x-text="gpsError"></span>
                                </template>
                            </div>

                            <button
                                type="button"
                                x-on:click="kalibrasiGpsAkurat"
                                :disabled="kalibrasiBerjalan"
                                class="pg-btn-refresh-gps"
                                title="Kalibrasi & perbarui satelit GPS presisi tinggi sekarang"
                            >
                                <x-filament::icon icon="heroicon-o-arrow-path" style="width: 0.85rem; height: 0.85rem;" />
                                <span x-text="kalibrasiBerjalan ? 'Kalibrasi...' : '🎯 Kalibrasi GPS'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. Core Interactive Presensi: Biometric Camera HUD & Smart Geofence Map -->
                <div class="pg-main-grid">
                    <!-- Kolom Kiri: AI Biometric Camera Scanner -->
                    <div class="pg-panel-card">
                        <div>
                            <div class="pg-panel-header">
                                <h4 class="pg-panel-title">
                                    <x-filament::icon icon="heroicon-o-camera" style="width: 1.1rem; height: 1.1rem; color: #10b981;" />
                                    <span>Verifikasi Wajah Biometrik</span>
                                </h4>

                                <button
                                    type="button"
                                    x-on:click="gantiKamera"
                                    class="pg-btn-small"
                                    title="Ganti kamera depan / belakang"
                                >
                                    <x-filament::icon icon="heroicon-o-arrow-path-rounded-square" style="width: 0.85rem; height: 0.85rem;" />
                                    <span>Ganti Kamera</span>
                                </button>
                            </div>

                            <!-- Kamera / Foto Viewport -->
                            <div class="pg-camera-wrapper">
                                <!-- Shutter Flash Effect -->
                                <div class="pg-shutter-flash" :class="{ 'flash-active': shutterFlash }"></div>

                                <!-- Live Camera Video -->
                                <video
                                    x-ref="videoElem"
                                    autoplay
                                    playsinline
                                    muted
                                    class="pg-camera-video"
                                    :class="{ 'env-mode': facingMode === 'environment' }"
                                    x-show="kameraAktif && !fotoBase64"
                                ></video>

                                <!-- Biometric Scanner HUD -->
                                <div x-show="kameraAktif && !fotoBase64" class="pg-hud-corners"></div>
                                <div x-show="kameraAktif && !fotoBase64" class="pg-laser-line"></div>

                                <!-- Foto Snapshot Result -->
                                <template x-if="fotoBase64">
                                    <img :src="fotoBase64" alt="Hasil Foto Presensi" style="width: 100%; height: 100%; object-fit: cover;" />
                                </template>

                                <!-- Fallback Bila Kamera Tidak Aktif -->
                                <div x-show="!kameraAktif && !fotoBase64" style="text-align: center; padding: 1.5rem; color: #94a3b8; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                    <x-filament::icon icon="heroicon-o-video-camera-slash" style="width: 3rem; height: 3rem; color: #64748b;" />
                                    <p style="font-size: 0.75rem; font-weight: 500; margin: 0;" x-text="kameraError || 'Kamera belum aktif atau izin browser belum disetujui.'"></p>
                                    <button
                                        type="button"
                                        x-on:click="mulaiKamera"
                                        style="font-size: 0.75rem; color: #34d399; font-weight: 700; background: transparent; border: none; text-decoration: underline; cursor: pointer;"
                                    >
                                        Aktifkan Kamera Sekarang
                                    </button>
                                </div>

                                <!-- Status Badge Over Video -->
                                <span x-show="kameraAktif && !fotoBase64" style="position: absolute; top: 0.75rem; left: 0.75rem; padding: 0.25rem 0.6rem; border-radius: 0.5rem; font-size: 0.6875rem; font-weight: 800; background: rgba(0, 0, 0, 0.65); color: #fff; backdrop-filter: blur(8px); display: flex; align-items: center; gap: 0.35rem; z-index: 20; border: 1px solid rgba(255,255,255,0.15);">
                                    <span style="width: 0.45rem; height: 0.45rem; border-radius: 9999px; background: #34d399;"></span>
                                    AI Face Detection Ready
                                </span>
                            </div>
                        </div>

                        <!-- Live Camera Snapshot & Retake Controls (STRICTLY NO FILE UPLOAD) -->
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 0.65rem; width: 100%;">
                            <template x-if="!fotoBase64">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.45rem; width: 100%;">
                                    <button
                                        type="button"
                                        x-on:click="ambilFoto"
                                        :disabled="!kameraAktif"
                                        class="pg-btn-snap-photo"
                                    >
                                        <x-filament::icon icon="heroicon-o-camera" style="width: 1.15rem; height: 1.15rem;" />
                                        <span>Ambil Foto Selfie (Kamera Langsung)</span>
                                    </button>
                                    <span style="font-size: 0.6875rem; color: var(--pg-text-sub); display: flex; align-items: center; gap: 0.3rem;">
                                        <span>🔒</span>
                                        <span>Hanya boleh dari kamera perangkat (lampiran file/dokumen dinonaktifkan).</span>
                                    </span>
                                </div>
                            </template>

                            <template x-if="fotoBase64">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 700; color: #059669; background: #ecfdf5; padding: 0.35rem 0.75rem; border-radius: 9999px; border: 1px solid #a7f3d0;">
                                        <x-filament::icon icon="heroicon-s-check-circle" style="width: 1rem; height: 1rem; color: #10b981;" />
                                        Foto Wajah Terverifikasi (Kamera Live)
                                    </span>
                                    <button
                                        type="button"
                                        x-on:click="ulangFoto"
                                        class="pg-btn-retake-photo"
                                    >
                                        <x-filament::icon icon="heroicon-o-arrow-path" style="width: 0.9rem; height: 0.9rem;" />
                                        <span>Ambil Ulang Foto</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Live Geofence Map & Action Button -->
                    <div class="pg-panel-card">
                        <div>
                            <div class="pg-panel-header">
                                <h4 class="pg-panel-title">
                                    <x-filament::icon icon="heroicon-o-map-pin" style="width: 1.1rem; height: 1.1rem; color: #10b981;" />
                                    <span>Peta Radius Geofence Sekolah</span>
                                </h4>

                                <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                    <button
                                        type="button"
                                        x-on:click="pusatkanPeta('user')"
                                        class="pg-btn-small"
                                        title="Pusatkan peta ke lokasi saya"
                                    >
                                        📍 Posisi Saya
                                    </button>
                                    <button
                                        type="button"
                                        x-on:click="pusatkanPeta('sekolah')"
                                        class="pg-btn-small"
                                        title="Pusatkan peta ke sekolah"
                                    >
                                        🏫 Sekolah
                                    </button>
                                    <button
                                        type="button"
                                        x-on:click="pusatkanPeta('fit')"
                                        class="pg-btn-small"
                                        title="Tampilkan posisi saya dan sekolah sekaligus"
                                    >
                                        🔍 Fit View
                                    </button>
                                </div>
                            </div>

                            <!-- Leaflet Map Container -->
                            <div id="pg-leaflet-map" wire:ignore></div>

                            <!-- Telemetri GPS & Geofence Inspector -->
                            <div style="margin-top: 0.85rem; padding: 0.85rem; background: rgba(0, 0, 0, 0.02); border: 1px solid var(--pg-card-border); border-radius: 0.85rem; display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.75rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                                    <span style="font-weight: 800; color: var(--pg-text-title); display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <span>🛰️</span>
                                        <span>Telemetri Koordinat & Geofence GPS</span>
                                    </span>
                                    <button
                                        type="button"
                                        x-on:click="salinKoordinat"
                                        class="pg-btn-small"
                                        style="font-size: 0.6875rem; padding: 0.2rem 0.6rem; font-weight: 700;"
                                        title="Salin koordinat GPS saya saat ini untuk sinkronisasi admin"
                                    >
                                        <x-filament::icon icon="heroicon-o-clipboard-document-check" style="width: 0.85rem; height: 0.85rem; color: #10b981;" />
                                        <span x-text="copiedKoordinat ? '✓ Koordinat Disalin!' : 'Salin Koordinat Saya'"></span>
                                    </button>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.6rem;">
                                    <!-- Posisi User -->
                                    <div style="background: var(--pg-card-bg); padding: 0.6rem 0.75rem; border-radius: 0.6rem; border: 1px solid var(--pg-card-border);">
                                        <div style="color: var(--pg-text-sub); font-size: 0.6875rem; font-weight: 600;">Koordinat GPS Saya:</div>
                                        <div style="font-weight: 800; color: var(--pg-text-title); font-family: monospace; font-size: 0.75rem; margin-top: 0.15rem;">
                                            <span x-text="lat ? lat.toFixed(6) + ', ' + lng.toFixed(6) : 'Mencari sinyal GPS...'"></span>
                                        </div>
                                        <div style="margin-top: 0.25rem;">
                                            <template x-if="akurasi !== null">
                                                <span :class="{
                                                    'pg-accuracy-high': akurasi <= 25,
                                                    'pg-accuracy-medium': akurasi > 25 && akurasi <= 75,
                                                    'pg-accuracy-low': akurasi > 75
                                                }" class="pg-accuracy-pill" style="font-size: 0.625rem; padding: 0.1rem 0.45rem;">
                                                    Akurasi: ±<span x-text="akurasi"></span>m
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Titik Sekolah -->
                                    <div style="background: var(--pg-card-bg); padding: 0.6rem 0.75rem; border-radius: 0.6rem; border: 1px solid var(--pg-card-border);">
                                        <div style="color: var(--pg-text-sub); font-size: 0.6875rem; font-weight: 600;">Titik Acuan Sekolah:</div>
                                        <div style="font-weight: 800; color: var(--pg-text-title); font-family: monospace; font-size: 0.75rem; margin-top: 0.15rem;">
                                            {{ $pengaturan->latitude ?? '-' }}, {{ $pengaturan->longitude ?? '-' }}
                                        </div>
                                        <div style="font-size: 0.6875rem; color: #059669; font-weight: 700; margin-top: 0.25rem;">
                                            Radius Izin: {{ $pengaturan->radius_meter ?? 120 }} meter
                                        </div>
                                    </div>

                                    <!-- Jarak Terhitung -->
                                    <div style="background: var(--pg-card-bg); padding: 0.6rem 0.75rem; border-radius: 0.6rem; border: 1px solid var(--pg-card-border);">
                                        <div style="color: var(--pg-text-sub); font-size: 0.6875rem; font-weight: 600;">Jarak ke Sekolah:</div>
                                        <div style="font-weight: 800; font-size: 0.8125rem; margin-top: 0.15rem;" :style="diLuarRadius ? 'color: #e11d48;' : 'color: #059669;'">
                                            <span x-text="jarakMeter !== null ? jarakMeter + ' meter' : 'Menghitung...'"></span>
                                        </div>
                                        <div style="font-size: 0.6875rem; font-weight: 700; margin-top: 0.25rem;" :style="diLuarRadius ? 'color: #e11d48;' : 'color: #059669;'">
                                            <span x-text="diLuarRadius ? '⛔ Di Luar Radius Sekolah' : '✅ Dalam Radius Sah'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Presensi Card -->
                        <div style="padding-top: 0.75rem; border-top: 1px solid var(--pg-card-border);">
                            @if ($presensiHariIni?->jam_masuk && $presensiHariIni?->jam_pulang)
                                <!-- 1. Status Sudah Lengkap (Masuk & Pulang Selesai) -->
                                <div class="pg-completed-box">
                                    <div class="pg-completed-title">
                                        <x-filament::icon icon="heroicon-s-check-badge" style="width: 1.35rem; height: 1.35rem; color: #059669;" />
                                        <span>Presensi Hari Ini Telah Lengkap & Sah</span>
                                    </div>
                                    <div class="pg-completed-grid">
                                        <div class="pg-time-box">
                                            <p class="pg-time-box-label">Jam Masuk:</p>
                                            <p class="pg-time-box-val">
                                                {{ $presensiHariIni->jam_masuk->format('H:i') }} WITA
                                            </p>
                                            <span style="font-size: 0.6875rem; font-weight: 700; color: #059669;">
                                                {{ ucfirst(str_replace('_', ' ', $presensiHariIni->status_masuk)) }}
                                            </span>
                                        </div>
                                        <div class="pg-time-box">
                                            <p class="pg-time-box-label">Jam Pulang:</p>
                                            <p class="pg-time-box-val">
                                                {{ $presensiHariIni->jam_pulang->format('H:i') }} WITA
                                            </p>
                                            <span style="font-size: 0.6875rem; font-weight: 700; color: #059669;">
                                                {{ ucfirst(str_replace('_', ' ', $presensiHariIni->status_pulang)) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            @elseif ($presensiHariIni?->jam_masuk)
                                <!-- 2. Sudah Check-In, Menunggu Check-Out Pulang -->
                                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.75rem; padding: 0.65rem 0.85rem; font-size: 0.75rem; color: #1e40af; display: flex; align-items: center; gap: 0.45rem;">
                                        <x-filament::icon icon="heroicon-s-clock" style="width: 1rem; height: 1rem; color: #2563eb; flex-shrink: 0;" />
                                        <span>Sudah Absen Masuk: <b>{{ $presensiHariIni->jam_masuk->format('H:i') }} WITA</b> ({{ ucfirst(str_replace('_', ' ', $presensiHariIni->status_masuk)) }})</span>
                                    </div>

                                    <!-- Alert Jika Di Luar Radius -->
                                    <div x-show="diLuarRadius" x-transition style="background: #fff1f2; border: 1.5px solid #fda4af; border-radius: 0.85rem; padding: 0.85rem 1rem; color: #9f1239; font-size: 0.8125rem;">
                                        <div style="display: flex; align-items: flex-start; gap: 0.65rem;">
                                            <span style="font-size: 1.35rem; line-height: 1;">⛔</span>
                                            <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                                                <div style="font-weight: 800; color: #881337;">Presensi Terkunci: Di Luar Radius Sekolah</div>
                                                <div style="font-size: 0.75rem; line-height: 1.4; color: #9f1239;">
                                                    Jarak Anda saat ini: <b x-text="jarakMeter + ' meter'"></b> dari sekolah (Batas maksimal admin: <b x-text="radiusSekolah + ' meter'"></b>). Presensi tidak dapat direkam.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Alert Jika GPS Belum Terdeteksi -->
                                    <div x-show="wajibLokasi && (!lat || !lng)" x-transition style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 0.85rem; padding: 0.75rem 1rem; color: #92400e; font-size: 0.8125rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span style="font-size: 1.15rem;">🛰️</span>
                                            <span style="font-size: 0.75rem; font-weight: 600;">Mendeteksi koordinat GPS lokasi Anda untuk verifikasi radius sekolah...</span>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        x-on:click="kirimCheckOut"
                                        x-bind:disabled="submitting || !bolehAbsen"
                                        class="pg-btn-checkout"
                                        :style="!bolehAbsen ? 'opacity: 0.55; cursor: not-allowed; background: #64748b; box-shadow: none;' : ''"
                                    >
                                        <x-filament::icon icon="heroicon-o-arrow-left-on-rectangle" style="width: 1.35rem; height: 1.35rem;" />
                                        <template x-if="submitting">
                                            <span>Memproses Presensi Pulang...</span>
                                        </template>
                                        <template x-if="!submitting && diLuarRadius">
                                            <span>⛔ DI LUAR RADIUS (<span x-text="jarakMeter"></span>m / Maks <span x-text="radiusSekolah"></span>m)</span>
                                        </template>
                                        <template x-if="!submitting && !diLuarRadius">
                                            <span>KLIK UNTUK ABSEN PULANG SEKARANG</span>
                                        </template>
                                    </button>
                                </div>

                            @else
                                <!-- 3. Belum Check-In Masuk -->
                                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                    <!-- Alert Jika Di Luar Radius -->
                                    <div x-show="diLuarRadius" x-transition style="background: #fff1f2; border: 1.5px solid #fda4af; border-radius: 0.85rem; padding: 0.85rem 1rem; color: #9f1239; font-size: 0.8125rem;">
                                        <div style="display: flex; align-items: flex-start; gap: 0.65rem;">
                                            <span style="font-size: 1.35rem; line-height: 1;">⛔</span>
                                            <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                                                <div style="font-weight: 800; color: #881337;">Presensi Terkunci: Di Luar Radius Sekolah</div>
                                                <div style="font-size: 0.75rem; line-height: 1.4; color: #9f1239;">
                                                    Jarak Anda saat ini: <b x-text="jarakMeter + ' meter'"></b> dari sekolah (Batas maksimal admin: <b x-text="radiusSekolah + ' meter'"></b>). Presensi tidak dapat direkam.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Alert Jika GPS Belum Terdeteksi -->
                                    <div x-show="wajibLokasi && (!lat || !lng)" x-transition style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 0.85rem; padding: 0.75rem 1rem; color: #92400e; font-size: 0.8125rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span style="font-size: 1.15rem;">🛰️</span>
                                            <span style="font-size: 0.75rem; font-weight: 600;">Mendeteksi koordinat GPS lokasi Anda untuk verifikasi radius sekolah...</span>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        x-on:click="kirimCheckIn"
                                        x-bind:disabled="submitting || !bolehAbsen"
                                        class="pg-btn-checkin"
                                        :style="!bolehAbsen ? 'opacity: 0.55; cursor: not-allowed; background: #64748b; box-shadow: none;' : ''"
                                    >
                                        <x-filament::icon icon="heroicon-o-camera" style="width: 1.5rem; height: 1.5rem;" />
                                        <template x-if="submitting">
                                            <span>Memverifikasi & Menyimpan...</span>
                                        </template>
                                        <template x-if="!submitting && diLuarRadius">
                                            <span>⛔ DI LUAR RADIUS (<span x-text="jarakMeter"></span>m / Maks <span x-text="radiusSekolah"></span>m)</span>
                                        </template>
                                        <template x-if="!submitting && !diLuarRadius">
                                            <span>KLIK UNTUK ABSEN MASUK SEKARANG</span>
                                        </template>
                                    </button>
                                </div>
                            @endif

                            <div style="font-size: 0.6875rem; color: var(--pg-text-sub); text-align: center; margin-top: 0.65rem; display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                                <span>🛡️</span>
                                <span>Validasi biometrik & koordinat geofence terlindungi secara otomatis oleh sistem.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Statistik Kehadiran Guru Bulan Ini -->
                <div class="pg-stats-grid">
                    <div class="pg-stat-card">
                        <div class="pg-stat-icon-box" style="background: #d1fae5; color: #065f46;">
                            🎯
                        </div>
                        <div class="pg-stat-info">
                            <p class="pg-stat-label">Persentase Hadir</p>
                            <h4 class="pg-stat-val">{{ $statistikKehadiran['persentase'] }}%</h4>
                        </div>
                    </div>

                    <div class="pg-stat-card">
                        <div class="pg-stat-icon-box" style="background: #ccfbf1; color: #0f766e;">
                            ⚡
                        </div>
                        <div class="pg-stat-info">
                            <p class="pg-stat-label">Tepat Waktu</p>
                            <h4 class="pg-stat-val">{{ $statistikKehadiran['tepatWaktu'] }} Hari</h4>
                        </div>
                    </div>

                    <div class="pg-stat-card">
                        <div class="pg-stat-icon-box" style="background: #fef3c7; color: #92400e;">
                            ⚠️
                        </div>
                        <div class="pg-stat-info">
                            <p class="pg-stat-label">Terlambat</p>
                            <h4 class="pg-stat-val">{{ $statistikKehadiran['terlambat'] }} Hari</h4>
                        </div>
                    </div>

                    <div class="pg-stat-card">
                        <div class="pg-stat-icon-box" style="background: #dbeafe; color: #1e40af;">
                            📝
                        </div>
                        <div class="pg-stat-info">
                            <p class="pg-stat-label">Izin / Sakit / Cuti</p>
                            <h4 class="pg-stat-val">{{ $statistikKehadiran['izinSakit'] }} Hari</h4>
                        </div>
                    </div>
                </div>

                <!-- 4. Riwayat Presensi 7 Hari Terakhir -->
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <x-filament::icon icon="heroicon-o-clock" style="width: 1.15rem; height: 1.15rem; color: #059669;" />
                                <span style="font-weight: 800; font-size: 0.875rem;">Riwayat Presensi 7 Hari Terakhir</span>
                            </div>
                            <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 400;">Sinkronisasi Real-Time</span>
                        </div>
                    </x-slot>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; font-size: 0.75rem; text-align: left; border-collapse: collapse;">
                            <thead>
                                <tr style="background: rgba(0, 0, 0, 0.03); color: var(--pg-text-sub); font-weight: 800; text-transform: uppercase; border-bottom: 1px solid var(--pg-card-border);">
                                    <th style="padding: 0.75rem;">Hari & Tanggal</th>
                                    <th style="padding: 0.75rem;">Shift</th>
                                    <th style="padding: 0.75rem;">Waktu Masuk</th>
                                    <th style="padding: 0.75rem;">Waktu Pulang</th>
                                    <th style="padding: 0.75rem;">Status Kehadiran</th>
                                    <th style="padding: 0.75rem;">Foto Selfie</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riwayatSeminggu as $item)
                                    <tr style="border-bottom: 1px solid var(--pg-card-border);">
                                        <td style="padding: 0.75rem; font-weight: 700; color: var(--pg-text-title);">
                                            {{ $item->tanggal->translatedFormat('l, d M Y') }}
                                        </td>
                                        <td style="padding: 0.75rem; color: var(--pg-text-sub);">
                                            {{ $item->shift?->nama ?? 'Shift Pagi Reguler' }}
                                        </td>
                                        <td style="padding: 0.75rem;">
                                            @if ($item->jam_masuk)
                                                <div style="display: flex; align-items: center; gap: 0.35rem;">
                                                    <span style="font-weight: 800; color: var(--pg-text-title);">{{ $item->jam_masuk->format('H:i') }} WITA</span>
                                                    <span style="padding: 0.1rem 0.4rem; border-radius: 0.3rem; font-size: 0.625rem; font-weight: 800; {{ $item->status_masuk === 'terlambat' ? 'background:#fef3c7; color:#92400e;' : 'background:#d1fae5; color:#065f46;' }}">
                                                        {{ $item->status_masuk === 'terlambat' ? 'Terlambat' : 'Tepat Waktu' }}
                                                    </span>
                                                </div>
                                            @else
                                                <span style="color: #94a3b8;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem;">
                                            @if ($item->jam_pulang)
                                                <div style="display: flex; align-items: center; gap: 0.35rem;">
                                                    <span style="font-weight: 800; color: var(--pg-text-title);">{{ $item->jam_pulang->format('H:i') }} WITA</span>
                                                    <span style="padding: 0.1rem 0.4rem; border-radius: 0.3rem; font-size: 0.625rem; font-weight: 800; {{ $item->status_pulang === 'pulang_cepat' ? 'background:#fef3c7; color:#92400e;' : 'background:#d1fae5; color:#065f46;' }}">
                                                        {{ $item->status_pulang === 'pulang_cepat' ? 'Pulang Cepat' : 'Normal' }}
                                                    </span>
                                                </div>
                                            @else
                                                <span style="color: #94a3b8;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem;">
                                            <span style="display: inline-flex; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; {{ $item->status_kehadiran === 'hadir' ? 'background:#d1fae5; color:#065f46;' : ($item->status_kehadiran === 'alpa' ? 'background:#ffe4e6; color:#9f1239;' : 'background:#dbeafe; color:#1e40af;') }}">
                                                {{ ucfirst($item->status_kehadiran) }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.75rem;">
                                            <div style="display: flex; align-items: center; gap: 0.35rem;">
                                                @if ($item->foto_masuk_url)
                                                    <button
                                                        type="button"
                                                        x-on:click="zoomFotoUrl = '{{ $item->foto_masuk_url }}'"
                                                        style="border-radius: 0.4rem; overflow: hidden; border: 1px solid #10b981; cursor: pointer; padding: 0; background: none;"
                                                        title="Lihat Foto Masuk"
                                                    >
                                                        <img src="{{ $item->foto_masuk_url }}" style="width: 1.75rem; height: 1.75rem; object-fit: cover; display: block;" />
                                                    </button>
                                                @endif
                                                @if ($item->foto_pulang_url)
                                                    <button
                                                        type="button"
                                                        x-on:click="zoomFotoUrl = '{{ $item->foto_pulang_url }}'"
                                                        style="border-radius: 0.4rem; overflow: hidden; border: 1px solid #f59e0b; cursor: pointer; padding: 0; background: none;"
                                                        title="Lihat Foto Pulang"
                                                    >
                                                        <img src="{{ $item->foto_pulang_url }}" style="width: 1.75rem; height: 1.75rem; object-fit: cover; display: block;" />
                                                    </button>
                                                @endif
                                                @if (! $item->foto_masuk_url && ! $item->foto_pulang_url)
                                                    <span style="color: #94a3b8;">-</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="padding: 1.5rem; text-align: center; color: #94a3b8; font-size: 0.75rem;">
                                            Belum ada data riwayat presensi dalam 7 hari terakhir.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>

                <!-- 5. Modal Zoom Foto Selfie -->
                <template x-if="zoomFotoUrl">
                    <div class="pg-modal-backdrop" x-on:click="zoomFotoUrl = null">
                        <div class="pg-modal-card" x-on:click.stop>
                            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.75rem; margin-bottom: 0.75rem; border-bottom: 1px solid var(--pg-card-border);">
                                <h5 style="font-size: 0.875rem; font-weight: 800; color: var(--pg-text-title); margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                                    <span>📸</span>
                                    <span>Pratinjau Foto Selfie Presensi</span>
                                </h5>
                                <button type="button" x-on:click="zoomFotoUrl = null" style="width: 1.75rem; height: 1.75rem; border-radius: 0.4rem; background: #f1f5f9; border: 1px solid #cbd5e1; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.8125rem; font-weight: 700;">✕</button>
                            </div>
                            <img :src="zoomFotoUrl" alt="Foto Presensi Zoom" style="width: 100%; height: auto; border-radius: 0.75rem; object-fit: cover;" />
                        </div>
                    </div>
                </template>
            @endif
        </div>

        <script>
            (function() {
                function initPresensiMandiriApp() {
                    if (typeof Alpine !== 'undefined' && !window._presensiMandiriRegistered) {
                        window._presensiMandiriRegistered = true;
                        Alpine.data('presensiMandiriApp', (config) => ({
                            jam: '--:--:--',
                            tanggalLengkap: '',
                            lat: null,
                            lng: null,
                            akurasi: null,
                            jarakMeter: null,
                            loadingGps: false,
                            kalibrasiBerjalan: false,
                            gpsError: null,
                            gpsIzin: 'loading',
                            gpsSamples: [],
                            copiedKoordinat: false,
                            kameraAktif: false,
                            kameraIzin: 'loading',
                            kameraError: null,
                            shutterFlash: false,
                            facingMode: 'user',
                            fotoBase64: null,
                            streamKamera: null,
                            submitting: false,
                            suaraAktif: true,
                            pwaDeferredPrompt: null,
                            showPwaBanner: false,
                            zoomFotoUrl: null,
                            watchGpsId: null,

                            sekolahLat: config.sekolahLat,
                            sekolahLng: config.sekolahLng,
                            radiusSekolah: config.radiusSekolah || 120,
                            wajibLokasi: config.wajibLokasi || false,
                            namaSekolah: config.namaSekolah || 'Sekolah',
                            logoutUrl: config.logoutUrl,
                            csrfToken: config.csrfToken,

                            map: null,
                            markerUser: null,
                            markerSekolah: null,
                            circleRadius: null,
                            circleAkurasi: null,
                            lineJarak: null,

                            get izinDitolak() {
                                return (this.gpsIzin === 'denied' && this.wajibLokasi) || this.kameraIzin === 'denied';
                            },

                            get diLuarRadius() {
                                return this.jarakMeter !== null && this.jarakMeter > this.radiusSekolah;
                            },

                            get bolehAbsen() {
                                if (this.wajibLokasi) {
                                    if (!this.lat || !this.lng) return false;
                                    if (this.diLuarRadius) return false;
                                } else if (this.sekolahLat && this.sekolahLng && this.diLuarRadius) {
                                    return false;
                                }
                                return true;
                            },

                            init() {
                                this.updateWaktu();
                                setInterval(() => this.updateWaktu(), 1000);

                                this.mulaiPelacakanGps();
                                this.mulaiKamera();

                                this.$nextTick(() => {
                                    this.setupMap();
                                });

                                // Support Livewire SPA Navigation (no refresh needed)
                                document.addEventListener('livewire:navigated', () => {
                                    if (document.getElementById('pg-leaflet-map')) {
                                        this.setupMap();
                                    }
                                });

                                window.addEventListener('beforeinstallprompt', (e) => {
                                    e.preventDefault();
                                    this.pwaDeferredPrompt = e;
                                    this.showPwaBanner = true;
                                });
                            },

                            installPwa() {
                                if (this.pwaDeferredPrompt) {
                                    this.pwaDeferredPrompt.prompt();
                                    this.pwaDeferredPrompt.userChoice.then((choiceResult) => {
                                        if (choiceResult.outcome === 'accepted') {
                                            this.showPwaBanner = false;
                                        }
                                        this.pwaDeferredPrompt = null;
                                    });
                                }
                            },

                            logoutAplikasi() {
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = this.logoutUrl;
                                const csrf = document.createElement('input');
                                csrf.type = 'hidden';
                                csrf.name = '_token';
                                csrf.value = this.csrfToken;
                                form.appendChild(csrf);
                                document.body.appendChild(form);
                                form.submit();
                            },

                            bicara(teks) {
                                if (!this.suaraAktif || !('speechSynthesis' in window)) return;
                                try {
                                    window.speechSynthesis.cancel();
                                    const utterance = new SpeechSynthesisUtterance(teks);
                                    utterance.lang = 'id-ID';
                                    utterance.rate = 1.05;
                                    window.speechSynthesis.speak(utterance);
                                } catch (e) {
                                    console.warn('Speech error:', e);
                                }
                            },

                            updateWaktu() {
                                const now = new Date();
                                this.jam = new Intl.DateTimeFormat('en-GB', {
                                    timeZone: 'Asia/Makassar',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit',
                                    hour12: false
                                }).format(now);
                                this.tanggalLengkap = new Intl.DateTimeFormat('id-ID', {
                                    timeZone: 'Asia/Makassar',
                                    weekday: 'long',
                                    day: 'numeric',
                                    month: 'long',
                                    year: 'numeric'
                                }).format(now);
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

                            setupMap() {
                                this.ensureLeaflet(() => {
                                    const mapElem = document.getElementById('pg-leaflet-map');
                                    if (!mapElem) {
                                        setTimeout(() => this.setupMap(), 150);
                                        return;
                                    }

                                    // Bersihkan instance lama jika ada untuk mencegah error 'Map container is already initialized'
                                    if (this.map) {
                                        try {
                                            this.map.remove();
                                        } catch (e) {}
                                        this.map = null;
                                    }
                                    if (mapElem._leaflet_id) {
                                        mapElem._leaflet_id = null;
                                    }

                                    this.markerSekolah = null;
                                    this.circleRadius = null;
                                    this.markerUser = null;
                                    this.circleAkurasi = null;

                                    const centerLat = this.sekolahLat || this.lat || -5.147665;
                                    const centerLng = this.sekolahLng || this.lng || 119.432731;

                                    this.map = L.map('pg-leaflet-map', {
                                        center: [centerLat, centerLng],
                                        zoom: 17,
                                        zoomControl: true,
                                        attributionControl: false
                                    });

                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        maxZoom: 19
                                    }).addTo(this.map);

                                    const iconSekolah = L.divIcon({
                                        className: 'custom-school-pin',
                                        html: '<div style="background:#0f766e; color:#fff; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid #fff; box-shadow:0 4px 10px rgba(0,0,0,0.3); font-size:18px;">🏫</div>',
                                        iconSize: [36, 36],
                                        iconAnchor: [18, 18]
                                    });

                                    if (this.sekolahLat && this.sekolahLng) {
                                        this.markerSekolah = L.marker([this.sekolahLat, this.sekolahLng], { icon: iconSekolah })
                                            .addTo(this.map)
                                            .bindPopup('<b>' + this.namaSekolah + '</b><br>Titik Koordinat: ' + this.sekolahLat.toFixed(6) + ', ' + this.sekolahLng.toFixed(6) + '<br>Radius Izin: ' + this.radiusSekolah + ' meter');

                                        this.circleRadius = L.circle([this.sekolahLat, this.sekolahLng], {
                                            radius: this.radiusSekolah,
                                            color: '#10b981',
                                            fillColor: '#10b981',
                                            fillOpacity: 0.15,
                                            weight: 2
                                        }).addTo(this.map);
                                    }

                                    this.updateMapUserMarker();

                                    setTimeout(() => {
                                        if (this.map) {
                                            this.map.invalidateSize();
                                            if (this.lat && this.lng && this.sekolahLat && this.sekolahLng) {
                                                this.pusatkanPeta('fit');
                                            }
                                        }
                                    }, 200);

                                    setTimeout(() => {
                                        if (this.map) {
                                            this.map.invalidateSize();
                                        }
                                    }, 600);
                                });
                            },

                            updateMapUserMarker() {
                                if (!this.map || typeof L === 'undefined' || !this.lat || !this.lng) return;

                                const userPos = [this.lat, this.lng];
                                const isInside = this.jarakMeter !== null ? this.jarakMeter <= this.radiusSekolah : true;
                                const markerColor = isInside ? '#059669' : '#e11d48';

                                const iconUser = L.divIcon({
                                    className: 'custom-user-pin',
                                    html: '<div style="background:' + markerColor + '; color:#fff; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid #fff; box-shadow:0 4px 12px rgba(0,0,0,0.35); font-size:16px;">📍</div>',
                                    iconSize: [34, 34],
                                    iconAnchor: [17, 17]
                                });

                                if (!this.markerUser) {
                                    this.markerUser = L.marker(userPos, { icon: iconUser })
                                        .addTo(this.map)
                                        .bindPopup('<b>Posisi Anda Saat Ini</b><br>Koordinat: ' + this.lat.toFixed(6) + ', ' + this.lng.toFixed(6) + '<br>Jarak ke Sekolah: ' + (this.jarakMeter ?? 0) + 'm<br>Akurasi GPS: ±' + (this.akurasi ?? 0) + 'm');
                                } else {
                                    this.markerUser.setLatLng(userPos);
                                    this.markerUser.setIcon(iconUser);
                                    this.markerUser.setPopupContent('<b>Posisi Anda Saat Ini</b><br>Koordinat: ' + this.lat.toFixed(6) + ', ' + this.lng.toFixed(6) + '<br>Jarak ke Sekolah: ' + (this.jarakMeter ?? 0) + 'm<br>Akurasi GPS: ±' + (this.akurasi ?? 0) + 'm');
                                }

                                // Visual Accuracy Circle on Map
                                if (this.akurasi) {
                                    if (!this.circleAkurasi) {
                                        this.circleAkurasi = L.circle(userPos, {
                                            radius: this.akurasi,
                                            color: '#3b82f6',
                                            fillColor: '#60a5fa',
                                            fillOpacity: 0.12,
                                            weight: 1.5,
                                            dashArray: '4, 4'
                                        }).addTo(this.map);
                                    } else {
                                        this.circleAkurasi.setLatLng(userPos);
                                        this.circleAkurasi.setRadius(this.akurasi);
                                    }
                                }

                                if (this.sekolahLat && this.sekolahLng) {
                                    const lineCoords = [userPos, [this.sekolahLat, this.sekolahLng]];
                                    if (!this.lineJarak) {
                                        this.lineJarak = L.polyline(lineCoords, {
                                            color: isInside ? '#059669' : '#e11d48',
                                            dashArray: '6, 8',
                                            weight: 2.5
                                        }).addTo(this.map);
                                    } else {
                                        this.lineJarak.setLatLngs(lineCoords);
                                        this.lineJarak.setStyle({ color: isInside ? '#059669' : '#e11d48' });
                                    }

                                    if (this.circleRadius) {
                                        this.circleRadius.setStyle({
                                            color: isInside ? '#10b981' : '#f43f5e',
                                            fillColor: isInside ? '#10b981' : '#f43f5e'
                                        });
                                    }
                                }
                            },

                            pusatkanPeta(ke) {
                                if (!this.map) return;
                                if (ke === 'user' && this.lat && this.lng) {
                                    this.map.flyTo([this.lat, this.lng], 18, { duration: 1.2 });
                                } else if (ke === 'sekolah' && this.sekolahLat && this.sekolahLng) {
                                    this.map.flyTo([this.sekolahLat, this.sekolahLng], 17, { duration: 1.2 });
                                } else if (ke === 'fit' && this.lat && this.lng && this.sekolahLat && this.sekolahLng) {
                                    const bounds = L.latLngBounds([[this.lat, this.lng], [this.sekolahLat, this.sekolahLng]]);
                                    this.map.fitBounds(bounds, { padding: [50, 50], maxZoom: 18 });
                                }
                            },

                            salinKoordinat() {
                                if (!this.lat || !this.lng) {
                                    alert('Koordinat GPS belum terdeteksi. Silakan tunggu atau tekan tombol Kalibrasi GPS.');
                                    return;
                                }
                                const teks = this.lat.toFixed(6) + ', ' + this.lng.toFixed(6);
                                if (navigator.clipboard && navigator.clipboard.writeText) {
                                    navigator.clipboard.writeText(teks).then(() => {
                                        this.copiedKoordinat = true;
                                        setTimeout(() => { this.copiedKoordinat = false; }, 3000);
                                    }).catch(() => {
                                        this.fallbackCopyText(teks);
                                    });
                                } else {
                                    this.fallbackCopyText(teks);
                                }
                            },

                            fallbackCopyText(text) {
                                const input = document.createElement('input');
                                input.value = text;
                                document.body.appendChild(input);
                                input.select();
                                document.execCommand('copy');
                                document.body.removeChild(input);
                                this.copiedKoordinat = true;
                                setTimeout(() => { this.copiedKoordinat = false; }, 3000);
                            },

                            prosesSampelGps(pos) {
                                const acc = Math.round(pos.coords.accuracy);
                                const rawLat = pos.coords.latitude;
                                const rawLng = pos.coords.longitude;
                                const now = Date.now();

                                // Ignore extreme outliers (> 800m) if we already have a coordinate
                                if (acc > 800 && this.lat !== null) return;

                                this.gpsSamples.push({ lat: rawLat, lng: rawLng, acc: acc, ts: now });

                                // Retain recent high-accuracy samples (last 25 seconds, up to 10 samples)
                                this.gpsSamples = this.gpsSamples.filter(s => now - s.ts < 25000).slice(-10);

                                // If any sample has ultra-high precision (<= 12m), prioritize it
                                const ultraAcc = this.gpsSamples.filter(s => s.acc <= 12);
                                if (ultraAcc.length > 0) {
                                    const latest = ultraAcc[ultraAcc.length - 1];
                                    this.lat = latest.lat;
                                    this.lng = latest.lng;
                                    this.akurasi = latest.acc;
                                } else {
                                    // Weighted Least Squares averaging (weights = 1 / acc^2)
                                    let totalWeight = 0;
                                    let sumLat = 0;
                                    let sumLng = 0;
                                    let minAcc = Infinity;

                                    for (const s of this.gpsSamples) {
                                        const w = 1 / Math.max(s.acc * s.acc, 1);
                                        totalWeight += w;
                                        sumLat += s.lat * w;
                                        sumLng += s.lng * w;
                                        if (s.acc < minAcc) minAcc = s.acc;
                                    }

                                    if (totalWeight > 0) {
                                        this.lat = sumLat / totalWeight;
                                        this.lng = sumLng / totalWeight;
                                        this.akurasi = minAcc;
                                    } else {
                                        this.lat = rawLat;
                                        this.lng = rawLng;
                                        this.akurasi = acc;
                                    }
                                }

                                this.gpsIzin = 'granted';
                                this.gpsError = null;
                                this.hitungJarak();
                                if (!this.map) {
                                    this.setupMap();
                                } else {
                                    this.updateMapUserMarker();
                                }
                            },

                            mulaiPelacakanGps() {
                                if (!navigator.geolocation) {
                                    this.gpsError = 'Geolocation GPS tidak didukung di browser ini.';
                                    this.gpsIzin = 'denied';
                                    return;
                                }

                                this.loadingGps = true;
                                this.gpsError = null;

                                const opsiTinggi = {
                                    enableHighAccuracy: true,
                                    timeout: 20000,
                                    maximumAge: 0
                                };

                                navigator.geolocation.getCurrentPosition(
                                    (pos) => {
                                        this.loadingGps = false;
                                        this.prosesSampelGps(pos);
                                    },
                                    (err) => {
                                        this.loadingGps = false;
                                        if (err.code === 1) {
                                            this.gpsIzin = 'denied';
                                            this.gpsError = 'Izin akses lokasi GPS ditolak oleh browser/pengguna.';
                                        } else if (err.code === 2) {
                                            this.gpsError = 'Sinyal satelit GPS tidak dapat diperoleh. Pastikan GPS perangkat aktif.';
                                        } else {
                                            this.gpsError = 'Waktu permintaan GPS habis: ' + err.message;
                                        }
                                    },
                                    opsiTinggi
                                );

                                // Continuous High-Accuracy Geolocation Watch
                                if (this.watchGpsId !== null) {
                                    navigator.geolocation.clearWatch(this.watchGpsId);
                                }

                                this.watchGpsId = navigator.geolocation.watchPosition(
                                    (pos) => {
                                        this.loadingGps = false;
                                        this.prosesSampelGps(pos);
                                    },
                                    (err) => {
                                        if (err.code === 1) {
                                            this.gpsIzin = 'denied';
                                        }
                                    },
                                    { enableHighAccuracy: true, timeout: 20000, maximumAge: 2000 }
                                );
                            },

                            kalibrasiGpsAkurat() {
                                if (!navigator.geolocation) {
                                    this.gpsError = 'Geolocation GPS tidak didukung di browser ini.';
                                    this.gpsIzin = 'denied';
                                    return;
                                }

                                this.kalibrasiBerjalan = true;
                                this.loadingGps = true;
                                this.gpsError = null;
                                this.gpsSamples = []; // Reset sample buffer for fresh calibration

                                const opsiTinggi = {
                                    enableHighAccuracy: true,
                                    timeout: 15000,
                                    maximumAge: 0
                                };

                                let count = 0;
                                const intervalId = setInterval(() => {
                                    count++;
                                    navigator.geolocation.getCurrentPosition(
                                        (pos) => { this.prosesSampelGps(pos); },
                                        () => {},
                                        opsiTinggi
                                    );
                                    if (count >= 4) {
                                        clearInterval(intervalId);
                                    }
                                }, 750);

                                setTimeout(() => {
                                    this.kalibrasiBerjalan = false;
                                    this.loadingGps = false;
                                    if (this.lat && this.lng) {
                                        this.pusatkanPeta('user');
                                        this.bicara('Kalibrasi GPS selesai. Akurasi saat ini: ' + (this.akurasi || 0) + ' meter.');
                                    }
                                }, 3500);
                            },

                            hitungJarak() {
                                if (!this.lat || !this.lng || !this.sekolahLat || !this.sekolahLng) {
                                    this.jarakMeter = null;
                                    return;
                                }
                                const R = 6371000;
                                const dLat = (this.sekolahLat - this.lat) * Math.PI / 180;
                                const dLng = (this.sekolahLng - this.lng) * Math.PI / 180;
                                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                                          Math.cos(this.lat * Math.PI / 180) * Math.cos(this.sekolahLat * Math.PI / 180) *
                                          Math.sin(dLng / 2) * Math.sin(dLng / 2);
                                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                                this.jarakMeter = Math.round(R * c);
                            },

                            async mulaiKamera() {
                                this.kameraError = null;
                                try {
                                    if (this.streamKamera) {
                                        this.streamKamera.getTracks().forEach(t => t.stop());
                                    }
                                    this.streamKamera = await navigator.mediaDevices.getUserMedia({
                                        video: {
                                            facingMode: this.facingMode,
                                            width: { ideal: 720 },
                                            height: { ideal: 540 }
                                        },
                                        audio: false
                                    });
                                    if (this.$refs.videoElem) {
                                        this.$refs.videoElem.srcObject = this.streamKamera;
                                        this.kameraAktif = true;
                                        this.kameraIzin = 'granted';
                                    }
                                } catch (err) {
                                    console.warn('Kamera error:', err);
                                    this.kameraAktif = false;
                                    this.kameraIzin = 'denied';
                                    this.kameraError = 'Izin kamera selfie ditolak atau kamera tidak terdeteksi. Wajib mengizinkan kamera live.';
                                }
                            },

                            gantiKamera() {
                                this.facingMode = this.facingMode === 'user' ? 'environment' : 'user';
                                this.mulaiKamera();
                            },

                            ambilFoto() {
                                if (!this.kameraAktif || !this.$refs.videoElem) {
                                    alert('Kamera Belum Aktif: Pastikan izin kamera telah disetujui di browser.');
                                    return null;
                                }

                                const video = this.$refs.videoElem;
                                const canvas = document.createElement('canvas');
                                const vw = video.videoWidth || 640;
                                const vh = video.videoHeight || 480;
                                canvas.width = vw;
                                canvas.height = vh;
                                const ctx = canvas.getContext('2d');

                                if (this.facingMode === 'user') {
                                    ctx.translate(vw, 0);
                                    ctx.scale(-1, 1);
                                }

                                ctx.drawImage(video, 0, 0, vw, vh);
                                this.fotoBase64 = canvas.toDataURL('image/jpeg', 0.85);

                                // Trigger Shutter Flash
                                this.shutterFlash = true;
                                setTimeout(() => {
                                    this.shutterFlash = false;
                                }, 180);

                                return this.fotoBase64;
                            },

                            ulangFoto() {
                                this.fotoBase64 = null;
                                if (!this.kameraAktif) {
                                    this.mulaiKamera();
                                }
                            },

                            async kirimCheckIn() {
                                if (this.submitting) return;

                                if ((!this.lat || !this.lng) && navigator.geolocation) {
                                    await new Promise((resolve) => {
                                        navigator.geolocation.getCurrentPosition(
                                            (pos) => {
                                                this.prosesSampelGps(pos);
                                                resolve(true);
                                            },
                                            () => resolve(false),
                                            { timeout: 5000, enableHighAccuracy: true }
                                        );
                                    });
                                }

                                if (this.wajibLokasi && (!this.lat || !this.lng)) {
                                    alert('Gagal Melakukan Presensi: Koordinat GPS lokasi Anda belum terdeteksi. Silakan izinkan akses lokasi pada browser lalu tekan Kalibrasi GPS.');
                                    return;
                                }

                                if (this.diLuarRadius || (this.wajibLokasi && this.diLuarRadius)) {
                                    this.bicara('Presensi ditolak. Posisi Anda berada di luar radius sekolah yang ditentukan admin.');
                                    alert('Presensi Ditolak! Posisi Anda berada di luar radius sekolah (' + this.jarakMeter + ' meter dari sekolah). Batas maksimal radius yang ditentukan admin adalah ' + this.radiusSekolah + ' meter. Data presensi tidak dapat direkam.');
                                    return;
                                }

                                // Verifikasi Kamera & Foto Langsung
                                if (!this.fotoBase64 && !this.kameraAktif) {
                                    alert('Verifikasi Biometrik Wajib: Kamera harus aktif dan foto selfie wajib diambil langsung dari kamera perangkat (lampiran dokumen/file tidak diizinkan).');
                                    return;
                                }

                                const foto = this.fotoBase64 || this.ambilFoto();
                                if (!foto) {
                                    alert('Gagal Mengambil Foto: Silakan pastikan kamera live berfungsi dengan baik.');
                                    return;
                                }

                                const deviceId = this.getDeviceId();
                                const akurasi = this.akurasiGps || this.akurasiMeter || null;

                                this.submitting = true;
                                this.$wire.checkIn(this.lat, this.lng, foto, deviceId, akurasi)
                                    .then(() => {
                                        this.bicara('Terima kasih, Presensi Masuk Anda berhasil dicatat. Selamat bertugas!');
                                    })
                                    .finally(() => { this.submitting = false; });
                            },

                            getDeviceId() {
                                let id = localStorage.getItem('sekolah_guru_device_id');
                                if (!id) {
                                    const screenInfo = `${window.screen.width}x${window.screen.height}`;
                                    const randomPart = Math.random().toString(36).substring(2, 10) + Date.now().toString(36);
                                    id = 'DEV-' + btoa(screenInfo).replace(/[^a-zA-Z0-9]/g, '').substring(0, 8) + '-' + randomPart.substring(0, 8);
                                    localStorage.setItem('sekolah_guru_device_id', id);
                                }
                                return id;
                            },

                            async kirimCheckOut() {
                                if (this.submitting) return;

                                if ((!this.lat || !this.lng) && navigator.geolocation) {
                                    await new Promise((resolve) => {
                                        navigator.geolocation.getCurrentPosition(
                                            (pos) => {
                                                this.prosesSampelGps(pos);
                                                resolve(true);
                                            },
                                            () => resolve(false),
                                            { timeout: 5000, enableHighAccuracy: true }
                                        );
                                    });
                                }

                                if (this.wajibLokasi && (!this.lat || !this.lng)) {
                                    alert('Gagal Melakukan Presensi: Koordinat GPS lokasi Anda belum terdeteksi. Silakan izinkan akses lokasi pada browser lalu tekan Kalibrasi GPS.');
                                    return;
                                }

                                if (this.diLuarRadius || (this.wajibLokasi && this.diLuarRadius)) {
                                    this.bicara('Presensi ditolak. Posisi Anda berada di luar radius sekolah yang ditentukan admin.');
                                    alert('Presensi Ditolak! Posisi Anda berada di luar radius sekolah (' + this.jarakMeter + ' meter dari sekolah). Batas maksimal radius yang ditentukan admin adalah ' + this.radiusSekolah + ' meter. Data presensi tidak dapat direkam.');
                                    return;
                                }

                                // Verifikasi Kamera & Foto Langsung
                                if (!this.fotoBase64 && !this.kameraAktif) {
                                    alert('Verifikasi Biometrik Wajib: Kamera harus aktif dan foto selfie wajib diambil langsung dari kamera perangkat (lampiran dokumen/file tidak diizinkan).');
                                    return;
                                }

                                const foto = this.fotoBase64 || this.ambilFoto();
                                if (!foto) {
                                    alert('Gagal Mengambil Foto: Silakan pastikan kamera live berfungsi dengan baik.');
                                    return;
                                }

                                // Dialog Pengisian Jurnal Pembelajaran Harian (Opsional bagi Guru)
                                let jurnal = null;
                                const tanyaJurnal = confirm('Apakah Anda ingin mengisi Jurnal Pembelajaran / Kinerja Mengajar hari ini sebelum presensi pulang?');
                                if (tanyaJurnal) {
                                    const kelas = prompt('Kelas / Rombongan Belajar:', 'Kelas IV');
                                    const mapel = prompt('Mata Pelajaran:', 'KBM Tematik');
                                    const materi = prompt('Materi Pokok & Kegiatan KBM Hari Ini:');
                                    if (materi && materi.trim() !== '') {
                                        jurnal = {
                                            kelas: kelas || 'Kelas',
                                            mata_pelajaran: mapel || 'KBM',
                                            materi_kegiatan: materi,
                                            jumlah_jam: 2
                                        };
                                    }
                                }

                                const deviceId = this.getDeviceId();
                                const akurasi = this.akurasiGps || this.akurasiMeter || null;

                                this.submitting = true;
                                this.$wire.checkOut(this.lat, this.lng, foto, deviceId, akurasi, jurnal)
                                    .then(() => {
                                        this.bicara('Terima kasih, Presensi Pulang Anda berhasil dicatat. Hati-hati di perjalanan!');
                                    })
                                    .finally(() => { this.submitting = false; });
                            }
                        }));
                    }
                }

                if (typeof Alpine !== 'undefined') {
                    initPresensiMandiriApp();
                }
                document.addEventListener('alpine:init', initPresensiMandiriApp);
            })();
        </script>
    </div>
</x-filament-panels::page>
