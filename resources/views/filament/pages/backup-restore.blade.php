<x-filament-panels::page>
    @php
        $stats = $this->getBackupStats();
        $backups = $this->getBackupsList();
    @endphp

    <style>
        :root {
            --adm-bg: #ffffff;
            --adm-card-bg: #ffffff;
            --adm-border: #e2e8f0;
            --adm-ink: #0f172a;
            --adm-muted: #64748b;
            --adm-subtle: #f8fafc;
            --adm-primary: #059669;
            --adm-primary-hover: #047857;
            --adm-accent-soft: rgba(5, 150, 105, 0.08);
            --adm-accent-border: rgba(5, 150, 105, 0.25);
        }

        .dark, html.dark, .fi-theme-dark {
            --adm-bg: #111827;
            --adm-card-bg: #182234;
            --adm-border: #1f2937;
            --adm-ink: #f8fafc;
            --adm-muted: #94a3b8;
            --adm-subtle: #111827;
            --adm-primary: #10b981;
            --adm-primary-hover: #059669;
            --adm-accent-soft: rgba(16, 185, 129, 0.12);
            --adm-accent-border: rgba(16, 185, 129, 0.3);
        }

        .adm-wrapper {
            display: flex;
            flex-direction: column;
            gap: 18px;
            color: var(--adm-ink);
            font-family: inherit;
        }

        /* SVG SIZING ENFORCEMENT */
        .adm-wrapper svg {
            display: inline-block;
            vertical-align: middle;
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        /* STATS GRID */
        .adm-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }
        @media (max-width: 1024px) {
            .adm-stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 640px) {
            .adm-stats-grid { grid-template-columns: 1fr; }
        }

        .adm-stat-card {
            background: var(--adm-card-bg);
            border: 1px solid var(--adm-border);
            border-radius: 12px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            border-left: 4px solid var(--adm-primary);
        }
        .adm-stat-card-blue { border-left-color: #0284c7; }
        .adm-stat-card-purple { border-left-color: #8b5cf6; }
        .adm-stat-card-amber { border-left-color: #f59e0b; }

        .adm-stat-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .adm-stat-label {
            font-size: 11px;
            font-weight: 750;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--adm-muted);
            white-space: nowrap;
        }

        .adm-stat-value {
            font-size: 22px;
            font-weight: 800;
            color: var(--adm-ink);
            line-height: 1.2;
            white-space: nowrap;
        }

        .adm-stat-hint {
            font-size: 11px;
            color: var(--adm-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .adm-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .adm-stat-icon svg {
            width: 22px;
            height: 22px;
        }

        /* ACTION BAR */
        .adm-action-bar {
            background: var(--adm-card-bg);
            border: 1px solid var(--adm-border);
            border-radius: 12px;
            padding: 14px 18px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .adm-action-title {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .adm-action-heading {
            font-size: 14px;
            font-weight: 750;
            color: var(--adm-ink);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .adm-action-sub {
            font-size: 12px;
            color: var(--adm-muted);
        }

        .adm-action-btns {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .adm-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            text-decoration: none;
            line-height: 1.2;
        }
        .adm-btn svg {
            width: 16px;
            height: 16px;
        }

        .adm-btn-primary {
            background: #059669;
            color: #ffffff !important;
            border-color: #047857;
        }
        .adm-btn-primary:hover {
            background: #047857;
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
        }

        .adm-btn-secondary {
            background: var(--adm-card-bg);
            border-color: var(--adm-border);
            color: var(--adm-ink) !important;
        }
        .adm-btn-secondary:hover {
            background: var(--adm-subtle);
            border-color: var(--adm-muted);
        }

        .adm-btn-danger {
            background: #dc2626;
            color: #ffffff !important;
        }
        .adm-btn-danger:hover {
            background: #b91c1c;
        }

        .adm-btn-icon {
            padding: 6px 9px;
            border-radius: 7px;
            border: 1px solid var(--adm-border);
            background: var(--adm-card-bg);
            color: var(--adm-ink);
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .adm-btn-icon:hover {
            background: var(--adm-subtle);
        }
        .adm-btn-icon svg {
            width: 15px;
            height: 15px;
        }
        .adm-btn-icon-danger:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
        }
        .adm-btn-icon-amber:hover {
            background: #fffbeb;
            border-color: #fde68a;
            color: #d97706;
        }
        .adm-btn-icon-emerald:hover {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #059669;
        }

        /* TABLE SHELL */
        .adm-table-shell {
            background: var(--adm-card-bg);
            border: 1px solid var(--adm-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        }

        .adm-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            text-align: left;
        }

        .adm-table th {
            background: var(--adm-subtle);
            color: var(--adm-muted);
            font-size: 11px;
            font-weight: 750;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 12px 16px;
            border-bottom: 1px solid var(--adm-border);
            white-space: nowrap;
        }

        .adm-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--adm-border);
            vertical-align: middle;
            color: var(--adm-ink);
        }
        .adm-table tbody tr:last-child td {
            border-bottom: none;
        }
        .adm-table tbody tr:hover {
            background: var(--adm-subtle);
        }

        .adm-pill {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }
        .adm-pill-emerald { background: #dcfce7; color: #15803d; }
        .adm-pill-blue { background: #e0f2fe; color: #0369a1; }
        .adm-pill-purple { background: #f3e8ff; color: #7e22ce; }
        .adm-pill-amber { background: #fef3c7; color: #b45309; }

        /* MODAL DIALOGS */
        .adm-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .adm-modal-box {
            background: var(--adm-card-bg);
            border: 1px solid var(--adm-border);
            border-radius: 14px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            color: var(--adm-ink);
        }

        .adm-modal-header {
            padding: 14px 18px;
            border-bottom: 1px solid var(--adm-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--adm-subtle);
            font-size: 13.5px;
            font-weight: 750;
        }

        .adm-modal-body {
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            font-size: 12.5px;
            max-height: 75vh;
            overflow-y: auto;
        }

        .adm-modal-footer {
            padding: 12px 18px;
            border-top: 1px solid var(--adm-border);
            background: var(--adm-subtle);
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .adm-radio-card {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 11px 14px;
            border: 1px solid var(--adm-border);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.15s ease;
            background: var(--adm-card-bg);
        }
        .adm-radio-card:hover {
            border-color: var(--adm-primary);
        }
        .adm-radio-card.selected {
            border-color: var(--adm-primary);
            background: var(--adm-accent-soft);
        }

        .adm-input {
            width: 100%;
            height: 38px;
            padding: 0 12px;
            background: var(--adm-card-bg);
            border: 1px solid var(--adm-border);
            border-radius: 8px;
            font-size: 12.5px;
            color: var(--adm-ink);
            outline: none;
            box-sizing: border-box;
        }
        .adm-input:focus {
            border-color: var(--adm-primary);
        }
    </style>

    <div class="adm-wrapper">
        <!-- 1. STATS METRICS -->
        <div class="adm-stats-grid">
            <div class="adm-stat-card">
                <div class="adm-stat-info">
                    <span class="adm-stat-label">Total Cadangan</span>
                    <span class="adm-stat-value">{{ $stats['total_backups'] }} file</span>
                    <span class="adm-stat-hint">Tersimpan di storage lokal</span>
                </div>
                <div class="adm-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                    <x-heroicon-o-circle-stack />
                </div>
            </div>

            <div class="adm-stat-card adm-stat-card-blue">
                <div class="adm-stat-info">
                    <span class="adm-stat-label">Ukuran Arsip</span>
                    <span class="adm-stat-value">{{ $stats['total_size'] }}</span>
                    <span class="adm-stat-hint">Format kompresi ZIP</span>
                </div>
                <div class="adm-stat-icon" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                    <x-heroicon-o-archive-box />
                </div>
            </div>

            <div class="adm-stat-card adm-stat-card-purple">
                <div class="adm-stat-info">
                    <span class="adm-stat-label">Database Aktif</span>
                    <span class="adm-stat-value">{{ $stats['db_driver'] }}</span>
                    <span class="adm-stat-hint">Ukuran file: {{ $stats['db_size'] }}</span>
                </div>
                <div class="adm-stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <x-heroicon-o-circle-stack />
                </div>
            </div>

            <div class="adm-stat-card adm-stat-card-amber">
                <div class="adm-stat-info">
                    <span class="adm-stat-label">Cadangan Terakhir</span>
                    <span class="adm-stat-value" style="font-size: 14px; margin: 6px 0;">{{ $stats['latest_backup'] }}</span>
                    <span class="adm-stat-hint">Riwayat backup sistem</span>
                </div>
                <div class="adm-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <x-heroicon-o-clock />
                </div>
            </div>
        </div>

        <!-- 2. ACTION BAR -->
        <div class="adm-action-bar">
            <div class="adm-action-title">
                <div class="adm-action-heading">
                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #059669;"></span>
                    <span>Cadangan &amp; Pemulihan Data (Backup &amp; Restore)</span>
                </div>
                <div class="adm-action-sub">
                    Pencadangan rutin direkomendasikan sebelum pembaruan aplikasi atau pergantian tahun ajaran sekolah.
                </div>
            </div>

            <div class="adm-action-btns">
                <button type="button" wire:click="openUploadModal" class="adm-btn adm-btn-secondary">
                    <x-heroicon-o-cloud-arrow-up />
                    <span>Upload &amp; Restore</span>
                </button>

                <button type="button" wire:click="openCreateModal" class="adm-btn adm-btn-primary">
                    <x-heroicon-o-plus />
                    <span>Buat Backup Baru</span>
                </button>
            </div>
        </div>

        <!-- 3. DATA TABLE -->
        <div class="adm-table-shell">
            <div style="overflow-x: auto; width: 100%;">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Berkas Cadangan</th>
                            <th style="width: 140px;">Komponen</th>
                            <th style="width: 110px;">Ukuran</th>
                            <th style="width: 160px;">Waktu Dibuat</th>
                            <th>Pembuat / Catatan</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $b)
                            <tr style="{{ $b['is_safety'] ? 'background: rgba(245, 158, 11, 0.05);' : '' }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="padding: 6px; border-radius: 6px; background: rgba(0,0,0,0.04); display: flex;">
                                            <x-heroicon-o-document-arrow-down style="width: 16px; height: 16px; color: #059669;" />
                                        </div>
                                        <div>
                                            <div style="font-family: monospace; font-weight: 700; font-size: 12px;">{{ $b['filename'] }}</div>
                                            @if($b['is_safety'])
                                                <span class="adm-pill adm-pill-amber" style="margin-top: 3px;">
                                                    Snapshot Keselamatan
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $pillClass = match($b['type']) {
                                            'full' => 'adm-pill-emerald',
                                            'database' => 'adm-pill-blue',
                                            'media' => 'adm-pill-purple',
                                            'safety' => 'adm-pill-amber',
                                            default => 'adm-pill-blue',
                                        };
                                        $pillLabel = match($b['type']) {
                                            'full' => 'Penuh (DB + Media)',
                                            'database' => 'Database Saja',
                                            'media' => 'Media Saja',
                                            'safety' => 'Snapshot Safety',
                                            default => strtoupper($b['type']),
                                        };
                                    @endphp
                                    <span class="adm-pill {{ $pillClass }}">{{ $pillLabel }}</span>
                                </td>
                                <td>
                                    <span style="font-family: monospace; font-weight: 700;">{{ $b['size_formatted'] }}</span>
                                </td>
                                <td>
                                    <span style="color: var(--adm-muted); font-size: 12px;">{{ $b['created_at'] }}</span>
                                </td>
                                <td>
                                    <div style="font-weight: 700; font-size: 12px;">{{ $b['created_by'] }}</div>
                                    <div style="color: var(--adm-muted); font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;">{{ $b['notes'] }}</div>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: inline-flex; align-items: center; gap: 4px;">
                                        <!-- Download -->
                                        <button
                                            type="button"
                                            wire:click="downloadBackup('{{ $b['filename'] }}')"
                                            class="adm-btn-icon adm-btn-icon-emerald"
                                            title="Unduh Berkas ZIP"
                                        >
                                            <x-heroicon-o-arrow-down-tray />
                                        </button>

                                        <!-- Restore -->
                                        <button
                                            type="button"
                                            wire:click="openRestoreModal('{{ $b['filename'] }}')"
                                            class="adm-btn-icon adm-btn-icon-amber"
                                            title="Pulihkan (Restore)"
                                        >
                                            <x-heroicon-o-arrow-path />
                                        </button>

                                        <!-- Delete -->
                                        <button
                                            type="button"
                                            wire:click="deleteBackup('{{ $b['filename'] }}')"
                                            wire:confirm="Hapus berkas cadangan {{ $b['filename'] }} secara permanen?"
                                            class="adm-btn-icon adm-btn-icon-danger"
                                            title="Hapus"
                                        >
                                            <x-heroicon-o-trash />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 40px; text-align: center; color: var(--adm-muted);">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                        <x-heroicon-o-circle-stack style="width: 32px; height: 32px; opacity: 0.4;" />
                                        <div style="font-weight: 700; font-size: 13px;">Belum Ada Berkas Cadangan</div>
                                        <div style="font-size: 11.5px;">Klik tombol "Buat Backup Baru" di atas untuk membuat arsip cadangan.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: BUAT BACKUP -->
        @if ($showCreateModal)
            <div class="adm-modal-backdrop">
                <div class="adm-modal-box">
                    <div class="adm-modal-header">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <x-heroicon-o-circle-stack style="color: #059669;" />
                            <span>Buat Cadangan Sistem Baru</span>
                        </div>
                        <button type="button" wire:click="closeCreateModal" style="background: none; border: none; cursor: pointer; color: var(--adm-muted);">
                            <x-heroicon-o-x-mark />
                        </button>
                    </div>

                    <div class="adm-modal-body">
                        <div>
                            <span style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; color: var(--adm-muted); display: block; margin-bottom: 8px;">Komponen Yang Dicadangkan</span>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label class="adm-radio-card {{ $backupType === 'full' ? 'selected' : '' }}">
                                    <input type="radio" wire:model.live="backupType" value="full" style="margin-top: 2px;" />
                                    <div>
                                        <div style="font-weight: 750;">Cadangan Penuh (Database &amp; Media / Uploads)</div>
                                        <div style="font-size: 11px; color: var(--adm-muted); margin-top: 2px;">Tabel presensi, akun guru, dan foto absensi/profil. (Direkomendasikan)</div>
                                    </div>
                                </label>

                                <label class="adm-radio-card {{ $backupType === 'database' ? 'selected' : '' }}">
                                    <input type="radio" wire:model.live="backupType" value="database" style="margin-top: 2px;" />
                                    <div>
                                        <div style="font-weight: 750;">Hanya Database</div>
                                        <div style="font-size: 11px; color: var(--adm-muted); margin-top: 2px;">Hanya basis data SQL/SQLite (cepat &amp; ukuran kecil).</div>
                                    </div>
                                </label>

                                <label class="adm-radio-card {{ $backupType === 'media' ? 'selected' : '' }}">
                                    <input type="radio" wire:model.live="backupType" value="media" style="margin-top: 2px;" />
                                    <div>
                                        <div style="font-weight: 750;">Hanya Berkas Media (Storage)</div>
                                        <div style="font-size: 11px; color: var(--adm-muted); margin-top: 2px;">Khusus foto profil dan foto bukti presensi.</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <span style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; color: var(--adm-muted); display: block; margin-bottom: 6px;">Catatan Tambahan (Opsional)</span>
                            <input
                                type="text"
                                wire:model="backupNotes"
                                class="adm-input"
                                placeholder="Contoh: Sebelum migrasi jadwal semester genap..."
                            />
                        </div>
                    </div>

                    <div class="adm-modal-footer">
                        <button type="button" wire:click="closeCreateModal" class="adm-btn adm-btn-secondary">
                            Batal
                        </button>
                        <button type="button" wire:click="createBackup" wire:loading.attr="disabled" class="adm-btn adm-btn-primary">
                            <span wire:loading.remove wire:target="createBackup">Mulai Pencadangan</span>
                            <span wire:loading wire:target="createBackup">Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL: RESTORE KONFIRMASI -->
        @if ($showRestoreModal)
            <div class="adm-modal-backdrop">
                <div class="adm-modal-box">
                    <div class="adm-modal-header" style="background: rgba(220, 38, 38, 0.08); color: #dc2626;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <x-heroicon-o-exclamation-triangle />
                            <span>Konfirmasi Pemulihan (Restore)</span>
                        </div>
                        <button type="button" wire:click="closeRestoreModal" style="background: none; border: none; cursor: pointer; color: var(--adm-muted);">
                            <x-heroicon-o-x-mark />
                        </button>
                    </div>

                    <div class="adm-modal-body">
                        <div style="padding: 12px; border-radius: 8px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); color: #b45309; font-size: 11.5px; line-height: 1.5;">
                            <div style="font-weight: 750; margin-bottom: 4px; display: flex; align-items: center; gap: 5px;">
                                <x-heroicon-o-shield-check style="width: 16px; height: 16px; color: #059669;" />
                                <span>Perlindungan Keselamatan Otomatis</span>
                            </div>
                            <div>Pemulihan berkas <strong style="font-family: monospace;">{{ $selectedBackupFilename }}</strong> akan menimpa basis data saat ini.</div>
                            <div style="margin-top: 4px; color: #059669; font-weight: 700;">✓ Snapshot cadangan keselamatan otomatis dibuat sebelum penimpaan berjalan.</div>
                        </div>

                        <div>
                            <span style="font-size: 11px; font-weight: 750; text-transform: uppercase; color: var(--adm-muted); display: block; margin-bottom: 6px;">
                                Ketik kata <span style="color: #dc2626; font-weight: 900;">PULIHKAN</span> untuk konfirmasi:
                            </span>
                            <input
                                type="text"
                                wire:model="confirmRestoreText"
                                class="adm-input"
                                placeholder="PULIHKAN"
                                style="font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;"
                            />
                        </div>
                    </div>

                    <div class="adm-modal-footer">
                        <button type="button" wire:click="closeRestoreModal" class="adm-btn adm-btn-secondary">
                            Batal
                        </button>
                        <button type="button" wire:click="restoreBackup" wire:loading.attr="disabled" class="adm-btn adm-btn-danger">
                            <span wire:loading.remove wire:target="restoreBackup">Pulihkan Data Sekarang</span>
                            <span wire:loading wire:target="restoreBackup">Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL: UPLOAD & RESTORE -->
        @if ($showUploadModal)
            <div class="adm-modal-backdrop">
                <div class="adm-modal-box">
                    <div class="adm-modal-header">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <x-heroicon-o-cloud-arrow-up style="color: #0284c7;" />
                            <span>Upload &amp; Restore Arsip Cadangan</span>
                        </div>
                        <button type="button" wire:click="closeUploadModal" style="background: none; border: none; cursor: pointer; color: var(--adm-muted);">
                            <x-heroicon-o-x-mark />
                        </button>
                    </div>

                    <div class="adm-modal-body">
                        <div style="color: var(--adm-muted); font-size: 12px;">Pilih berkas cadangan <strong style="font-family: monospace;">.zip</strong> yang sebelumnya diunduh dari aplikasi ini.</div>

                        <div style="border: 2px dashed var(--adm-border); border-radius: 10px; padding: 20px; text-align: center;">
                            <input
                                type="file"
                                wire:model="uploadedBackupFile"
                                accept=".zip"
                                style="font-size: 12px; max-width: 100%;"
                            />
                            @error('uploadedBackupFile')
                                <div style="color: #dc2626; font-size: 11px; margin-top: 6px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div style="font-size: 11px; color: var(--adm-muted); padding: 8px 12px; background: var(--adm-subtle); border-radius: 8px;">
                            Sistem secara otomatis membuat snapshot keselamatan sebelum menimpa data dengan berkas yang diunggah.
                        </div>
                    </div>

                    <div class="adm-modal-footer">
                        <button type="button" wire:click="closeUploadModal" class="adm-btn adm-btn-secondary">
                            Batal
                        </button>
                        <button type="button" wire:click="uploadAndRestore" wire:loading.attr="disabled" class="adm-btn adm-btn-primary" style="background: #0284c7; border-color: #0369a1;">
                            <span wire:loading.remove wire:target="uploadAndRestore">Unggah &amp; Pulihkan</span>
                            <span wire:loading wire:target="uploadAndRestore">Mengunggah...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
