<x-filament-panels::page>
    <style>
        .attendance-detail {
            --detail-ink: #172b31;
            --detail-muted: #687b80;
            --detail-line: #dce7e5;
            --detail-soft: #f4f8f7;
            --detail-surface: #ffffff;
            --detail-accent: #087f70;
            --detail-accent-soft: #e1f4ef;
            display: flex;
            flex-direction: column;
            gap: 20px;
            color: var(--detail-ink);
        }
        .dark .attendance-detail {
            --detail-ink: #e5eeeb;
            --detail-muted: #9aadaa;
            --detail-line: #31423f;
            --detail-soft: #1b2927;
            --detail-surface: #111b1a;
            --detail-accent: #59d3b2;
            --detail-accent-soft: #163a33;
        }
        .detail-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            padding: 2px 0 18px;
            border-bottom: 1px solid var(--detail-line);
        }
        .detail-eyebrow {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 8px;
            color: var(--detail-accent);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .detail-eyebrow::before {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--detail-accent);
            content: '';
        }
        .detail-summary {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 8px 16px;
            color: var(--detail-muted);
            font-size: 13px;
        }
        .detail-summary strong {
            color: var(--detail-ink);
            font-size: 19px;
            font-variant-numeric: tabular-nums;
        }
        .detail-period {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--detail-ink);
            font-weight: 700;
        }
        .detail-period svg { width: 16px; height: 16px; color: var(--detail-accent); }
        .detail-back {
            display: inline-flex;
            min-height: 40px;
            flex: 0 0 auto;
            align-items: center;
            gap: 8px;
            padding: 0 14px;
            border: 1px solid var(--detail-line);
            border-radius: 8px;
            color: var(--detail-ink);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: background .15s ease, border-color .15s ease;
        }
        .detail-back:hover { border-color: var(--detail-accent); background: var(--detail-accent-soft); }
        .detail-back svg { width: 16px; height: 16px; }
        .detail-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .detail-print {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            gap: 8px;
            padding: 0 14px;
            border: 1px solid #087f70;
            border-radius: 8px;
            color: #ffffff;
            background: #087f70;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: background .15s ease;
        }
        .detail-print:hover { color: #ffffff; background: #06685d; }
        .detail-print svg { width: 16px; height: 16px; }
        .print-only { display: none; }
        .detail-filters {
            padding: 16px;
            border: 1px solid var(--detail-line);
            border-radius: 10px;
            background: var(--detail-surface);
        }
        .detail-filter-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }
        .detail-filter-head h2 { margin: 0; color: var(--detail-ink); font-size: 14px; font-weight: 800; }
        .detail-filter-head p { margin: 0; color: var(--detail-muted); font-size: 12px; }
        .detail-filter-grid {
            display: grid;
            grid-template-columns: minmax(220px, 1.5fr) repeat(4, minmax(130px, 1fr));
            gap: 12px;
        }
        .detail-field { display: flex; min-width: 0; flex-direction: column; gap: 6px; }
        .detail-field span { color: var(--detail-muted); font-size: 11px; font-weight: 700; }
        .detail-control {
            width: 100%;
            height: 40px;
            padding: 0 11px;
            border: 1px solid var(--detail-line);
            border-radius: 7px;
            outline: none;
            color: var(--detail-ink);
            background: var(--detail-surface);
            font-size: 13px;
        }
        .detail-control:focus { border-color: var(--detail-accent); box-shadow: 0 0 0 3px color-mix(in srgb, var(--detail-accent) 16%, transparent); }
        .detail-table-shell { overflow: hidden; border: 1px solid var(--detail-line); border-radius: 10px; background: var(--detail-surface); }
        .detail-table-scroll { overflow-x: auto; }
        .detail-table { width: 100%; min-width: 1000px; border-collapse: collapse; text-align: left; }
        .detail-table thead { background: var(--detail-soft); }
        .detail-table th {
            padding: 12px 14px;
            border-bottom: 1px solid var(--detail-line);
            color: var(--detail-muted);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .detail-table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--detail-line);
            color: var(--detail-ink);
            font-size: 13px;
            vertical-align: middle;
        }
        .detail-table tbody tr:last-child td { border-bottom: 0; }
        .detail-table tbody tr:hover { background: color-mix(in srgb, var(--detail-soft) 68%, transparent); }
        .detail-date { min-width: 105px; white-space: nowrap; font-weight: 700; font-variant-numeric: tabular-nums; }
        .detail-person { min-width: 190px; }
        .detail-person strong { display: block; font-size: 13px; font-weight: 750; }
        .detail-person small, .detail-time small { display: block; margin-top: 4px; color: var(--detail-muted); font-size: 11px; }
        .detail-shift { min-width: 145px; color: var(--detail-muted) !important; }
        .detail-status {
            display: inline-flex;
            padding: 5px 8px;
            border: 1px solid transparent;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 750;
            white-space: nowrap;
        }
        .status-hadir { color: #087f70; background: #e1f4ef; border-color: #c3e9dd; }
        .status-dinas { color: #176a87; background: #e5f4f8; border-color: #c9e8f0; }
        .status-izin { color: #93620a; background: #fff5d9; border-color: #f3e6b8; }
        .status-sakit { color: #315f9a; background: #eaf1fb; border-color: #d4e1f4; }
        .status-cuti { color: #735493; background: #f2ebf8; border-color: #e4d7f0; }
        .status-alpa { color: #a63a42; background: #fcecee; border-color: #f3d5d8; }
        .dark .status-hadir { color: #91e4ce; background: #163a33; border-color: #285e51; }
        .dark .status-dinas { color: #9ad9eb; background: #163541; border-color: #285565; }
        .dark .status-izin { color: #f1d78e; background: #40351b; border-color: #655224; }
        .dark .status-sakit { color: #b6d0fa; background: #202f49; border-color: #34496c; }
        .dark .status-cuti { color: #d3b9ed; background: #352744; border-color: #503966; }
        .dark .status-alpa { color: #f3a6ac; background: #44272a; border-color: #69363b; }
        .detail-time { min-width: 105px; font-variant-numeric: tabular-nums; }
        .detail-time strong { font-size: 14px; font-weight: 800; }
        .detail-note { min-width: 180px; max-width: 300px; color: var(--detail-muted) !important; line-height: 1.45; }
        .time-late { color: #a76a0b !important; }
        .time-normal { color: var(--detail-ink) !important; }
        .detail-empty { padding: 48px 20px !important; color: var(--detail-muted) !important; text-align: center; }
        .detail-table-footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border-top: 1px solid var(--detail-line);
            color: var(--detail-muted);
            font-size: 12px;
        }
        .detail-table-footer nav { margin-left: auto; }
        .detail-pagination { display: flex; align-items: center; gap: 6px; }
        .detail-pagination button {
            min-height: 32px;
            padding: 0 10px;
            border: 1px solid var(--detail-line);
            border-radius: 6px;
            color: var(--detail-ink);
            background: var(--detail-surface);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }
        .detail-pagination button:hover:not(:disabled) { border-color: var(--detail-accent); background: var(--detail-accent-soft); }
        .detail-pagination button:disabled { opacity: .45; cursor: not-allowed; }
        .detail-pagination span { min-width: 82px; color: var(--detail-muted); text-align: center; font-size: 12px; font-variant-numeric: tabular-nums; }
        @page { size: landscape; margin: 10mm; }
        @media print {
            html, body {
                overflow: visible !important;
                height: auto !important;
                min-height: 0 !important;
                background: #ffffff !important;
            }
            .fi-layout, .fi-main-ctn, .fi-main, .fi-page, .fi-page-content, .fi-page-header {
                visibility: visible !important;
                opacity: 1 !important;
                transform: none !important;
                animation: none !important;
                transition: none !important;
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
            .fi-sidebar, .fi-topbar, .fi-header, .fi-breadcrumbs, .fi-sidebar-close-overlay,
            .detail-filters, .detail-actions, .detail-table-footer { display: none !important; }
            .attendance-detail { gap: 10px; color: #111827; }
            .detail-heading { display: block; padding: 0 0 10px; border-bottom: 2px solid #111827; }
            .detail-eyebrow { margin-bottom: 4px; color: #111827; }
            .detail-summary { color: #374151; }
            .detail-summary strong, .detail-period { color: #111827; }
            .detail-summary::before { display: block; margin-bottom: 4px; color: #111827; content: 'LAPORAN RINCIAN PRESENSI HARIAN'; font-size: 16px; font-weight: 800; }
            .detail-table-shell { overflow: visible; border: 0; border-radius: 0; }
            .detail-table-scroll { overflow: visible; }
            .detail-table { min-width: 0; font-size: 9px; }
            .detail-table thead { display: table-header-group; background: #f3f4f6; }
            .detail-table tr { break-inside: avoid; }
            .detail-table th, .detail-table td { padding: 5px 7px; border: 1px solid #d1d5db; color: #111827 !important; font-size: 9px; }
            .detail-table th { color: #374151 !important; }
            .detail-table tbody tr:hover { background: transparent; }
            .detail-status { border-color: #d1d5db !important; color: #111827 !important; background: #f3f4f6 !important; }
            .detail-time small, .detail-person small { color: #4b5563 !important; }
            .detail-note { max-width: 240px; }
            .print-only { display: block !important; }
        }
        @media (max-width: 1100px) {
            .detail-filter-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .detail-field:first-child { grid-column: span 3; }
        }
        @media (max-width: 700px) {
            .attendance-detail { gap: 16px; }
            .detail-heading { align-items: flex-start; flex-direction: column; }
            .detail-back { align-self: flex-start; }
            .detail-filter-head { align-items: flex-start; flex-direction: column; }
            .detail-filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
            .detail-field:first-child { grid-column: span 2; }
            .detail-table-scroll { overflow: visible; }
            .detail-table { min-width: 0; }
            .detail-table thead { display: none; }
            .detail-table, .detail-table tbody, .detail-table tr, .detail-table td { display: block; width: 100%; }
            .detail-table tbody tr { padding: 10px 14px; border-bottom: 1px solid var(--detail-line); }
            .detail-table tbody tr:last-child { border-bottom: 0; }
            .detail-table td { display: grid; grid-template-columns: 105px minmax(0, 1fr); gap: 10px; padding: 6px 0; border: 0; }
            .detail-table td::before { color: var(--detail-muted); content: attr(data-label); font-size: 10px; font-weight: 800; text-transform: uppercase; }
            .detail-table td.detail-person { display: block; padding: 8px 0 10px; border-bottom: 1px solid var(--detail-line); }
            .detail-table td.detail-person::before { display: none; }
            .detail-table td.detail-note { max-width: none; }
            .detail-empty { display: block !important; }
            .detail-table-footer { align-items: flex-start; flex-direction: column; }
            .detail-table-footer nav { margin-left: 0; }
        }
        @media (max-width: 380px) {
            .detail-filter-grid { grid-template-columns: 1fr; }
            .detail-field:first-child { grid-column: auto; }
            .detail-table td { grid-template-columns: 86px minmax(0, 1fr); }
        }
    </style>

    <div class="attendance-detail">
        <header class="detail-heading">
            <div>
                <p class="detail-eyebrow">Rekap kehadiran harian</p>
                <div class="detail-summary">
                    <span class="detail-period">
                        <x-filament::icon icon="heroicon-o-calendar-days" />
                        {{ $this->daftarBulan[$bulan] }} {{ $tahun }}
                    </span>
                    <span><strong>{{ number_format($this->totalRincian, 0, ',', '.') }}</strong> catatan presensi</span>
                </div>
            </div>
            <div class="detail-actions">
                @unless ($cetakSemua)
                    <a href="{{ $this->urlCetak }}" target="_blank" rel="noopener" class="detail-print">
                        <x-filament::icon icon="heroicon-o-printer" />
                        Cetak / Simpan PDF
                    </a>
                @endunless
                <a href="{{ $this->urlLaporan }}" class="detail-back">
                    <x-filament::icon icon="heroicon-o-arrow-left" />
                    Kembali ke Matriks
                </a>
            </div>
        </header>

        <section class="detail-filters" aria-label="Filter rincian presensi">
            <div class="detail-filter-head">
                <h2>Filter laporan</h2>
                <p>Perubahan filter langsung memperbarui rincian.</p>
            </div>
            <div class="detail-filter-grid">
                <label class="detail-field">
                    <span>Nama, NIP, atau jabatan</span>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari guru..." class="detail-control" />
                </label>
                <label class="detail-field">
                    <span>Bulan</span>
                    <select wire:model.live="bulan" class="detail-control">
                        @foreach ($this->daftarBulan as $nomorBulan => $namaBulan)
                            <option value="{{ $nomorBulan }}">{{ $namaBulan }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="detail-field">
                    <span>Tahun</span>
                    <select wire:model.live="tahun" class="detail-control">
                        @foreach ($this->daftarTahun as $tahunPilihan)
                            <option value="{{ $tahunPilihan }}">{{ $tahunPilihan }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="detail-field">
                    <span>Status pegawai</span>
                    <select wire:model.live="statusKepegawaian" class="detail-control">
                        <option value="semua">Semua status</option>
                        <option value="pns">PNS</option>
                        <option value="pppk">PPPK</option>
                        <option value="non_pns">Non-PNS / Honorer</option>
                    </select>
                </label>
                <label class="detail-field">
                    <span>Shift kerja</span>
                    <select wire:model.live="shiftId" class="detail-control">
                        <option value="">Semua shift</option>
                        @foreach ($this->daftarShift as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->nama }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </section>

        <section class="detail-table-shell" aria-label="Daftar rincian presensi">
            <div class="detail-table-scroll">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Guru</th>
                            <th>Shift</th>
                            <th>Status</th>
                            <th>Jam masuk</th>
                            <th>Jam pulang</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->daftarRincian as $presensi)
                            @php
                                $statusClass = match ($presensi->status_kehadiran) {
                                    'hadir' => 'status-hadir',
                                    'dinas_luar' => 'status-dinas',
                                    'sakit' => 'status-sakit',
                                    'izin' => 'status-izin',
                                    'cuti' => 'status-cuti',
                                    'alpa' => 'status-alpa',
                                    default => '',
                                };
                            @endphp
                            <tr class="detail-row">
                                <td class="detail-date" data-label="Tanggal">{{ $presensi->tanggal?->locale('id')->translatedFormat('d M Y') }}</td>
                                <td class="detail-person" data-label="Guru">
                                    <strong>{{ $presensi->guru?->nama ?? 'Guru tidak ditemukan' }}</strong>
                                    <small>{{ $presensi->guru?->nip ? 'NIP: '.$presensi->guru->nip : ($presensi->guru?->nuptk ? 'NUPTK: '.$presensi->guru->nuptk : 'Non-NIP') }}</small>
                                </td>
                                <td class="detail-shift" data-label="Shift">{{ $presensi->shift?->nama ?? '-' }}</td>
                                <td data-label="Status">
                                    <span class="detail-status {{ $statusClass }}">{{ $this->getStatusKehadiranLabel($presensi) }}</span>
                                </td>
                                <td class="detail-time" data-label="Jam masuk">
                                    <strong class="{{ $presensi->status_masuk === 'terlambat' ? 'time-late' : 'time-normal' }}">{{ $presensi->jam_masuk?->format('H:i') ?? '-' }}</strong>
                                    @if ($presensi->status_masuk)
                                        <small>{{ str($presensi->status_masuk)->replace('_', ' ')->title() }}</small>
                                    @endif
                                </td>
                                <td class="detail-time" data-label="Jam pulang">
                                    <strong class="time-normal">{{ $presensi->jam_pulang?->format('H:i') ?? '-' }}</strong>
                                    @if ($presensi->status_pulang)
                                        <small>{{ str($presensi->status_pulang)->replace('_', ' ')->title() }}</small>
                                    @endif
                                </td>
                                <td class="detail-note" data-label="Keterangan">{{ $presensi->keterangan ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="detail-empty">Tidak ada catatan presensi yang sesuai dengan filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @unless ($cetakSemua)
                <footer class="detail-table-footer">
                    <span>
                        @if ($this->totalRincian > 0)
                            Menampilkan {{ $this->daftarRincian->firstItem() }}–{{ $this->daftarRincian->lastItem() }} dari {{ number_format($this->totalRincian, 0, ',', '.') }} catatan
                        @else
                            Belum ada catatan untuk filter ini
                        @endif
                    </span>
                    @if ($this->daftarRincian->hasPages())
                        <nav class="detail-pagination" aria-label="Navigasi halaman rincian presensi">
                            <button type="button" wire:click="previousPage" @disabled($this->daftarRincian->onFirstPage())>Sebelumnya</button>
                            <span>Halaman {{ $this->daftarRincian->currentPage() }} dari {{ $this->daftarRincian->lastPage() }}</span>
                            <button type="button" wire:click="nextPage" @disabled(! $this->daftarRincian->hasMorePages())>Berikutnya</button>
                        </nav>
                    @endif
                </footer>
            @endunless
        </section>
    </div>

    @if ($cetakSemua)
        <script>
            window.addEventListener('load', () => {
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        window.setTimeout(() => window.print(), 250);
                    });
                });
            }, { once: true });
        </script>
    @endif
</x-filament-panels::page>
