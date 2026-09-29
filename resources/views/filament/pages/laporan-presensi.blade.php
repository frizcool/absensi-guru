<x-filament-panels::page>
    <style>
        :root {
            --lp-card-bg: #ffffff;
            --lp-card-border: #e2e8f0;
            --lp-text-primary: #0f172a;
            --lp-text-secondary: #475569;
            --lp-text-muted: #64748b;
            --lp-input-bg: #ffffff;
            --lp-input-border: #cbd5e1;
            --lp-input-text: #1e293b;
            --lp-table-bg: #ffffff;
            --lp-table-border: #cbd5e1;
            --lp-th-bg: #f1f5f9;
            --lp-th-text: #1e293b;
            --lp-th-sub-bg: #f8fafc;
            --lp-th-sub-text: #64748b;
            --lp-badge-empty: #cbd5e1;
            --lp-libur-bg: #fef2f2;
            --lp-libur-text: #991b1b;
            --lp-libur-th-bg: #fee2e2;
            --lp-libur-th-text: #b91c1c;

            --lp-total-h-bg: #dcfce7;
            --lp-total-h-text: #15803d;
            --lp-total-t-bg: #fef3c7;
            --lp-total-t-text: #b45309;
            --lp-total-dl-bg: #e0f2fe;
            --lp-total-dl-text: #0369a1;
            --lp-total-s-bg: #dbeafe;
            --lp-total-s-text: #1d4ed8;
            --lp-total-i-bg: #fef9c3;
            --lp-total-i-text: #a16207;
            --lp-total-c-bg: #f3e8ff;
            --lp-total-c-text: #7e22ce;
            --lp-total-a-bg: #ffe4e6;
            --lp-total-a-text: #be123c;
            --lp-total-pct-bg: #ecfdf5;
            --lp-total-pct-text: #047857;

            --lp-val-h-bg: #f0fdf4;
            --lp-val-t-bg: #fffbeb;
            --lp-val-dl-bg: #f0f9ff;
            --lp-val-s-bg: #eff6ff;
            --lp-val-i-bg: #fefce8;
            --lp-val-c-bg: #faf5ff;
            --lp-val-a-bg: #fff1f2;
            --lp-val-pct-bg: #ecfdf5;

            --lp-badge-h-bg: #dcfce7;
            --lp-badge-h-text: #15803d;
            --lp-badge-t-bg: #fef3c7;
            --lp-badge-t-text: #b45309;
            --lp-badge-dl-bg: #e0f2fe;
            --lp-badge-dl-text: #0369a1;
            --lp-badge-s-bg: #dbeafe;
            --lp-badge-s-text: #1d4ed8;
            --lp-badge-i-bg: #fef9c3;
            --lp-badge-i-text: #a16207;
            --lp-badge-c-bg: #f3e8ff;
            --lp-badge-c-text: #7e22ce;
            --lp-badge-a-bg: #ffe4e6;
            --lp-badge-a-text: #be123c;
            --lp-badge-l-bg: #f1f5f9;
            --lp-badge-l-text: #64748b;
        }

        .dark, html.dark, .fi-theme-dark,
        .dark .laporan-wrapper, html.dark .laporan-wrapper, .fi-theme-dark .laporan-wrapper {
            --lp-card-bg: #111827;
            --lp-card-border: #1f2937;
            --lp-text-primary: #f9fafb;
            --lp-text-secondary: #cbd5e1;
            --lp-text-muted: #9ca3af;
            --lp-input-bg: #1f2937;
            --lp-input-border: #374151;
            --lp-input-text: #f9fafb;
            --lp-table-bg: #111827;
            --lp-table-border: #374151;
            --lp-th-bg: #1f2937;
            --lp-th-text: #f9fafb;
            --lp-th-sub-bg: #182234;
            --lp-th-sub-text: #9ca3af;
            --lp-badge-empty: #4b5563;
            --lp-libur-bg: rgba(127, 29, 29, 0.25);
            --lp-libur-text: #fca5a5;
            --lp-libur-th-bg: rgba(127, 29, 29, 0.4);
            --lp-libur-th-text: #fca5a5;

            --lp-total-h-bg: rgba(21, 128, 61, 0.35);
            --lp-total-h-text: #86efac;
            --lp-total-t-bg: rgba(180, 83, 9, 0.35);
            --lp-total-t-text: #fde68a;
            --lp-total-dl-bg: rgba(14, 165, 233, 0.35);
            --lp-total-dl-text: #7dd3fc;
            --lp-total-s-bg: rgba(29, 78, 216, 0.35);
            --lp-total-s-text: #93c5fd;
            --lp-total-i-bg: rgba(161, 98, 7, 0.35);
            --lp-total-i-text: #fef08a;
            --lp-total-c-bg: rgba(126, 34, 206, 0.35);
            --lp-total-c-text: #d8b4fe;
            --lp-total-a-bg: rgba(190, 18, 60, 0.35);
            --lp-total-a-text: #fda4af;
            --lp-total-pct-bg: rgba(4, 120, 87, 0.35);
            --lp-total-pct-text: #6ee7b7;

            --lp-val-h-bg: rgba(21, 128, 61, 0.2);
            --lp-val-t-bg: rgba(180, 83, 9, 0.2);
            --lp-val-dl-bg: rgba(14, 165, 233, 0.2);
            --lp-val-s-bg: rgba(29, 78, 216, 0.2);
            --lp-val-i-bg: rgba(161, 98, 7, 0.2);
            --lp-val-c-bg: rgba(126, 34, 206, 0.2);
            --lp-val-a-bg: rgba(190, 18, 60, 0.2);
            --lp-val-pct-bg: rgba(4, 120, 87, 0.2);

            --lp-badge-h-bg: rgba(21, 128, 61, 0.3);
            --lp-badge-h-text: #86efac;
            --lp-badge-t-bg: rgba(180, 83, 9, 0.3);
            --lp-badge-t-text: #fde68a;
            --lp-badge-dl-bg: rgba(14, 165, 233, 0.3);
            --lp-badge-dl-text: #7dd3fc;
            --lp-badge-s-bg: rgba(29, 78, 216, 0.3);
            --lp-badge-s-text: #93c5fd;
            --lp-badge-i-bg: rgba(161, 98, 7, 0.3);
            --lp-badge-i-text: #fef08a;
            --lp-badge-c-bg: rgba(126, 34, 206, 0.3);
            --lp-badge-c-text: #d8b4fe;
            --lp-badge-a-bg: rgba(190, 18, 60, 0.3);
            --lp-badge-a-text: #fda4af;
            --lp-badge-l-bg: #1f2937;
            --lp-badge-l-text: #9ca3af;
        }

        .laporan-wrapper {
            display: flex;
            flex-direction: column;
            gap: 20px;
            font-family: inherit;
        }

        /* FILTER CARD */
        .filter-box {
            background: var(--lp-card-bg);
            border: 1px solid var(--lp-card-border);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 16px;
            align-items: flex-end;
        }
        .filter-col {
            display: flex;
            flex-direction: column;
        }
        .filter-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--lp-text-muted);
            margin-bottom: 6px;
        }
        .filter-input {
            width: 100%;
            height: 40px;
            padding: 8px 12px;
            font-size: 13px;
            border-radius: 10px;
            border: 1px solid var(--lp-input-border);
            background-color: var(--lp-input-bg);
            color: var(--lp-input-text);
            font-weight: 600;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .filter-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }

        /* SEARCH INPUT WITH EMBEDDED ICON */
        .search-wrapper {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
        }
        .search-input {
            padding-left: 36px !important;
        }
        .search-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            color: var(--lp-text-muted);
        }
        .search-icon svg {
            width: 16px !important;
            height: 16px !important;
        }

        /* FILTER ACTIONS TOOLBAR */
        .filter-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--lp-card-border);
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 38px;
            padding: 0 16px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            outline: none;
            transition: all 0.15s ease-in-out;
            white-space: nowrap;
            line-height: 1;
            box-sizing: border-box;
        }
        .btn-action svg {
            width: 16px !important;
            height: 16px !important;
            flex-shrink: 0;
        }
        .btn-indigo {
            background: #4f46e5;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }
        .btn-indigo:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        }
        .btn-emerald {
            background: #059669;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
        }
        .btn-emerald:hover {
            background: #047857;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.35);
        }
        .btn-slate {
            background: #1e293b;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(30, 41, 59, 0.2);
        }
        .btn-slate:hover {
            background: #0f172a;
            transform: translateY(-1px);
        }
        .dark .btn-slate, html.dark .btn-slate {
            background: #334155;
        }
        .dark .btn-slate:hover, html.dark .btn-slate:hover {
            background: #1e293b;
        }
        .btn-reset {
            background: #f1f5f9;
            color: #475569 !important;
            border: 1px solid #cbd5e1;
        }
        .btn-reset:hover {
            background: #e2e8f0;
            color: #0f172a !important;
        }
        .dark .btn-reset, html.dark .btn-reset {
            background: #1f2937;
            color: #cbd5e1 !important;
            border-color: #374151;
        }

        /* LOADING STATE */
        .loading-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--lp-card-border);
            font-size: 12px;
            font-weight: 600;
            color: #059669;
        }
        .dark .loading-bar, html.dark .loading-bar {
            color: #34d399;
        }
        .spinner {
            width: 15px;
            height: 15px;
            border: 2px solid rgba(5, 150, 105, 0.2);
            border-top-color: #059669;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        .dark .spinner, html.dark .spinner {
            border-color: rgba(52, 211, 153, 0.2);
            border-top-color: #34d399;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* KPI SUMMARY GRID */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 12px;
        }
        .kpi-card {
            border-radius: 14px;
            padding: 14px 10px;
            text-align: center;
            border: 1px solid transparent;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-sizing: border-box;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        }
        .kpi-title {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 4px 0;
        }
        .kpi-value {
            font-size: 24px;
            font-weight: 900;
            line-height: 1.1;
            margin: 4px 0;
        }
        .kpi-sub {
            font-size: 10px;
            font-weight: 600;
            margin: 0;
            opacity: 0.85;
        }

        /* KPI COLOR THEMES */
        .kpi-guru {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }
        .dark .kpi-guru, html.dark .kpi-guru {
            background: rgba(6, 95, 70, 0.25);
            border-color: rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .kpi-hero {
            background: linear-gradient(135deg, #0d9488 0%, #059669 100%);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.25);
        }
        .kpi-hero .kpi-title { color: #ccfbf1; }
        .kpi-hero .kpi-value { color: #ffffff; }
        .kpi-hero .kpi-sub { color: #ccfbf1; }

        .kpi-hadir {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }
        .dark .kpi-hadir, html.dark .kpi-hadir {
            background: rgba(22, 101, 52, 0.25);
            border-color: rgba(34, 197, 94, 0.3);
            color: #86efac;
        }

        .kpi-terlambat {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }
        .dark .kpi-terlambat, html.dark .kpi-terlambat {
            background: rgba(146, 64, 14, 0.25);
            border-color: rgba(245, 158, 11, 0.3);
            color: #fde68a;
        }

        .kpi-sakit {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e40af;
        }
        .dark .kpi-sakit, html.dark .kpi-sakit {
            background: rgba(30, 64, 175, 0.25);
            border-color: rgba(59, 130, 246, 0.3);
            color: #93c5fd;
        }

        .kpi-cuti {
            background: #faf5ff;
            border-color: #e9d5ff;
            color: #6b21a8;
        }
        .dark .kpi-cuti, html.dark .kpi-cuti {
            background: rgba(107, 33, 168, 0.25);
            border-color: rgba(168, 85, 247, 0.3);
            color: #d8b4fe;
        }

        .kpi-alpa {
            background: #fff1f2;
            border-color: #fecdd3;
            color: #9f1239;
        }
        .dark .kpi-alpa, html.dark .kpi-alpa {
            background: rgba(159, 18, 57, 0.25);
            border-color: rgba(244, 63, 94, 0.3);
            color: #fda4af;
        }

        /* MATRIX CONTAINER & TABLE */
        .matrix-container {
            background: var(--lp-card-bg);
            border: 1px solid var(--lp-card-border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        }
        .matrix-table-wrapper {
            overflow-x: auto;
            max-width: 100%;
        }
        .matrix-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 11.5px;
        }
        .matrix-table th, .matrix-table td {
            border-right: 1px solid var(--lp-table-border);
            border-bottom: 1px solid var(--lp-table-border);
            padding: 6px 7px;
            text-align: center;
            white-space: nowrap;
        }

        /* Sticky Columns */
        .sticky-col-no {
            position: sticky;
            left: 0;
            z-index: 10;
            background-color: var(--lp-card-bg);
            width: 44px;
            min-width: 44px;
            max-width: 44px;
            box-sizing: border-box;
        }
        .sticky-col-nama {
            position: sticky;
            left: 44px;
            z-index: 10;
            background-color: var(--lp-card-bg);
            text-align: left !important;
            min-width: 190px;
            max-width: 250px;
            box-sizing: border-box;
            box-shadow: 3px 0 6px -2px rgba(0,0,0,0.1);
        }
        th.sticky-col-no {
            position: sticky;
            left: 0;
            background-color: var(--lp-th-bg);
            z-index: 20;
        }
        th.sticky-col-nama {
            position: sticky;
            left: 44px;
            background-color: var(--lp-th-bg);
            z-index: 20;
        }
        .matrix-table tr:hover td.sticky-col-no,
        .matrix-table tr:hover td.sticky-col-nama {
            background-color: #f8fafc;
        }
        .dark .matrix-table tr:hover td.sticky-col-no,
        .dark .matrix-table tr:hover td.sticky-col-nama,
        html.dark .matrix-table tr:hover td.sticky-col-no,
        html.dark .matrix-table tr:hover td.sticky-col-nama {
            background-color: #1e293b;
        }

        /* TEACHER INFO IN CELL */
        .guru-nama {
            font-weight: 700;
            font-size: 12px;
            color: var(--lp-text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .guru-meta {
            font-size: 10px;
            color: var(--lp-text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
            margin-top: 2px;
        }
        .guru-status-tag {
            color: #059669;
            font-weight: 700;
        }
        .guru-shift-tag {
            color: #6366f1;
            font-weight: 600;
        }

        /* BADGES */
        .cell-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 800;
            cursor: default;
        }
        .badge-H { background: var(--lp-badge-h-bg); color: var(--lp-badge-h-text); }
        .badge-T { background: var(--lp-badge-t-bg); color: var(--lp-badge-t-text); }
        .badge-DL { background: var(--lp-badge-dl-bg); color: var(--lp-badge-dl-text); }
        .badge-S { background: var(--lp-badge-s-bg); color: var(--lp-badge-s-text); }
        .badge-I { background: var(--lp-badge-i-bg); color: var(--lp-badge-i-text); }
        .badge-C { background: var(--lp-badge-c-bg); color: var(--lp-badge-c-text); }
        .badge-A { background: var(--lp-badge-a-bg); color: var(--lp-badge-a-text); }
        .badge-L { background: var(--lp-badge-l-bg); color: var(--lp-badge-l-text); }
        .badge-empty { color: var(--lp-badge-empty); font-weight: normal; cursor: default; }

        /* LEGEND BAR */
        .matrix-legend {
            padding: 14px 18px;
            border-top: 1px solid var(--lp-card-border);
            background: var(--lp-th-sub-bg);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 11px;
            color: var(--lp-text-secondary);
        }
        .legend-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* PRINT ONLY STYLES (HIDDEN ON SCREEN) */
        .print-kop {
            display: none !important;
        }
        .print-signature {
            display: none !important;
        }

        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .no-print, .fi-topbar, .fi-sidebar, .filter-box, .kpi-grid {
                display: none !important;
            }
            .matrix-container {
                border: none !important;
                box-shadow: none !important;
            }
            .matrix-table {
                font-size: 9px !important;
            }
            .matrix-table th, .matrix-table td {
                padding: 3px 4px !important;
                border: 1px solid #000 !important;
            }
            .print-kop {
                display: block !important;
                text-align: center;
                border-bottom: 2px solid #000;
                padding-bottom: 8px;
                margin-bottom: 12px;
            }
            .print-signature {
                display: flex !important;
                justify-content: space-between;
                padding-top: 28px;
                font-size: 10px;
                text-align: center;
            }
        }
    </style>

    @php
        $matriks = $this->matriksLaporan;
    @endphp

    <div class="laporan-wrapper">
        <!-- FILTER BOX -->
        <div class="filter-box no-print">
            <div class="filter-grid">
                <!-- Search Teacher Name -->
                <div class="filter-col">
                    <label class="filter-label">Cari Nama / NIP Guru</label>
                    <div class="search-wrapper">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama atau NIP..." class="filter-input search-input" />
                        <span class="search-icon">
                            <x-filament::icon icon="heroicon-o-magnifying-glass" />
                        </span>
                    </div>
                </div>

                <!-- Select Month -->
                <div class="filter-col">
                    <label class="filter-label">Pilih Bulan</label>
                    <select wire:model.live="bulan" class="filter-input">
                        @foreach ($this->daftarBulan as $num => $nama)
                            <option value="{{ $num }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Year -->
                <div class="filter-col">
                    <label class="filter-label">Pilih Tahun</label>
                    <select wire:model.live="tahun" class="filter-input">
                        @foreach ($this->daftarTahun as $yr)
                            <option value="{{ $yr }}">{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Kepegawaian -->
                <div class="filter-col">
                    <label class="filter-label">Status Pegawai</label>
                    <select wire:model.live="statusKepegawaian" class="filter-input">
                        <option value="semua">Semua Status (PNS/PPPK/Honorer)</option>
                        <option value="pns">PNS </option>
                        <option value="pppk">PPPK</option>
                        <option value="non_pns">Non-PNS / Honorer</option>
                    </select>
                </div>

                <!-- Filter Shift Kerja -->
                <div class="filter-col">
                    <label class="filter-label">Shift Kerja</label>
                    <select wire:model.live="shiftId" class="filter-input">
                        <option value="">Semua Shift</option>
                        @foreach ($this->daftarShift as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }} ({{ \Carbon\Carbon::parse($s->jam_masuk)->format('H:i') }}-{{ \Carbon\Carbon::parse($s->jam_pulang)->format('H:i') }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons Toolbar -->
            <div class="filter-actions">
                <a href="{{ $this->urlRincian }}" class="btn-action btn-slate" title="Lihat jam masuk dan pulang setiap guru">
                    <x-filament::icon icon="heroicon-o-clock" />
                    <span>Rincian Jam Harian</span>
                </a>

                <a href="{{ $this->urlCetakKedinasan }}" target="_blank" class="btn-action btn-indigo" title="Cetak Dokumen Kedinasan Format A4 Landscape">
                    <x-filament::icon icon="heroicon-o-document-arrow-down" />
                    <span>Cetak Kedinasan (PDF)</span>
                </a>

                <button type="button" wire:click="exportExcel" class="btn-action btn-emerald" title="Unduh File Rekap Excel .xlsx">
                    <x-filament::icon icon="heroicon-o-arrow-down-tray" />
                    <span>Unduh Excel</span>
                </button>

                <a href="{{ route('school.live-display') }}" target="_blank" class="btn-action btn-slate" title="Buka Layar Monitor TV Lobi Sekolah">
                    <x-filament::icon icon="heroicon-o-tv" />
                    <span>TV Lobi</span>
                </a>

                @if ($search !== '' || $statusKepegawaian !== 'semua' || $shiftId !== null || $bulan !== (int) now()->month || $tahun !== (int) now()->year)
                    <button type="button" wire:click="resetFilter" class="btn-action btn-reset" title="Kembalikan Filter ke Standar">
                        <x-filament::icon icon="heroicon-o-arrow-path" />
                        <span>Reset Filter</span>
                    </button>
                @endif
            </div>

            <!-- Loading Indicator -->
            <div wire:loading.delay class="loading-bar">
                <div class="spinner"></div>
                <span>Memperbarui matriks rekapitulasi kehadiran...</span>
            </div>
        </div>

        <!-- KPI SUMMARY CARDS -->
        <div class="kpi-grid no-print">
            <div class="kpi-card kpi-guru">
                <p class="kpi-title">Guru Terdata</p>
                <p class="kpi-value">{{ $matriks['total_guru'] }}</p>
                <p class="kpi-sub">Orang Aktif</p>
            </div>
            <div class="kpi-card kpi-hero">
                <p class="kpi-title">Rata-rata Kehadiran</p>
                <p class="kpi-value">{{ $matriks['avg_persentase'] }}%</p>
                <p class="kpi-sub">{{ $matriks['hari_efektif'] }} Hari Kerja Efektif</p>
            </div>
            <div class="kpi-card kpi-hadir">
                <p class="kpi-title">Total Hadir</p>
                <p class="kpi-value">{{ $matriks['grand_total_hadir'] }}</p>
                <p class="kpi-sub">Presensi Tercatat</p>
            </div>
            <div class="kpi-card kpi-terlambat">
                <p class="kpi-title">Terlambat</p>
                <p class="kpi-value">{{ $matriks['grand_total_terlambat'] }}</p>
                <p class="kpi-sub">Kali Terlambat</p>
            </div>
            <div class="kpi-card kpi-sakit">
                <p class="kpi-title">Sakit / Izin</p>
                <p class="kpi-value">{{ $matriks['grand_total_sakit'] + $matriks['grand_total_izin'] }}</p>
                <p class="kpi-sub">Hari Izin Resmi</p>
            </div>
            <div class="kpi-card kpi-cuti">
                <p class="kpi-title">Cuti</p>
                <p class="kpi-value">{{ $matriks['grand_total_cuti'] }}</p>
                <p class="kpi-sub">Hari Cuti</p>
            </div>
            <div class="kpi-card kpi-alpa">
                <p class="kpi-title">Alpa</p>
                <p class="kpi-value">{{ $matriks['grand_total_alpa'] }}</p>
                <p class="kpi-sub">Hari Tanpa Keterangan</p>
            </div>
        </div>

        <!-- PRINT ONLY KOP SURAT (HIDDEN ON SCREEN) -->
        <div class="print-kop" style="display: none;">
            <h2 style="font-size: 15px; font-weight: bold; text-transform: uppercase; margin: 0;">{{ strtoupper($this->pengaturan->nama_sekolah ?: 'UPTD SPF SD INPRES RAPPOJAWA') }}</h2>
            <p style="font-size: 11px; margin: 2px 0 0 0;">{{ $this->pengaturan->alamat ?: 'Kota Makassar, Sulawesi Selatan' }} &bull; NPSN: {{ $this->pengaturan->npsn ?: '-' }}</p>
            <p style="font-size: 11px; font-weight: bold; text-transform: uppercase; margin-top: 4px; color: #065f46;">REKAPITULASI PRESENSI GURU & TENAGA KEPENDIDIKAN &bull; PERIODE: {{ strtoupper($matriks['periode_label']) }}</p>
        </div>

        <!-- MATRIX TABLE -->
        <div class="matrix-container">
            <div class="matrix-table-wrapper">
                <table class="matrix-table">
                    <thead>
                        <!-- Row 1: Headers -->
                        <tr style="background-color: var(--lp-th-bg); color: var(--lp-th-text);">
                            <th rowspan="2" class="sticky-col-no font-bold">No</th>
                            <th rowspan="2" class="sticky-col-nama font-bold">Nama Guru & NIP</th>
                            <th colspan="{{ $matriks['jumlah_hari'] }}" class="font-bold">Tanggal ({{ $matriks['periode_label'] }})</th>
                            <th colspan="8" class="font-bold">Rekapitulasi</th>
                        </tr>
                        <!-- Row 2: Sub-headers (Days & Symbols) -->
                        <tr style="background-color: var(--lp-th-sub-bg); color: var(--lp-th-sub-text);">
                            @for ($d = 1; $d <= $matriks['jumlah_hari']; $d++)
                                @php $info = $matriks['hari_info'][$d]; @endphp
                                <th style="{{ $info['is_libur'] ? 'background-color: var(--lp-libur-th-bg); color: var(--lp-libur-th-text);' : '' }}">
                                    <div class="font-bold">{{ $d }}</div>
                                    <div style="font-size: 9px; opacity: 0.8;">{{ $info['hari'] }}</div>
                                </th>
                            @endfor
                            <th style="background-color: var(--lp-total-h-bg); color: var(--lp-total-h-text); font-weight: 800;" title="Hadir Tepat Waktu">H</th>
                            <th style="background-color: var(--lp-total-t-bg); color: var(--lp-total-t-text); font-weight: 800;" title="Terlambat">T</th>
                            <th style="background-color: var(--lp-total-dl-bg); color: var(--lp-total-dl-text); font-weight: 800;" title="Tugas Luar / Dinas Luar (SPPD)">DL</th>
                            <th style="background-color: var(--lp-total-s-bg); color: var(--lp-total-s-text); font-weight: 800;" title="Sakit">S</th>
                            <th style="background-color: var(--lp-total-i-bg); color: var(--lp-total-i-text); font-weight: 800;" title="Izin">I</th>
                            <th style="background-color: var(--lp-total-c-bg); color: var(--lp-total-c-text); font-weight: 800;" title="Cuti">C</th>
                            <th style="background-color: var(--lp-total-a-bg); color: var(--lp-total-a-text); font-weight: 800;" title="Alpa">A</th>
                            <th style="background-color: var(--lp-total-pct-bg); color: var(--lp-total-pct-text); font-weight: 800;" title="Persentase Kehadiran">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($matriks['rows'] as $index => $row)
                            <tr>
                                <td class="sticky-col-no font-semibold">{{ $index + 1 }}</td>
                                <td class="sticky-col-nama">
                                    <div class="guru-nama">{{ $row['guru']->nama }}</div>
                                    <div class="guru-meta">
                                        <span>{{ $row['guru']->nip ? 'NIP: ' . $row['guru']->nip : ($row['guru']->nuptk ? 'NUPTK: ' . $row['guru']->nuptk : 'Non-NIP') }}</span>
                                        <span class="guru-status-tag">&bull; {{ strtoupper($row['guru']->status_kepegawaian) }}</span>
                                        @if ($row['guru']->shift)
                                            <span class="guru-shift-tag">&bull; {{ $row['guru']->shift->nama }}</span>
                                        @endif
                                    </div>
                                </td>

                                @for ($d = 1; $d <= $matriks['jumlah_hari']; $d++)
                                    @php
                                        $cell = $row['kehadiran'][$d];
                                        $info = $matriks['hari_info'][$d];
                                    @endphp
                                    <td style="{{ $info['is_libur'] ? 'background-color: var(--lp-libur-bg);' : '' }}" title="{{ $cell['tooltip'] ?? '' }}">
                                        @if ($cell['kode'] !== '-')
                                            <span class="cell-badge badge-{{ $cell['kode'] }}" title="{{ $cell['tooltip'] ?? '' }}">
                                                {{ $cell['kode'] }}
                                            </span>
                                        @else
                                            <span class="badge-empty" title="{{ $cell['tooltip'] ?? '' }}">&bull;</span>
                                        @endif
                                    </td>
                                @endfor

                                <td style="background-color: var(--lp-val-h-bg); font-weight: 700;">{{ $row['total_hadir'] }}</td>
                                <td style="background-color: var(--lp-val-t-bg); font-weight: 700; color: #d97706;">{{ $row['total_terlambat'] }}</td>
                                <td style="background-color: var(--lp-val-dl-bg); font-weight: 700; color: #0284c7;">{{ $row['total_dinas_luar'] }}</td>
                                <td style="background-color: var(--lp-val-s-bg); font-weight: 700; color: #2563eb;">{{ $row['total_sakit'] }}</td>
                                <td style="background-color: var(--lp-val-i-bg); font-weight: 700; color: #ca8a04;">{{ $row['total_izin'] }}</td>
                                <td style="background-color: var(--lp-val-c-bg); font-weight: 700; color: #9333ea;">{{ $row['total_cuti'] }}</td>
                                <td style="background-color: var(--lp-val-a-bg); font-weight: 700; color: #e11d48;">{{ $row['total_alpa'] }}</td>
                                <td style="background-color: var(--lp-val-pct-bg); font-weight: 800; color: #059669;">{{ $row['persentase'] }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $matriks['jumlah_hari'] + 10 }}" style="padding: 32px; text-align: center; color: var(--lp-text-muted);">
                                    Tidak ada data guru yang cocok dengan filter atau pencarian Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Legend / Keterangan Simbol -->
            <div class="matrix-legend">
                <div class="legend-group">
                    <span style="font-weight: 700; color: var(--lp-text-primary);">Keterangan:</span>
                    <span class="legend-item"><span class="cell-badge badge-H">H</span> Hadir Tepat Waktu</span>
                    <span class="legend-item"><span class="cell-badge badge-T">T</span> Hadir Terlambat</span>
                    <span class="legend-item"><span class="cell-badge badge-DL">DL</span> Dinas Luar</span>
                    <span class="legend-item"><span class="cell-badge badge-S">S</span> Sakit</span>
                    <span class="legend-item"><span class="cell-badge badge-I">I</span> Izin Dinas/Pribadi</span>
                    <span class="legend-item"><span class="cell-badge badge-C">C</span> Cuti</span>
                    <span class="legend-item"><span class="cell-badge badge-A">A</span> Alpa / Tanpa Keterangan</span>
                    <span class="legend-item"><span class="cell-badge badge-L">L</span> Libur / Akhir Pekan</span>
                </div>
                <div style="font-size: 11px; opacity: 0.85;">
                    Total Hari Kerja Efektif: <b>{{ $matriks['hari_efektif'] }} hari</b>
                </div>
            </div>
        </div>

        <!-- PRINT SIGNATURE AREA (Only visible when printing) -->
        <div class="print-signature" style="display: none;">
            <div style="display: flex; flex-direction: column; justify-content: space-between; height: 110px;">
                <p style="margin: 0;">Mengetahui,<br>Pengelola Kepegawaian / Operator,</p>
                <div>
                    <p style="font-weight: bold; text-decoration: underline; margin: 0;">{{ auth()->user()->name }}</p>
                    <p style="color: #64748b; margin: 2px 0 0 0;">Sistem Presensi Digital</p>
                </div>
            </div>
            <div style="display: flex; flex-direction: column; justify-content: space-between; height: 110px;">
                <p style="margin: 0;">Makassar, {{ now()->translatedFormat('d F Y') }}<br>Kepala Sekolah,</p>
                <div>
                    <p style="font-weight: bold; text-decoration: underline; margin: 0;">{{ $this->pengaturan->kepala_sekolah ?: 'Kepala Sekolah' }}</p>
                    <p style="color: #64748b; margin: 2px 0 0 0;">NIP. {{ $this->pengaturan->nip_kepala_sekolah ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
