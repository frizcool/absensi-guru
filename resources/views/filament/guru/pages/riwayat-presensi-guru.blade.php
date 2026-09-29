<x-filament-panels::page>
    <style>
        .rp-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            width: 100%;
        }

        /* Filter Row */
        .rp-filter-row {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .rp-filter-row {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        .rp-select-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .rp-select-item label {
            display: block;
            font-size: 0.6875rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .rp-select-input {
            font-size: 0.8125rem;
            font-weight: 700;
            padding: 0.45rem 0.85rem;
            border-radius: 0.6rem;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
        }

        .dark .rp-select-input {
            background: #1e293b;
            border-color: #334155;
            color: #f8fafc;
        }

        .rp-btn-view-toggle {
            display: inline-flex;
            background: #f1f5f9;
            border-radius: 0.65rem;
            padding: 0.25rem;
            border: 1px solid #e2e8f0;
        }

        .dark .rp-btn-view-toggle {
            background: #0f172a;
            border-color: #1f2937;
        }

        .rp-btn-tab {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            border: none;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
        }

        .rp-btn-tab.active {
            background: #ffffff;
            color: #059669;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .dark .rp-btn-tab.active {
            background: #1e293b;
            color: #34d399;
        }

        .rp-btn-print {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.95rem;
            border-radius: 0.65rem;
            background: #059669;
            color: #ffffff !important;
            font-size: 0.75rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
            transition: all 0.2s;
        }

        .rp-btn-print:hover {
            background: #047857;
            transform: translateY(-1px);
        }

        /* KPI Summary Grid */
        .rp-kpi-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        @media (min-width: 640px) {
            .rp-kpi-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .rp-kpi-grid {
                grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            }
        }

        .rp-kpi-card {
            border-radius: 1rem;
            padding: 0.85rem 0.75rem;
            text-align: center;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .rp-kpi-card-title {
            font-size: 0.6875rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin: 0;
        }

        .rp-kpi-card-val {
            font-size: 1.5rem;
            font-weight: 900;
            line-height: 1.2;
            margin: 0.25rem 0 0.1rem 0;
        }

        .rp-kpi-card-sub {
            font-size: 0.625rem;
            margin: 0;
            opacity: 0.8;
        }

        /* Calendar Grid */
        .rp-cal-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            padding-top: 0.5rem;
        }

        @media (min-width: 640px) {
            .rp-cal-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 768px) {
            .rp-cal-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .rp-cal-grid {
                grid-template-columns: repeat(7, 1fr);
            }
        }

        .rp-cal-cell {
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            padding: 0.75rem;
            min-height: 105px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .dark .rp-cal-cell {
            border-color: #1e293b;
        }

        .rp-cal-cell:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
        }

        .rp-badge-tag {
            display: inline-block;
            font-size: 0.5625rem;
            font-weight: 800;
            text-transform: uppercase;
            padding: 0.15rem 0.45rem;
            border-radius: 0.35rem;
            letter-spacing: 0.04em;
        }

        /* Table Log */
        .rp-table-wrapper {
            overflow-x: auto;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
        }

        .dark .rp-table-wrapper {
            border-color: #1f2937;
        }

        .rp-table {
            width: 100%;
            font-size: 0.75rem;
            text-align: left;
            border-collapse: collapse;
        }

        .rp-thead {
            background: #f8fafc;
            color: #475569;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.6875rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .dark .rp-thead {
            background: #0f172a;
            color: #94a3b8;
            border-color: #1f2937;
        }

        .rp-th, .rp-td {
            padding: 0.75rem 0.85rem;
        }

        .rp-tr {
            border-bottom: 1px solid #f1f5f9;
        }

        .dark .rp-tr {
            border-color: #1e293b;
        }
    </style>

    <div class="rp-container" x-data="{ showPrintModal: false }">
        <!-- Filter & View Mode Controls -->
        <x-filament::section>
            <div class="rp-filter-row">
                <div class="rp-select-group">
                    <div class="rp-select-item">
                        <label>Bulan</label>
                        <select wire:model.live="bulan" class="rp-select-input">
                            @foreach ($this->daftarBulan as $num => $nama)
                                <option value="{{ $num }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="rp-select-item">
                        <label>Tahun</label>
                        <select wire:model.live="tahun" class="rp-select-input">
                            @foreach ($this->daftarTahun as $yr)
                                <option value="{{ $yr }}">{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="padding-top: 1.25rem; font-size: 0.75rem; color: #64748b;">
                        Periode: <b style="color: #059669;">{{ $this->daftarBulan[$this->bulan] }} {{ $this->tahun }}</b>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                    <!-- Toggle View Mode Buttons -->
                    <div class="rp-btn-view-toggle">
                        <button type="button" wire:click="setViewMode('grid')" class="rp-btn-tab {{ $viewMode === 'grid' ? 'active' : '' }}">
                            <x-filament::icon icon="heroicon-o-squares-2x2" style="width: 0.95rem; height: 0.95rem;" />
                            <span>Kalender</span>
                        </button>
                        <button type="button" wire:click="setViewMode('table')" class="rp-btn-tab {{ $viewMode === 'table' ? 'active' : '' }}">
                            <x-filament::icon icon="heroicon-o-list-bullet" style="width: 0.95rem; height: 0.95rem;" />
                            <span>Tabel Log</span>
                        </button>
                    </div>

                    <!-- Print Slip Button -->
                    <button type="button" @click="showPrintModal = true" class="rp-btn-print">
                        <x-filament::icon icon="heroicon-o-printer" style="width: 1rem; height: 1rem;" />
                        <span>Cetak Slip Rekap</span>
                    </button>
                </div>
            </div>
        </x-filament::section>

        <!-- KPI Summary Cards -->
        <div class="rp-kpi-grid">
            <div class="rp-kpi-card" style="background: #ecfdf5; border-color: #a7f3d0; color: #065f46;">
                <p class="rp-kpi-card-title">Hadir</p>
                <p class="rp-kpi-card-val">{{ $this->rekapBulanan['total_hadir'] }}</p>
                <p class="rp-kpi-card-sub">Hari</p>
            </div>
            <div class="rp-kpi-card" style="background: #f0fdfa; border-color: #99f6e4; color: #0f766e;">
                <p class="rp-kpi-card-title">Tepat Waktu</p>
                <p class="rp-kpi-card-val">{{ $this->rekapBulanan['total_tepat_waktu'] }}</p>
                <p class="rp-kpi-card-sub">Hari</p>
            </div>
            <div class="rp-kpi-card" style="background: #fffbeb; border-color: #fde68a; color: #92400e;">
                <p class="rp-kpi-card-title">Terlambat</p>
                <p class="rp-kpi-card-val">{{ $this->rekapBulanan['total_terlambat'] }}</p>
                <p class="rp-kpi-card-sub">Hari</p>
            </div>
            <div class="rp-kpi-card" style="background: #f0fdf4; border-color: #99f6e4; color: #0d9488;">
                <p class="rp-kpi-card-title">Dinas Luar</p>
                <p class="rp-kpi-card-val">{{ $this->rekapBulanan['total_dinas_luar'] ?? 0 }}</p>
                <p class="rp-kpi-card-sub">Hari</p>
            </div>
            <div class="rp-kpi-card" style="background: #eff6ff; border-color: #bfdbfe; color: #1e40af;">
                <p class="rp-kpi-card-title">Sakit</p>
                <p class="rp-kpi-card-val">{{ $this->rekapBulanan['total_sakit'] }}</p>
                <p class="rp-kpi-card-sub">Hari</p>
            </div>
            <div class="rp-kpi-card" style="background: #fefce8; border-color: #fef08a; color: #854d0e;">
                <p class="rp-kpi-card-title">Izin</p>
                <p class="rp-kpi-card-val">{{ $this->rekapBulanan['total_izin'] }}</p>
                <p class="rp-kpi-card-sub">Hari</p>
            </div>
            <div class="rp-kpi-card" style="background: #faf5ff; border-color: #e9d5ff; color: #6b21a8;">
                <p class="rp-kpi-card-title">Cuti</p>
                <p class="rp-kpi-card-val">{{ $this->rekapBulanan['total_cuti'] }}</p>
                <p class="rp-kpi-card-sub">Hari</p>
            </div>
            <div class="rp-kpi-card" style="background: #fff1f2; border-color: #fecdd3; color: #9f1239;">
                <p class="rp-kpi-card-title">Alpa</p>
                <p class="rp-kpi-card-val">{{ $this->rekapBulanan['total_alpa'] }}</p>
                <p class="rp-kpi-card-sub">Hari</p>
            </div>
            <div class="rp-kpi-card" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); color: #ffffff; border-color: transparent; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);">
                <p class="rp-kpi-card-title" style="color: #a7f3d0;">% Kehadiran</p>
                <p class="rp-kpi-card-val" style="color: #ffffff;">{{ $this->rekapBulanan['persentase'] }}%</p>
                <p class="rp-kpi-card-sub" style="color: #d1fae5;">{{ $this->rekapBulanan['hari_efektif'] }} Hari Kerja</p>
            </div>
        </div>

        @if ($viewMode === 'grid')
            <!-- KALENDER VISUAL GRID VIEW -->
            <x-filament::section>
                <x-slot name="heading">
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <x-filament::icon icon="heroicon-o-calendar" style="width: 1.15rem; height: 1.15rem; color: #059669;" />
                            <span style="font-weight: 800; font-size: 0.95rem;">Kalender Presensi Mandiri ({{ $this->daftarBulan[$this->bulan] }} {{ $this->tahun }})</span>
                        </div>
                        <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 400;">Klik kotak tanggal untuk melihat detail</span>
                    </div>
                </x-slot>

                <div class="rp-cal-grid">
                    @foreach ($this->rekapBulanan['daftar_hari'] as $day => $item)
                        @php
                            $p = $item['presensi'];
                            $isLibur = $item['is_libur'];
                            $status = $p?->status_kehadiran;
                            $masukStatus = $p?->status_masuk;

                            $bgColor = 'background: #f8fafc; border-color: #e2e8f0; color: #475569;';
                            $badgeColor = 'background: #e2e8f0; color: #334155;';
                            $badgeText = '-';

                            if ($isLibur) {
                                $bgColor = 'background: #fff1f2; border-color: #fecdd3; color: #9f1239;';
                                $badgeColor = 'background: #ffe4e6; color: #be123c;';
                                $badgeText = 'LIBUR';
                            } elseif ($status === 'hadir') {
                                if ($masukStatus === 'terlambat') {
                                    $bgColor = 'background: #fffbeb; border-color: #fde68a; color: #92400e;';
                                    $badgeColor = 'background: #fef3c7; color: #b45309;';
                                    $badgeText = 'TERLAMBAT';
                                } else {
                                    $bgColor = 'background: #ecfdf5; border-color: #a7f3d0; color: #065f46;';
                                    $badgeColor = 'background: #d1fae5; color: #047857;';
                                    $badgeText = 'TEPAT WAKTU';
                                }
                            } elseif ($status === 'dinas_luar') {
                                $bgColor = 'background: #f0fdf4; border-color: #99f6e4; color: #0d9488;';
                                $badgeColor = 'background: #ccfbf1; color: #0f766e;';
                                $badgeText = 'DINAS LUAR';
                            } elseif ($status === 'sakit') {
                                $bgColor = 'background: #eff6ff; border-color: #bfdbfe; color: #1e40af;';
                                $badgeColor = 'background: #dbeafe; color: #1d4ed8;';
                                $badgeText = 'SAKIT';
                            } elseif ($status === 'izin') {
                                $bgColor = 'background: #fefce8; border-color: #fef08a; color: #854d0e;';
                                $badgeColor = 'background: #fef9c3; color: #a16207;';
                                $badgeText = 'IZIN';
                            } elseif ($status === 'cuti') {
                                $bgColor = 'background: #faf5ff; border-color: #e9d5ff; color: #6b21a8;';
                                $badgeColor = 'background: #f3e8ff; color: #7e22ce;';
                                $badgeText = 'CUTI';
                            } elseif ($status === 'alpa') {
                                $bgColor = 'background: #fff1f2; border-color: #fecdd3; color: #9f1239;';
                                $badgeColor = 'background: #ffe4e6; color: #be123c;';
                                $badgeText = 'ALPA';
                            }
                        @endphp

                        <div wire:click="bukaDetail({{ $day }})" class="rp-cal-cell" style="{{ $bgColor }}">
                            <!-- Top Date & Day -->
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 1.15rem; font-weight: 900; {{ $isLibur ? 'color:#e11d48;' : '' }}">
                                    {{ $day }}
                                </span>
                                <span style="font-size: 0.625rem; font-weight: 800; text-transform: uppercase; opacity: 0.7;">
                                    {{ substr($item['hari'], 0, 3) }}
                                </span>
                            </div>

                            <!-- Middle Badge -->
                            <div style="margin: 0.35rem 0;">
                                <span class="rp-badge-tag" style="{{ $badgeColor }}">
                                    {{ $badgeText }}
                                </span>
                            </div>

                            <!-- Bottom Timestamps -->
                            <div style="font-size: 0.625rem; font-weight: 700; display: flex; flex-direction: column; gap: 0.15rem;">
                                @if ($p?->jam_masuk)
                                    <div style="display: flex; justify-content: space-between;">
                                        <span style="opacity: 0.7;">In:</span>
                                        <span style="{{ $masukStatus === 'terlambat' ? 'color:#e11d48;' : 'color:#059669;' }}">{{ $p->jam_masuk->format('H:i') }}</span>
                                    </div>
                                @endif
                                @if ($p?->jam_pulang)
                                    <div style="display: flex; justify-content: space-between;">
                                        <span style="opacity: 0.7;">Out:</span>
                                        <span style="color: #0f766e;">{{ $p->jam_pulang->format('H:i') }}</span>
                                    </div>
                                @elseif ($p?->jam_masuk)
                                    <div style="display: flex; justify-content: space-between; opacity: 0.5;">
                                        <span>Out:</span>
                                        <span>-</span>
                                    </div>
                                @endif
                                @if ($isLibur && $item['info_libur'])
                                    <div style="font-size: 0.5625rem; color: #e11d48; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item['info_libur'] }}">
                                        {{ $item['info_libur'] }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @else
            <!-- TABEL LOG DETAIL VIEW -->
            <x-filament::section>
                <x-slot name="heading">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <x-filament::icon icon="heroicon-o-list-bullet" style="width: 1.15rem; height: 1.15rem; color: #059669;" />
                        <span style="font-weight: 800; font-size: 0.95rem;">Log Rincian Kehadiran Harian ({{ $this->daftarBulan[$this->bulan] }} {{ $this->tahun }})</span>
                    </div>
                </x-slot>

                <div class="rp-table-wrapper">
                    <table class="rp-table">
                        <thead class="rp-thead">
                            <tr>
                                <th class="rp-th">Tanggal</th>
                                <th class="rp-th">Hari</th>
                                <th class="rp-th" style="text-align: center;">Selfie In</th>
                                <th class="rp-th">Jam Masuk</th>
                                <th class="rp-th" style="text-align: center;">Selfie Out</th>
                                <th class="rp-th">Jam Pulang</th>
                                <th class="rp-th">Status</th>
                                <th class="rp-th">Keterangan / Lokasi</th>
                                <th class="rp-th" style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->rekapBulanan['daftar_hari'] as $day => $item)
                                @php
                                    $p = $item['presensi'];
                                    $isLibur = $item['is_libur'];
                                @endphp
                                <tr class="rp-tr" style="{{ $isLibur ? 'background: rgba(254, 205, 211, 0.2);' : '' }}">
                                    <td class="rp-td" style="font-weight: 800;">
                                        {{ $item['tanggal']->format('d/m/Y') }}
                                    </td>
                                    <td class="rp-td" style="font-weight: 600; {{ $isLibur ? 'color: #e11d48;' : '' }}">
                                        {{ $item['hari'] }}
                                    </td>
                                    <td class="rp-td" style="text-align: center;">
                                        @if ($p?->foto_masuk_url)
                                            <img src="{{ $p->foto_masuk_url }}" alt="In" style="width: 2rem; height: 2rem; border-radius: 9999px; object-fit: cover; margin: 0 auto; border: 1px solid #10b981;" />
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </td>
                                    <td class="rp-td">
                                        @if ($p?->jam_masuk)
                                            <span style="font-weight: 800;">{{ $p->jam_masuk->format('H:i') }}</span>
                                            <span style="font-size: 0.625rem; font-weight: 700; margin-left: 0.25rem; {{ $p->status_masuk === 'terlambat' ? 'color:#e11d48;' : 'color:#059669;' }}">
                                                ({{ ucfirst(str_replace('_', ' ', $p->status_masuk)) }})
                                            </span>
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </td>
                                    <td class="rp-td" style="text-align: center;">
                                        @if ($p?->foto_pulang_url)
                                            <img src="{{ $p->foto_pulang_url }}" alt="Out" style="width: 2rem; height: 2rem; border-radius: 9999px; object-fit: cover; margin: 0 auto; border: 1px solid #14b8a6;" />
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </td>
                                    <td class="rp-td">
                                        @if ($p?->jam_pulang)
                                            <span style="font-weight: 800;">{{ $p->jam_pulang->format('H:i') }}</span>
                                            <span style="font-size: 0.625rem; font-weight: 700; margin-left: 0.25rem; {{ $p->status_pulang === 'pulang_cepat' ? 'color:#d97706;' : 'color:#059669;' }}">
                                                ({{ ucfirst(str_replace('_', ' ', $p->status_pulang)) }})
                                            </span>
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </td>
                                    <td class="rp-td">
                                        @if ($p)
                                            <span class="rp-badge-tag" style="{{ $p->status_kehadiran === 'hadir' ? 'background:#d1fae5; color:#065f46;' : ($p->status_kehadiran === 'dinas_luar' ? 'background:#ccfbf1; color:#0f766e;' : ($p->status_kehadiran === 'alpa' ? 'background:#ffe4e6; color:#9f1239;' : 'background:#fef3c7; color:#92400e;')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $p->status_kehadiran)) }}
                                            </span>
                                        @elseif ($isLibur)
                                            <span class="rp-badge-tag" style="background: #ffe4e6; color: #9f1239;">
                                                Libur
                                            </span>
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </td>
                                    <td class="rp-td" style="color: #64748b; max-width: 14rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $item['info_libur'] ?: ($p?->keterangan ?: '-') }}
                                    </td>
                                    <td class="rp-td" style="text-align: center;">
                                        <button type="button" wire:click="bukaDetail({{ $day }})" style="color: #059669; font-weight: 700; font-size: 0.75rem; text-decoration: underline; background: none; border: none; cursor: pointer;">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        <!-- MODAL POPUP DETAIL HARI -->
        @if ($selectedHariIndex !== null && $this->selectedDetail)
            @php
                $detail = $this->selectedDetail;
                $pres = $detail['presensi'];
            @endphp
            <div style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px); padding: 1rem;" wire:click.self="tutupDetail">
                <div style="background: var(--pg-card-bg, #ffffff); border: 1px solid #e2e8f0; border-radius: 1.5rem; padding: 1.5rem; width: 100%; max-width: 32rem; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35); display: flex; flex-direction: column; gap: 1.25rem;" @click.stop>
                    <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
                        <div>
                            <span style="font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; color: #059669; letter-spacing: 0.05em;">Rincian Kehadiran</span>
                            <h3 style="font-size: 1.15rem; font-weight: 900; margin: 0.15rem 0 0 0; color: #0f172a;">
                                {{ $detail['hari'] }}, {{ $detail['tanggal']->format('d F Y') }}
                            </h3>
                        </div>
                        <button type="button" wire:click="tutupDetail" style="width: 2rem; height: 2rem; border-radius: 9999px; background: #f1f5f9; border: 1px solid #cbd5e1; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 800;">✕</button>
                    </div>

                    @if ($pres)
                        <!-- Selfie Photos Grid -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <!-- In Selfie -->
                            <div style="border-radius: 1rem; border: 1px solid #e2e8f0; padding: 0.75rem; background: #f8fafc; text-align: center; display: flex; flex-direction: column; gap: 0.45rem;">
                                <div style="font-size: 0.6875rem; font-weight: 800; color: #059669;">
                                    📸 Selfie Check-In
                                </div>
                                @if ($pres->foto_masuk_url)
                                    <img src="{{ $pres->foto_masuk_url }}" alt="Foto Masuk" style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 0.75rem; border: 1px solid #10b981;" />
                                @else
                                    <div style="width: 100%; aspect-ratio: 1; border-radius: 0.75rem; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: #94a3b8;">
                                        Tanpa Foto
                                    </div>
                                @endif
                                <div style="font-size: 0.8125rem; font-weight: 800; color: #0f172a;">
                                    {{ $pres->jam_masuk ? $pres->jam_masuk->format('H:i') . ' WITA' : '-' }}
                                </div>
                                <div style="font-size: 0.625rem; font-weight: 800; {{ $pres->status_masuk === 'terlambat' ? 'color:#e11d48;' : 'color:#059669;' }}">
                                    {{ ucfirst(str_replace('_', ' ', $pres->status_masuk ?? '-')) }}
                                </div>
                            </div>

                            <!-- Out Selfie -->
                            <div style="border-radius: 1rem; border: 1px solid #e2e8f0; padding: 0.75rem; background: #f8fafc; text-align: center; display: flex; flex-direction: column; gap: 0.45rem;">
                                <div style="font-size: 0.6875rem; font-weight: 800; color: #0f766e;">
                                    📸 Selfie Check-Out
                                </div>
                                @if ($pres->foto_pulang_url)
                                    <img src="{{ $pres->foto_pulang_url }}" alt="Foto Pulang" style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 0.75rem; border: 1px solid #14b8a6;" />
                                @else
                                    <div style="width: 100%; aspect-ratio: 1; border-radius: 0.75rem; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: #94a3b8;">
                                        Tanpa Foto
                                    </div>
                                @endif
                                <div style="font-size: 0.8125rem; font-weight: 800; color: #0f172a;">
                                    {{ $pres->jam_pulang ? $pres->jam_pulang->format('H:i') . ' WITA' : '-' }}
                                </div>
                                <div style="font-size: 0.625rem; font-weight: 800; {{ $pres->status_pulang === 'pulang_cepat' ? 'color:#d97706;' : 'color:#0f766e;' }}">
                                    {{ ucfirst(str_replace('_', ' ', $pres->status_pulang ?? '-')) }}
                                </div>
                            </div>
                        </div>

                        <!-- Data Specs -->
                        <div style="background: #f8fafc; border-radius: 1rem; padding: 0.85rem; font-size: 0.75rem; display: flex; flex-direction: column; gap: 0.45rem; border: 1px solid #e2e8f0;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">Status Kehadiran:</span>
                                <span style="font-weight: 800; color: #059669; text-transform: uppercase;">{{ str_replace('_', ' ', $pres->status_kehadiran) }}</span>
                            </div>
                            @if ($pres->shift)
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: #64748b;">Shift Kerja:</span>
                                    <span style="font-weight: 700;">{{ $pres->shift->nama }} ({{ $pres->shift->jam_masuk?->format('H:i') }} - {{ $pres->shift->jam_pulang?->format('H:i') }})</span>
                                </div>
                            @endif
                            @if ($pres->lokasi_masuk_lat && $pres->lokasi_masuk_lng)
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="color: #64748b;">Koordinat GPS:</span>
                                    <a href="https://www.google.com/maps?q={{ $pres->lokasi_masuk_lat }},{{ $pres->lokasi_masuk_lng }}" target="_blank" style="color: #059669; font-weight: 700; text-decoration: underline;">
                                        Buka Google Maps
                                    </a>
                                </div>
                            @endif
                        </div>
                    @elseif ($detail['is_libur'])
                        <div style="background: #fff1f2; border-radius: 1rem; padding: 1.25rem; text-align: center; color: #9f1239;">
                            <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">🌴</div>
                            <div style="font-weight: 800; font-size: 0.875rem;">Hari Libur</div>
                            <div style="font-size: 0.75rem; opacity: 0.9;">{{ $detail['info_libur'] ?: 'Libur Resmi' }}</div>
                        </div>
                    @else
                        <div style="background: #f8fafc; border-radius: 1rem; padding: 1.25rem; text-align: center; font-size: 0.75rem; color: #94a3b8;">
                            Tidak ada catatan presensi pada tanggal ini.
                        </div>
                    @endif

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="button" wire:click="tutupDetail" style="padding: 0.45rem 1rem; border-radius: 0.6rem; font-size: 0.75rem; font-weight: 700; background: #f1f5f9; border: 1px solid #cbd5e1; cursor: pointer;">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- PRINTABLE MODAL / SLIP KEHADIRAN BULANAN GURU -->
        <div x-show="showPrintModal" style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px); padding: 1rem;" style="display: none;">
            <div style="background: #ffffff; color: #0f172a; border-radius: 1.5rem; padding: 2rem; width: 100%; max-width: 56rem; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.4);" id="slip-print-area">
                <!-- Action Bar -->
                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.25rem;" class="print:hidden">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <x-filament::icon icon="heroicon-o-printer" style="width: 1.35rem; height: 1.35rem; color: #059669;" />
                        <span style="font-weight: 800; font-size: 1rem;">Pratinjau Slip Rekap Kehadiran Guru</span>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="button" onclick="window.print()" class="rp-btn-print">
                            <x-filament::icon icon="heroicon-o-printer" style="width: 0.95rem; height: 0.95rem;" />
                            <span>Cetak Sekarang</span>
                        </button>
                        <button type="button" @click="showPrintModal = false" style="padding: 0.45rem 0.85rem; border-radius: 0.6rem; font-size: 0.75rem; font-weight: 700; background: #f1f5f9; border: 1px solid #cbd5e1; cursor: pointer;">
                            Tutup
                        </button>
                    </div>
                </div>

                <!-- Printable Document Content -->
                <div style="display: flex; flex-direction: column; gap: 1.25rem; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 1rem; font-family: sans-serif;">
                    <div style="border-bottom: 2px solid #000; padding-bottom: 0.75rem; text-align: center;">
                        <h2 style="font-size: 1.25rem; font-weight: 900; text-transform: uppercase; margin: 0;">{{ $this->pengaturan->nama_sekolah ?: 'UPTD SPF SD Inpres Rappojawa' }}</h2>
                        <p style="font-size: 0.75rem; color: #475569; margin: 0.25rem 0;">{{ $this->pengaturan->alamat ?: 'Kota Makassar, Sulawesi Selatan' }} &bull; NPSN: {{ $this->pengaturan->npsn ?: '-' }} &bull; Telp: {{ $this->pengaturan->telepon ?: '-' }}</p>
                        <p style="font-size: 0.8125rem; font-weight: 800; text-transform: uppercase; color: #065f46; margin: 0.35rem 0 0 0;">SLIP REKAPITULASI KEHADIRAN BULANAN GURU</p>
                    </div>

                    <!-- Teacher Information -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.75rem; background: #f8fafc; padding: 0.85rem; border-radius: 0.75rem; border: 1px solid #e2e8f0;">
                        <div>
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 7rem; color: #64748b;">Nama Guru</td>
                                    <td style="font-weight: 800;">: {{ $this->guru?->nama ?? auth()->user()->name }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b;">NIP / NUPTK</td>
                                    <td style="font-weight: 700;">: {{ $this->guru?->nip ?: ($this->guru?->nuptk ?: 'Non-NIP') }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b;">Jabatan / Tugas</td>
                                    <td>: {{ $this->guru?->jabatan ?: ($this->guru?->jenis_guru ?: 'Guru') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 7rem; color: #64748b;">Periode Bulan</td>
                                    <td style="font-weight: 800;">: {{ $this->daftarBulan[$this->bulan] }} {{ $this->tahun }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b;">Status Kepegawaian</td>
                                    <td style="font-weight: 700;">: {{ strtoupper($this->guru?->status_kepegawaian ?? 'GURU') }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b;">Shift Kerja</td>
                                    <td>: {{ $this->guru?->shift?->nama ?? 'Shift Pagi' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Recap Statistics Summary Table -->
                    <div>
                        <table style="width: 100%; font-size: 0.75rem; text-align: center; border-collapse: collapse; border: 1px solid #cbd5e1;">
                            <thead style="background: #f1f5f9; font-weight: 800;">
                                <tr>
                                    <th style="border: 1px solid #cbd5e1; padding: 0.5rem; color: #065f46;">Hadir</th>
                                    <th style="border: 1px solid #cbd5e1; padding: 0.5rem; color: #0f766e;">Tepat Waktu</th>
                                    <th style="border: 1px solid #cbd5e1; padding: 0.5rem; color: #92400e;">Terlambat</th>
                                    <th style="border: 1px solid #cbd5e1; padding: 0.5rem; color: #0d9488;">Dinas Luar</th>
                                    <th style="border: 1px solid #cbd5e1; padding: 0.5rem; color: #1e40af;">Sakit</th>
                                    <th style="border: 1px solid #cbd5e1; padding: 0.5rem; color: #854d0e;">Izin</th>
                                    <th style="border: 1px solid #cbd5e1; padding: 0.5rem; color: #6b21a8;">Cuti</th>
                                    <th style="border: 1px solid #cbd5e1; padding: 0.5rem; color: #9f1239;">Alpa</th>
                                    <th style="border: 1px solid #cbd5e1; padding: 0.5rem; background: #ecfdf5; color: #065f46;">% Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="font-weight: 900; font-size: 0.875rem;">
                                    <td style="border: 1px solid #cbd5e1; padding: 0.65rem;">{{ $this->rekapBulanan['total_hadir'] }} Hari</td>
                                    <td style="border: 1px solid #cbd5e1; padding: 0.65rem;">{{ $this->rekapBulanan['total_tepat_waktu'] }} Hari</td>
                                    <td style="border: 1px solid #cbd5e1; padding: 0.65rem; color: #d97706;">{{ $this->rekapBulanan['total_terlambat'] }} Hari</td>
                                    <td style="border: 1px solid #cbd5e1; padding: 0.65rem; color: #0d9488;">{{ $this->rekapBulanan['total_dinas_luar'] ?? 0 }} Hari</td>
                                    <td style="border: 1px solid #cbd5e1; padding: 0.65rem; color: #2563eb;">{{ $this->rekapBulanan['total_sakit'] }} Hari</td>
                                    <td style="border: 1px solid #cbd5e1; padding: 0.65rem; color: #ca8a04;">{{ $this->rekapBulanan['total_izin'] }} Hari</td>
                                    <td style="border: 1px solid #cbd5e1; padding: 0.65rem; color: #9333ea;">{{ $this->rekapBulanan['total_cuti'] }} Hari</td>
                                    <td style="border: 1px solid #cbd5e1; padding: 0.65rem; color: #e11d48;">{{ $this->rekapBulanan['total_alpa'] }} Hari</td>
                                    <td style="border: 1px solid #cbd5e1; padding: 0.65rem; background: #ecfdf5; color: #065f46;">{{ $this->rekapBulanan['persentase'] }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Signature Area -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; padding-top: 2rem; font-size: 0.75rem; text-align: center;">
                        <div style="display: flex; flex-direction: column; justify-content: space-between; min-height: 5rem;">
                            <div>Guru Yang Bersangkutan,</div>
                            <div>
                                <div style="font-weight: 800; text-decoration: underline;">{{ $this->guru?->nama ?? auth()->user()->name }}</div>
                                <div style="color: #64748b;">NIP. {{ $this->guru?->nip ?: '-' }}</div>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; justify-content: space-between; min-height: 5rem;">
                            <div>Makassar, {{ now()->translatedFormat('d F Y') }}<br>Kepala Sekolah,</div>
                            <div>
                                <div style="font-weight: 800; text-decoration: underline;">{{ $this->pengaturan->kepala_sekolah ?: 'Kepala Sekolah' }}</div>
                                <div style="color: #64748b;">NIP. {{ $this->pengaturan->nip_kepala_sekolah ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
