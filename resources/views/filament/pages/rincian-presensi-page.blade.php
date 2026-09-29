<x-filament-panels::page>
    @php
        $pengaturan = \App\Models\PengaturanSekolah::getSetting();
        $stats = $this->statistikKehadiran;
    @endphp

    <style>
        :root {
            --rincian-bg: #ffffff;
            --rincian-border: #e2e8f0;
            --rincian-ink: #0f172a;
            --rincian-muted: #64748b;
            --rincian-subtle: #f8fafc;
            --rincian-accent: #0f766e;
            --rincian-accent-soft: #f0fdfa;
            --rincian-accent-border: #ccfbf1;
        }

        .dark, html.dark, .fi-theme-dark,
        .dark .rincian-wrapper, html.dark .rincian-wrapper {
            --rincian-bg: #111827;
            --rincian-border: #1f2937;
            --rincian-ink: #f8fafc;
            --rincian-muted: #94a3b8;
            --rincian-subtle: #182234;
            --rincian-accent: #14b8a6;
            --rincian-accent-soft: rgba(20, 184, 166, 0.12);
            --rincian-accent-border: rgba(20, 184, 166, 0.3);
        }

        .rincian-wrapper {
            display: flex;
            flex-direction: column;
            gap: 16px;
            font-family: inherit;
            color: var(--rincian-ink);
        }

        /* HEADER SECTION */
        .rincian-header-card {
            background: var(--rincian-bg);
            border: 1px solid var(--rincian-border);
            border-radius: 14px;
            padding: 16px 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .rincian-title-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .rincian-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 750;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--rincian-accent);
        }

        .rincian-eyebrow-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: var(--rincian-accent);
            animation: pulse-subtle 2s infinite;
        }

        @keyframes pulse-subtle {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.85); }
        }

        .rincian-title-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .rincian-main-title {
            font-size: 18px;
            font-weight: 800;
            margin: 0;
            color: var(--rincian-ink);
            letter-spacing: -0.01em;
        }

        .rincian-badge-periode {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--rincian-accent-soft);
            border: 1px solid var(--rincian-accent-border);
            color: var(--rincian-accent);
            font-size: 12px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 6px;
        }

        .rincian-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-rincian {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
            border: 1px solid transparent;
            min-height: 38px;
        }

        .btn-rincian-primary {
            background: #0f766e;
            color: #ffffff !important;
            border-color: #0d9488;
        }
        .btn-rincian-primary:hover {
            background: #115e59;
            box-shadow: 0 3px 10px rgba(15, 118, 110, 0.25);
        }

        .btn-rincian-secondary {
            background: var(--rincian-bg);
            border-color: var(--rincian-border);
            color: var(--rincian-ink) !important;
        }
        .btn-rincian-secondary:hover {
            background: var(--rincian-subtle);
            border-color: var(--rincian-accent);
            color: var(--rincian-accent) !important;
        }

        .btn-rincian-reset {
            background: transparent;
            border-color: var(--rincian-border);
            color: var(--rincian-muted) !important;
        }
        .btn-rincian-reset:hover {
            background: rgba(239, 68, 68, 0.08);
            border-color: #fca5a5;
            color: #ef4444 !important;
        }

        /* KPI STAT METRICS */
        .rincian-stats-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 10px;
        }

        .stat-card {
            background: var(--rincian-bg);
            border: 1px solid var(--rincian-border);
            border-radius: 10px;
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
            border-left-width: 4px;
        }

        .stat-card-total { border-left-color: #0284c7; }
        .stat-card-hadir { border-left-color: #10b981; }
        .stat-card-terlambat { border-left-color: #f59e0b; }
        .stat-card-dinas { border-left-color: #06b6d4; }
        .stat-card-izin { border-left-color: #8b5cf6; }
        .stat-card-alpa { border-left-color: #ef4444; }

        .stat-label {
            font-size: 10.5px;
            font-weight: 750;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--rincian-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 800;
            color: var(--rincian-ink);
            font-variant-numeric: tabular-nums;
            line-height: 1.2;
        }

        /* FILTER TOOLBAR */
        .rincian-filter-card {
            background: var(--rincian-bg);
            border: 1px solid var(--rincian-border);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);
        }

        .filter-search-box {
            position: relative;
            flex: 1 1 240px;
            min-width: 200px;
        }

        .filter-search-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            color: var(--rincian-muted);
            pointer-events: none;
        }

        .filter-search-input {
            width: 100%;
            height: 36px;
            padding: 0 12px 0 34px;
            background: var(--rincian-bg);
            border: 1px solid var(--rincian-border);
            border-radius: 8px;
            font-size: 12.5px;
            color: var(--rincian-ink);
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .filter-search-input:focus {
            border-color: var(--rincian-accent);
            box-shadow: 0 0 0 3px var(--rincian-accent-soft);
        }

        .filter-select-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            flex: 2 1 auto;
        }

        .filter-select-wrapper {
            display: flex;
            align-items: center;
            min-width: 125px;
            flex: 1 1 125px;
        }

        .filter-select {
            width: 100%;
            height: 36px;
            padding: 0 10px;
            background: var(--rincian-bg);
            border: 1px solid var(--rincian-border);
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--rincian-ink);
            outline: none;
            cursor: pointer;
            transition: border-color 0.15s;
        }
        .filter-select:focus {
            border-color: var(--rincian-accent);
            box-shadow: 0 0 0 3px var(--rincian-accent-soft);
        }

        /* DATA TABLE CONTAINER */
        .rincian-table-shell {
            background: var(--rincian-bg);
            border: 1px solid var(--rincian-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
        }

        .rincian-table-wrapper {
            overflow-x: auto;
            width: 100%;
            max-height: calc(100vh - 280px);
        }

        .rincian-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 12.5px;
            text-align: left;
            line-height: 1.4;
        }

        .rincian-table thead {
            position: sticky;
            top: 0;
            z-index: 10;
            background: var(--rincian-subtle);
        }

        .rincian-table th {
            padding: 9px 12px;
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--rincian-muted);
            border-bottom: 1px solid var(--rincian-border);
            white-space: nowrap;
            background: var(--rincian-subtle);
        }

        .rincian-table td {
            padding: 9px 12px;
            border-bottom: 1px solid var(--rincian-border);
            color: var(--rincian-ink);
            vertical-align: middle;
            transition: background 0.1s ease;
        }

        .rincian-table tbody tr:last-child td {
            border-bottom: none;
        }

        .rincian-table tbody tr:hover td {
            background-color: var(--rincian-accent-soft);
        }

        /* Column Specific Styling */
        .col-no { width: 44px; text-align: center; color: var(--rincian-muted); font-size: 11px; font-variant-numeric: tabular-nums; }
        .col-date { width: 115px; white-space: nowrap; font-variant-numeric: tabular-nums; }
        .col-guru { min-width: 220px; }
        .col-shift { width: 120px; text-align: center; color: var(--rincian-muted); }
        .col-status { width: 115px; text-align: center; }
        .col-in { width: 100px; text-align: center; }
        .col-out { width: 100px; text-align: center; }
        .col-duration { width: 85px; text-align: center; font-variant-numeric: tabular-nums; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 11.5px; }
        .col-note { min-width: 140px; color: var(--rincian-muted); font-size: 12px; }

        .guru-name {
            font-weight: 750;
            color: var(--rincian-ink);
            display: block;
        }

        .guru-meta {
            font-size: 11px;
            color: var(--rincian-muted);
            margin-top: 1px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .time-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            line-height: 1.2;
        }

        .time-val {
            font-weight: 800;
            font-size: 13px;
            font-variant-numeric: tabular-nums;
        }

        .time-val.late { color: #d97706; }
        .time-val.normal { color: var(--rincian-ink); }

        .time-badge {
            font-size: 9.5px;
            font-weight: 700;
            margin-top: 2px;
            text-transform: capitalize;
        }
        .time-badge.late { color: #b45309; }
        .time-badge.ontime { color: #059669; }
        .time-badge.subtle { color: var(--rincian-muted); }

        /* Status Badges */
        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 750;
            letter-spacing: 0.02em;
            white-space: nowrap;
            line-height: 1.2;
            border: 1px solid transparent;
        }

        .badge-hadir { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
        .badge-terlambat { background: #fef3c7; color: #b45309; border-color: #fde68a; }
        .badge-dinas { background: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
        .badge-sakit { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
        .badge-izin { background: #fef9c3; color: #a16207; border-color: #fef08a; }
        .badge-cuti { background: #f3e8ff; color: #7e22ce; border-color: #e9d5ff; }
        .badge-alpa { background: #ffe4e6; color: #be123c; border-color: #fecdd3; }

        .dark .badge-hadir { background: rgba(21, 128, 61, 0.25); color: #86efac; border-color: rgba(21, 128, 61, 0.4); }
        .dark .badge-terlambat { background: rgba(180, 83, 9, 0.25); color: #fde68a; border-color: rgba(180, 83, 9, 0.4); }
        .dark .badge-dinas { background: rgba(14, 165, 233, 0.25); color: #7dd3fc; border-color: rgba(14, 165, 233, 0.4); }
        .dark .badge-sakit { background: rgba(29, 78, 216, 0.25); color: #93c5fd; border-color: rgba(29, 78, 216, 0.4); }
        .dark .badge-izin { background: rgba(161, 98, 7, 0.25); color: #fef08a; border-color: rgba(161, 98, 7, 0.4); }
        .dark .badge-cuti { background: rgba(126, 34, 206, 0.25); color: #d8b4fe; border-color: rgba(126, 34, 206, 0.4); }
        .dark .badge-alpa { background: rgba(190, 18, 60, 0.25); color: #fda4af; border-color: rgba(190, 18, 60, 0.4); }

        /* FOOTER / PAGINATION */
        .rincian-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            background: var(--rincian-subtle);
            border-top: 1px solid var(--rincian-border);
            font-size: 12px;
            color: var(--rincian-muted);
            flex-wrap: wrap;
            gap: 12px;
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-page {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid var(--rincian-border);
            background: var(--rincian-bg);
            color: var(--rincian-ink);
            font-size: 11.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-page:hover:not(:disabled) {
            border-color: var(--rincian-accent);
            background: var(--rincian-accent-soft);
            color: var(--rincian-accent);
        }
        .btn-page:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--rincian-muted);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        /* PRINT STYLES */
        .print-only-kop { display: none; }
        .print-only-ttd { display: none; }

        @page { size: landscape; margin: 8mm 10mm; }

        @media print {
            html, body {
                overflow: visible !important;
                height: auto !important;
                min-height: 0 !important;
                background: #ffffff !important;
                color: #000000 !important;
            }

            .fi-sidebar, .fi-topbar, .fi-header, .fi-breadcrumbs, .fi-sidebar-close-overlay,
            .rincian-header-card, .rincian-stats-grid, .rincian-filter-card, .rincian-footer {
                display: none !important;
            }

            .fi-layout, .fi-main-ctn, .fi-main, .fi-page, .fi-page-content, .rincian-wrapper {
                overflow: visible !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                display: block !important;
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .rincian-table-shell {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                overflow: visible !important;
            }

            .rincian-table-wrapper {
                overflow: visible !important;
                max-height: none !important;
            }

            .rincian-table {
                font-size: 8pt !important;
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .rincian-table th, .rincian-table td {
                border: 0.8px solid #000000 !important;
                padding: 3px 5px !important;
                color: #000000 !important;
                background: transparent !important;
            }

            .rincian-table thead {
                display: table-header-group !important;
            }

            .rincian-table tr {
                page-break-inside: avoid !important;
            }

            .status-pill {
                border: 0.5px solid #000000 !important;
                background: transparent !important;
                color: #000000 !important;
                font-size: 7.5pt !important;
                padding: 1px 4px !important;
            }

            .print-only-kop {
                display: block !important;
                margin-bottom: 12px !important;
            }

            .print-only-ttd {
                display: table !important;
                width: 100% !important;
                margin-top: 25px !important;
                page-break-inside: avoid !important;
            }
        }

        /* RESPONSIVE BREAKPOINTS */
        @media (max-width: 1100px) {
            .rincian-stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @media (max-width: 768px) {
            .rincian-stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .rincian-header-card { flex-direction: column; align-items: flex-start; }
            .rincian-actions { width: 100%; justify-content: flex-start; }
            .filter-select-group { width: 100%; }
            .filter-select-wrapper { min-width: 100%; }
        }
    </style>

    <!-- PRINT HEADER KOP SURAT (Hanya tampil saat dicetak dari browser) -->
    <div class="print-only-kop">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 3px;">
            <tr>
                <td style="width: 65px; vertical-align: middle; text-align: center;">
                    @if($pengaturan->logo_url)
                        <img src="{{ $pengaturan->logo_url }}" alt="Logo" style="max-height: 60px; max-width: 60px; object-fit: contain;">
                    @else
                        <div style="font-size: 24pt; border: 1.5px solid #000; border-radius: 50%; width: 50px; height: 50px; line-height: 50px; text-align: center;">🏫</div>
                    @endif
                </td>
                <td style="text-align: center; vertical-align: middle; padding: 0 10px;">
                    <div style="font-size: 11pt; font-weight: bold; text-transform: uppercase;">PEMERINTAH KABUPATEN / KOTA - DINAS PENDIDIKAN</div>
                    <div style="font-size: 14pt; font-weight: bold; text-transform: uppercase;">{{ $pengaturan->nama_sekolah }}</div>
                    <div style="font-size: 8.5pt;">{{ $pengaturan->alamat }} @if($pengaturan->telepon) | Telp: {{ $pengaturan->telepon }} @endif @if($pengaturan->npsn) | NPSN: {{ $pengaturan->npsn }} @endif</div>
                </td>
            </tr>
        </table>
        <div style="border-top: 2px solid #000; border-bottom: 0.8px solid #000; height: 3px; margin: 4px 0 10px 0;"></div>
        <div style="text-align: center; margin-bottom: 8px;">
            <div style="font-size: 12pt; font-weight: bold; text-decoration: underline;">LAPORAN RINCIAN PRESENSI &amp; JAM KERJA HARIAN GURU</div>
            <div style="font-size: 9.5pt;">Periode: {{ $this->daftarBulan[$bulan] }} {{ $tahun }} | Filter: {{ $this->statusKepegawaian === 'semua' ? 'Semua Status Pegawai' : strtoupper(str_replace('_', ' ', $this->statusKepegawaian)) }}</div>
        </div>
    </div>

    <div class="rincian-wrapper">
        <!-- 1. HEADER SECTION -->
        <header class="rincian-header-card">
            <div class="rincian-title-group">
                <div class="rincian-eyebrow">
                    <span class="rincian-eyebrow-dot"></span>
                    <span>Monitoring Presensi Terperinci</span>
                </div>
                <div class="rincian-title-row">
                    <h1 class="rincian-main-title">Rincian Jam Presensi Harian</h1>
                    <span class="rincian-badge-periode">
                        <x-filament::icon icon="heroicon-o-calendar-days" class="w-4 h-4" />
                        {{ $this->daftarBulan[$bulan] }} {{ $tahun }}
                    </span>
                </div>
            </div>

            <div class="rincian-actions">
                @if ($this->hasActiveFilters)
                    <button type="button" wire:click="resetFilters" class="btn-rincian btn-rincian-reset" title="Kembalikan semua filter ke standar">
                        <x-filament::icon icon="heroicon-o-arrow-path" class="w-4 h-4" />
                        <span>Reset Filter</span>
                    </button>
                @endif

                <a href="{{ $this->urlCetak }}" target="_blank" rel="noopener" class="btn-rincian btn-rincian-primary" title="Cetak Dokumen Resmi Kedinasan A4 Landscape">
                    <x-filament::icon icon="heroicon-o-printer" class="w-4 h-4" />
                    <span>Cetak Dokumen (PDF)</span>
                </a>

                <a href="{{ $this->urlLaporan }}" class="btn-rincian btn-rincian-secondary" title="Kembali ke Rekapitulasi Matriks Presensi">
                    <x-filament::icon icon="heroicon-o-arrow-left" class="w-4 h-4" />
                    <span>Matriks Bulanan</span>
                </a>
            </div>
        </header>

        <!-- 2. QUICK KPI SUMMARY METRICS -->
        <section class="rincian-stats-grid" aria-label="Ringkasan Statistik Kehadiran">
            <div class="stat-card stat-card-total">
                <span class="stat-label">Total Presensi</span>
                <span class="stat-value">{{ number_format($stats['total'], 0, ',', '.') }}</span>
            </div>
            <div class="stat-card stat-card-hadir">
                <span class="stat-label">Tepat Waktu</span>
                <span class="stat-value" style="color: #10b981;">{{ number_format($stats['tepat_waktu'], 0, ',', '.') }}</span>
            </div>
            <div class="stat-card stat-card-terlambat">
                <span class="stat-label">Terlambat</span>
                <span class="stat-value" style="color: #f59e0b;">{{ number_format($stats['terlambat'], 0, ',', '.') }}</span>
            </div>
            <div class="stat-card stat-card-dinas">
                <span class="stat-label">Dinas Luar</span>
                <span class="stat-value" style="color: #06b6d4;">{{ number_format($stats['dinas_luar'], 0, ',', '.') }}</span>
            </div>
            <div class="stat-card stat-card-izin">
                <span class="stat-label">Izin / Sakit / Cuti</span>
                <span class="stat-value" style="color: #8b5cf6;">{{ number_format($stats['izin'] + $stats['sakit'] + $stats['cuti'], 0, ',', '.') }}</span>
            </div>
            <div class="stat-card stat-card-alpa">
                <span class="stat-label">Alpa / Tanpa Ket.</span>
                <span class="stat-value" style="color: #ef4444;">{{ number_format($stats['alpa'], 0, ',', '.') }}</span>
            </div>
        </section>

        <!-- 3. COMPACT FILTER TOOLBAR -->
        <section class="rincian-filter-card" aria-label="Filter Presensi">
            <div class="filter-search-box">
                <x-filament::icon icon="heroicon-o-magnifying-glass" class="filter-search-icon" />
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama, NIP, atau jabatan..."
                    class="filter-search-input"
                />
            </div>

            <div class="filter-select-group">
                <div class="filter-select-wrapper">
                    <select wire:model.live="bulan" class="filter-select" aria-label="Pilih Bulan">
                        @foreach ($this->daftarBulan as $noBulan => $nmBulan)
                            <option value="{{ $noBulan }}">{{ $nmBulan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-select-wrapper" style="max-width: 110px;">
                    <select wire:model.live="tahun" class="filter-select" aria-label="Pilih Tahun">
                        @foreach ($this->daftarTahun as $thPilihan)
                            <option value="{{ $thPilihan }}">{{ $thPilihan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-select-wrapper">
                    <select wire:model.live="statusKepegawaian" class="filter-select" aria-label="Pilih Status Kepegawaian">
                        <option value="semua">Semua Status</option>
                        <option value="pns">PNS</option>
                        <option value="pppk">PPPK</option>
                        <option value="non_pns">Non-PNS / Honorer</option>
                    </select>
                </div>

                <div class="filter-select-wrapper">
                    <select wire:model.live="shiftId" class="filter-select" aria-label="Pilih Shift Kerja">
                        <option value="">Semua Shift</option>
                        @foreach ($this->daftarShift as $sh)
                            <option value="{{ $sh->id }}">{{ $sh->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        <!-- 4. DATA TABLE -->
        <section class="rincian-table-shell" aria-label="Tabel Rincian Presensi">
            <div class="rincian-table-wrapper">
                <table class="rincian-table">
                    <thead>
                        <tr>
                            <th class="col-no">No</th>
                            <th class="col-date">Tanggal &amp; Hari</th>
                            <th class="col-guru">Guru / Pegawai</th>
                            <th class="col-shift">Shift Kerja</th>
                            <th class="col-status">Kehadiran</th>
                            <th class="col-in">Jam Masuk</th>
                            <th class="col-out">Jam Pulang</th>
                            <th class="col-duration">Durasi</th>
                            <th class="col-note">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->daftarRincian as $idx => $presensi)
                            @php
                                $badgeClass = match ($presensi->status_kehadiran) {
                                    'hadir' => ($presensi->status_masuk === 'terlambat' ? 'badge-terlambat' : 'badge-hadir'),
                                    'dinas_luar' => 'badge-dinas',
                                    'sakit' => 'badge-sakit',
                                    'izin' => 'badge-izin',
                                    'cuti' => 'badge-cuti',
                                    'alpa' => 'badge-alpa',
                                    default => '',
                                };

                                $statusLabel = match ($presensi->status_kehadiran) {
                                    'hadir' => ($presensi->status_masuk === 'terlambat' ? 'Terlambat' : 'Hadir'),
                                    'dinas_luar' => 'Dinas Luar',
                                    'sakit' => 'Sakit',
                                    'izin' => 'Izin',
                                    'cuti' => 'Cuti',
                                    'alpa' => 'Alpa',
                                    default => str($presensi->status_kehadiran)->replace('_', ' ')->title()->toString(),
                                };

                                $durasiFormatted = '-';
                                if ($presensi->jam_masuk && $presensi->jam_pulang) {
                                    $menitTotal = abs((int) $presensi->jam_masuk->diffInMinutes($presensi->jam_pulang));
                                    $durasiFormatted = floor($menitTotal / 60).'j '.($menitTotal % 60).'m';
                                }

                                $rowNum = ($this->daftarRincian instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                    ? ($this->daftarRincian->firstItem() + $idx)
                                    : ($idx + 1);
                            @endphp
                            <tr class="detail-row">
                                <td class="col-no">{{ $rowNum }}</td>
                                <td class="col-date">
                                    <span style="font-weight: 700;">{{ $presensi->tanggal?->locale('id')->translatedFormat('D, d M Y') ?? '-' }}</span>
                                </td>
                                <td class="col-guru">
                                    <span class="guru-name">{{ $presensi->guru?->nama ?? 'Guru tidak ditemukan' }}</span>
                                    <div class="guru-meta">
                                        <span>{{ $presensi->guru?->nip ? 'NIP. '.$presensi->guru->nip : ($presensi->guru?->nuptk ? 'NUPTK. '.$presensi->guru->nuptk : 'Non-NIP') }}</span>
                                        @if($presensi->guru?->jabatan)
                                            <span>&bull; {{ $presensi->guru->jabatan }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="col-shift">{{ $presensi->shift?->nama ?? '-' }}</td>
                                <td class="col-status">
                                    <span class="status-pill {{ $badgeClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="col-in">
                                    @if ($presensi->jam_masuk)
                                        <div class="time-box">
                                            <span class="time-val {{ $presensi->status_masuk === 'terlambat' ? 'late' : 'normal' }}">
                                                {{ $presensi->jam_masuk->format('H:i') }}
                                            </span>
                                            @if ($presensi->status_masuk === 'terlambat')
                                                <span class="time-badge late">Terlambat</span>
                                            @elseif ($presensi->status_masuk === 'tepat_waktu')
                                                <span class="time-badge ontime">Tepat Waktu</span>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color: var(--rincian-muted);">-</span>
                                    @endif
                                </td>
                                <td class="col-out">
                                    @if ($presensi->jam_pulang)
                                        <div class="time-box">
                                            <span class="time-val normal">{{ $presensi->jam_pulang->format('H:i') }}</span>
                                            @if ($presensi->status_pulang)
                                                <span class="time-badge subtle">{{ str($presensi->status_pulang)->replace('_', ' ')->title() }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color: var(--rincian-muted);">-</span>
                                    @endif
                                </td>
                                <td class="col-duration">{{ $durasiFormatted }}</td>
                                <td class="col-note">{{ $presensi->keterangan ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <x-filament::icon icon="heroicon-o-document-magnifying-glass" class="w-10 h-10 text-gray-400" />
                                        <span style="font-weight: 600;">Tidak ada catatan presensi yang sesuai dengan parameter filter ini.</span>
                                        <span style="font-size: 11.5px;">Coba ubah kata kunci pencarian atau ganti pilihan filter di atas.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- TABLE FOOTER / PAGINATION -->
            @unless ($cetakSemua)
                <footer class="rincian-footer">
                    <div>
                        @if ($this->totalRincian > 0)
                            Menampilkan <strong>{{ $this->daftarRincian->firstItem() }}</strong>–<strong>{{ $this->daftarRincian->lastItem() }}</strong> dari <strong>{{ number_format($this->totalRincian, 0, ',', '.') }}</strong> catatan
                        @else
                            Tidak ada data
                        @endif
                    </div>

                    @if ($this->daftarRincian->hasPages())
                        <div class="pagination-controls" aria-label="Navigasi Halaman">
                            <button
                                type="button"
                                wire:click="previousPage"
                                @disabled($this->daftarRincian->onFirstPage())
                                class="btn-page"
                            >
                                &larr; Sebelumnya
                            </button>
                            <span style="font-size: 11.5px; font-weight: 600; padding: 0 4px;">
                                Hal. {{ $this->daftarRincian->currentPage() }} / {{ $this->daftarRincian->lastPage() }}
                            </span>
                            <button
                                type="button"
                                wire:click="nextPage"
                                @disabled(! $this->daftarRincian->hasMorePages())
                                class="btn-page"
                            >
                                Berikutnya &rarr;
                            </button>
                        </div>
                    @endif
                </footer>
            @endunless
        </section>
    </div>

    <!-- PRINT SIGNATURE SECTION (Hanya muncul saat dicetak ke kertas/PDF) -->
    <table class="print-only-ttd">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: top; font-size: 9pt;">
                Mengetahui / Diperiksa oleh,<br>
                <strong>Pengelola Presensi / Kepegawaian</strong>
                <div style="height: 50px;"></div>
                <strong><u>( ............................................................ )</u></strong><br>
                NIP. .......................................................
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top; font-size: 9pt;">
                {{ $pengaturan->nama_sekolah ? explode(' ', $pengaturan->nama_sekolah)[0] : 'Makassar' }}, {{ now()->translatedFormat('d F Y') }}<br>
                Kepala {{ $pengaturan->nama_sekolah }},
                <div style="height: 50px;"></div>
                <strong><u>{{ $pengaturan->kepala_sekolah ?: '( Nama Kepala Sekolah Belum Diatur )' }}</u></strong><br>
                NIP. {{ $pengaturan->nip_kepala_sekolah ?: '-' }}
            </td>
        </tr>
    </table>
</x-filament-panels::page>
