<x-filament-panels::page>
    @php
        $stats = $this->getSummaryStats();
        $filterOptions = $this->getFilterOptions();
        $logFiles = $this->getLogFiles();
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
        .adm-stat-card-rose { border-left-color: #e11d48; }
        .adm-stat-card-purple { border-left-color: #8b5cf6; }

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

        /* SEGMENTED TAB SWITCHER */
        .adm-tab-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .adm-tab-pill-box {
            display: inline-flex;
            background: var(--adm-subtle);
            border: 1px solid var(--adm-border);
            padding: 4px;
            border-radius: 10px;
            gap: 4px;
        }

        .adm-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 15px;
            border-radius: 7px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            background: transparent;
            color: var(--adm-muted);
            transition: all 0.15s ease;
        }
        .adm-tab-btn.active {
            background: var(--adm-card-bg);
            color: var(--adm-ink);
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .adm-tab-btn svg {
            width: 16px;
            height: 16px;
        }

        /* TOOLBAR */
        .adm-toolbar {
            background: var(--adm-card-bg);
            border: 1px solid var(--adm-border);
            border-radius: 12px;
            padding: 12px 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .adm-toolbar-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }
        @media (max-width: 1024px) {
            .adm-toolbar-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 640px) {
            .adm-toolbar-grid { grid-template-columns: 1fr; }
        }

        .adm-field-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .adm-field-label {
            font-size: 10.5px;
            font-weight: 750;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--adm-muted);
        }

        .adm-input, .adm-select {
            width: 100%;
            height: 36px;
            padding: 0 10px;
            background: var(--adm-card-bg);
            border: 1px solid var(--adm-border);
            border-radius: 8px;
            font-size: 12px;
            color: var(--adm-ink);
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.15s ease;
        }
        .adm-input:focus, .adm-select:focus {
            border-color: var(--adm-primary);
        }

        /* BUTTONS */
        .adm-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            text-decoration: none;
            line-height: 1.2;
        }
        .adm-btn svg {
            width: 15px;
            height: 15px;
        }

        .adm-btn-emerald {
            background: #059669;
            color: #ffffff !important;
            border-color: #047857;
        }
        .adm-btn-emerald:hover { background: #047857; }

        .adm-btn-secondary {
            background: var(--adm-card-bg);
            border-color: var(--adm-border);
            color: var(--adm-ink) !important;
        }
        .adm-btn-secondary:hover {
            background: var(--adm-subtle);
        }

        .adm-btn-danger-soft {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c !important;
        }
        .adm-btn-danger-soft:hover {
            background: #fee2e2;
        }

        .adm-btn-icon {
            padding: 5px 8px;
            border-radius: 6px;
            border: 1px solid var(--adm-border);
            background: var(--adm-card-bg);
            color: var(--adm-ink);
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11.5px;
            font-weight: 700;
        }
        .adm-btn-icon:hover {
            background: var(--adm-subtle);
        }
        .adm-btn-icon svg {
            width: 14px;
            height: 14px;
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
            padding: 11px 16px;
            border-bottom: 1px solid var(--adm-border);
            white-space: nowrap;
        }

        .adm-table td {
            padding: 11px 16px;
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
            padding: 2px 7px;
            border-radius: 5px;
            font-size: 10.5px;
            font-weight: 750;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }
        .adm-pill-emerald { background: #dcfce7; color: #15803d; }
        .adm-pill-blue { background: #e0f2fe; color: #0369a1; }
        .adm-pill-purple { background: #f3e8ff; color: #7e22ce; }
        .adm-pill-amber { background: #fef3c7; color: #b45309; }
        .adm-pill-rose { background: #ffe4e6; color: #be123c; }
        .adm-pill-gray { background: #f1f5f9; color: #475569; }

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
            max-width: 600px;
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
    </style>

    <div class="adm-wrapper">
        <!-- 1. STATS METRICS -->
        <div class="adm-stats-grid">
            <div class="adm-stat-card">
                <div class="adm-stat-info">
                    <span class="adm-stat-label">Aktivitas Hari Ini</span>
                    <span class="adm-stat-value">{{ number_format($stats['activity_today']) }}</span>
                    <span class="adm-stat-hint">Log aktivitas sistem</span>
                </div>
                <div class="adm-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                    <x-heroicon-o-clipboard-document-list />
                </div>
            </div>

            <div class="adm-stat-card adm-stat-card-blue">
                <div class="adm-stat-info">
                    <span class="adm-stat-label">Login Hari Ini</span>
                    <span class="adm-stat-value">
                        {{ $stats['login_success_today'] }} <span style="font-size: 13px; font-weight: 600; color: var(--adm-muted);">/</span>
                        <span style="font-size: 14px; color: {{ $stats['login_failed_today'] > 0 ? '#e11d48' : 'var(--adm-muted)' }}; font-weight: 700;">
                            {{ $stats['login_failed_today'] }} Gagal
                        </span>
                    </span>
                    <span class="adm-stat-hint">Aktivitas autentikasi akun</span>
                </div>
                <div class="adm-stat-icon" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                    <x-heroicon-o-shield-check />
                </div>
            </div>

            <div class="adm-stat-card adm-stat-card-rose">
                <div class="adm-stat-info">
                    <span class="adm-stat-label">Error Sistem Terdeteksi</span>
                    <span class="adm-stat-value" style="color: {{ $stats['system_errors'] > 0 ? '#e11d48' : 'var(--adm-ink)' }};">
                        {{ number_format($stats['system_errors']) }}
                    </span>
                    <span class="adm-stat-hint" style="color: {{ $stats['system_errors'] > 0 ? '#e11d48' : '#059669' }}; font-weight: 600;">
                        {{ $stats['system_errors'] > 0 ? 'Perlu tindakan admin' : 'Sistem beroperasi normal' }}
                    </span>
                </div>
                <div class="adm-stat-icon" style="background: rgba(225, 29, 72, 0.1); color: #e11d48;">
                    <x-heroicon-o-bug-ant />
                </div>
            </div>

            <div class="adm-stat-card adm-stat-card-purple">
                <div class="adm-stat-info">
                    <span class="adm-stat-label">Ukuran Berkas Log</span>
                    <span class="adm-stat-value">{{ $stats['log_file_size'] }}</span>
                    <span class="adm-stat-hint">Diperbarui: {{ $stats['last_modified'] }}</span>
                </div>
                <div class="adm-stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <x-heroicon-o-server-stack />
                </div>
            </div>
        </div>

        <!-- 2. TAB SWITCHER & ACTIONS -->
        <div class="adm-tab-bar">
            <div class="adm-tab-pill-box">
                <button
                    type="button"
                    wire:click="setTab('activity')"
                    class="adm-tab-btn {{ $activeTab === 'activity' ? 'active' : '' }}"
                >
                    <x-heroicon-o-user-group />
                    <span>Log Aktivitas Pengguna</span>
                </button>

                <button
                    type="button"
                    wire:click="setTab('system')"
                    class="adm-tab-btn {{ $activeTab === 'system' ? 'active' : '' }}"
                >
                    <x-heroicon-o-command-line />
                    <span>Log Sistem &amp; Error Laravel</span>
                    @if($stats['system_errors'] > 0)
                        <span class="adm-pill adm-pill-rose" style="margin-left: 4px;">{{ $stats['system_errors'] }}</span>
                    @endif
                </button>
            </div>

            <!-- Quick Action Contextual -->
            @if ($activeTab === 'activity')
                <button
                    type="button"
                    wire:click="clearOldActivityLogs(30)"
                    wire:confirm="Hapus catatan audit log aktivitas yang lebih dari 30 hari?"
                    class="adm-btn adm-btn-danger-soft"
                >
                    <x-heroicon-o-trash />
                    <span>Bersihkan Log &gt; 30 Hari</span>
                </button>
            @else
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button type="button" wire:click="downloadSystemLog" class="adm-btn adm-btn-secondary">
                        <x-heroicon-o-arrow-down-tray />
                        <span>Unduh File</span>
                    </button>
                    <button
                        type="button"
                        wire:click="clearSystemLog"
                        wire:confirm="PERINGATAN: Berkas log ini akan dikosongkan permanen. Lanjutkan?"
                        class="adm-btn adm-btn-danger-soft"
                    >
                        <x-heroicon-o-trash />
                        <span>Kosongkan Log</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- 3. TAB 1: ACTIVITY LOGS -->
        @if ($activeTab === 'activity')
            @php $activityLogs = $this->getActivityLogs(); @endphp
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <!-- Filter Toolbar -->
                <div class="adm-toolbar">
                    <div class="adm-toolbar-grid">
                        <div class="adm-field-group">
                            <span class="adm-field-label">Pencarian</span>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="activitySearch"
                                class="adm-input"
                                placeholder="Cari deskripsi, user, IP..."
                            />
                        </div>

                        <div class="adm-field-group">
                            <span class="adm-field-label">Modul</span>
                            <select wire:model.live="activityModule" class="adm-select">
                                <option value="all">Semua Modul</option>
                                @foreach($filterOptions['modules'] as $mod)
                                    <option value="{{ $mod }}">{{ $mod }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="adm-field-group">
                            <span class="adm-field-label">Aksi</span>
                            <select wire:model.live="activityAction" class="adm-select">
                                <option value="all">Semua Aksi</option>
                                @foreach($filterOptions['actions'] as $act)
                                    <option value="{{ $act }}">{{ $act }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="adm-field-group">
                            <span class="adm-field-label">Periode Waktu</span>
                            <select wire:model.live="activityPeriod" class="adm-select">
                                <option value="all">Semua Waktu</option>
                                <option value="today">Hari Ini</option>
                                <option value="7_days">7 Hari Terakhir</option>
                                <option value="30_days">30 Hari Terakhir</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table Shell -->
                <div class="adm-table-shell">
                    <div style="overflow-x: auto; width: 100%;">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th style="width: 130px;">Waktu</th>
                                    <th style="width: 160px;">Pengguna</th>
                                    <th style="width: 110px;">Modul</th>
                                    <th style="width: 120px;">Aksi</th>
                                    <th>Deskripsi Kejadian</th>
                                    <th style="width: 120px;">Alamat IP</th>
                                    <th style="width: 80px; text-align: center;">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activityLogs as $log)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700; font-size: 12px;">{{ $log->created_at->format('d/m/Y') }}</div>
                                            <div style="font-family: monospace; font-size: 11px; color: var(--adm-muted);">{{ $log->created_at->format('H:i:s') }}</div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; font-size: 12px;">{{ $log->user_name ?? 'Sistem' }}</div>
                                            @if($log->user_role)
                                                <span class="adm-pill adm-pill-gray" style="margin-top: 2px;">{{ $log->user_role }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span style="font-weight: 700; font-size: 12px;">{{ $log->module }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $pillClass = match($log->action) {
                                                    'LOGIN' => 'adm-pill-emerald',
                                                    'LOGOUT' => 'adm-pill-gray',
                                                    'LOGIN_FAILED' => 'adm-pill-rose',
                                                    'CREATE', 'BACKUP_CREATE' => 'adm-pill-blue',
                                                    'UPDATE', 'SETTINGS_UPDATE' => 'adm-pill-amber',
                                                    'DELETE', 'BACKUP_DELETE' => 'adm-pill-rose',
                                                    'BACKUP_RESTORE' => 'adm-pill-purple',
                                                    default => 'adm-pill-gray',
                                                };
                                            @endphp
                                            <span class="adm-pill {{ $pillClass }}">{{ $log->action }}</span>
                                        </td>
                                        <td>
                                            <div style="font-size: 12px; line-height: 1.4;">{{ $log->description }}</div>
                                        </td>
                                        <td>
                                            <span style="font-family: monospace; font-size: 11.5px; color: var(--adm-muted);">{{ $log->ip_address ?? '-' }}</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <button
                                                type="button"
                                                wire:click="viewActivityDetail({{ $log->id }})"
                                                class="adm-btn-icon"
                                                title="Lihat Detail Payload"
                                            >
                                                <x-heroicon-o-eye />
                                                <span>Detail</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="padding: 40px; text-align: center; color: var(--adm-muted);">
                                            <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                                <x-heroicon-o-inbox style="width: 32px; height: 32px; opacity: 0.4;" />
                                                <div style="font-weight: 700; font-size: 13px;">Tidak Ada Catatan Log</div>
                                                <div style="font-size: 11.5px;">Coba sesuaikan filter pencarian Anda di atas.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($activityLogs->hasPages())
                        <div style="padding: 12px 16px; border-top: 1px solid var(--adm-border); background: var(--adm-subtle); display: flex; align-items: center; justify-content: space-between; font-size: 12px;">
                            <span style="color: var(--adm-muted);">
                                Menampilkan {{ $activityLogs->firstItem() ?? 0 }} - {{ $activityLogs->lastItem() ?? 0 }} dari {{ $activityLogs->total() }} catatan
                            </span>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                @if(!$activityLogs->onFirstPage())
                                    <button wire:click="setActivityPage({{ $activityLogs->currentPage() - 1 }})" class="adm-btn adm-btn-secondary" style="padding: 4px 10px; font-size: 11px;">
                                        Sebelumnya
                                    </button>
                                @endif
                                <span class="adm-pill adm-pill-emerald">
                                    {{ $activityLogs->currentPage() }} / {{ $activityLogs->lastPage() }}
                                </span>
                                @if($activityLogs->hasMorePages())
                                    <button wire:click="setActivityPage({{ $activityLogs->currentPage() + 1 }})" class="adm-btn adm-btn-secondary" style="padding: 4px 10px; font-size: 11px;">
                                        Selanjutnya
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- 4. TAB 2: SYSTEM LOGS -->
        @if ($activeTab === 'system')
            @php $systemLogs = $this->getSystemLogs(); @endphp
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <!-- Filter Toolbar -->
                <div class="adm-toolbar">
                    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px;">
                        <div class="adm-field-group">
                            <span class="adm-field-label">Berkas Log</span>
                            <select wire:model.live="selectedLogFile" class="adm-select" style="font-family: monospace;">
                                @forelse($logFiles as $fileKey => $fileLabel)
                                    <option value="{{ $fileKey }}">{{ $fileLabel }}</option>
                                @empty
                                    <option value="laravel.log">laravel.log</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="adm-field-group">
                            <span class="adm-field-label">Level Minimal</span>
                            <select wire:model.live="systemLogLevel" class="adm-select">
                                <option value="ALL">Semua Level</option>
                                <option value="ERROR">ERROR / CRITICAL</option>
                                <option value="WARNING">WARNING</option>
                                <option value="INFO">INFO</option>
                                <option value="DEBUG">DEBUG</option>
                            </select>
                        </div>

                        <div class="adm-field-group">
                            <span class="adm-field-label">Kata Kunci</span>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="systemLogSearch"
                                class="adm-input"
                                placeholder="Cari exception, pesan error..."
                            />
                        </div>
                    </div>
                </div>

                <!-- Table Shell -->
                <div class="adm-table-shell">
                    <div style="padding: 10px 16px; background: var(--adm-subtle); border-bottom: 1px solid var(--adm-border); display: flex; justify-content: space-between; font-size: 11.5px; color: var(--adm-muted);">
                        <span>Berkas: <strong style="font-family: monospace; color: var(--adm-ink);">{{ $selectedLogFile }}</strong> ({{ $systemLogs['file_size'] }})</span>
                        <span>Ditemukan: <strong style="color: var(--adm-ink);">{{ count($systemLogs['entries']) }}</strong> entri</span>
                    </div>

                    <div style="overflow-x: auto; width: 100%;">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th style="width: 150px;">Waktu</th>
                                    <th style="width: 90px;">Level</th>
                                    <th style="width: 70px;">Env</th>
                                    <th>Pesan Exception / Error</th>
                                    <th style="width: 90px; text-align: center;">Trace</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($systemLogs['entries'] as $entry)
                                    @php
                                        $pillClass = match($entry['level']) {
                                            'EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR' => 'adm-pill-rose',
                                            'WARNING' => 'adm-pill-amber',
                                            'NOTICE', 'INFO' => 'adm-pill-blue',
                                            'DEBUG' => 'adm-pill-gray',
                                            default => 'adm-pill-gray',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <span style="font-family: monospace; font-size: 11.5px; color: var(--adm-muted);">{{ $entry['date'] }}</span>
                                        </td>
                                        <td>
                                            <span class="adm-pill {{ $pillClass }}">{{ $entry['level'] }}</span>
                                        </td>
                                        <td>
                                            <span style="font-size: 10.5px; text-transform: uppercase; color: var(--adm-muted);">{{ $entry['env'] }}</span>
                                        </td>
                                        <td>
                                            <div style="font-family: monospace; font-size: 11.5px; line-height: 1.4; word-break: break-all;">{{ $entry['message'] }}</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <button
                                                type="button"
                                                wire:click="viewSystemLogDetail({{ $entry['id'] }})"
                                                class="adm-btn-icon"
                                                title="Lihat Stack Trace"
                                            >
                                                <span>Trace</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="padding: 40px; text-align: center; color: var(--adm-muted);">
                                            <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                                <x-heroicon-o-check-circle style="width: 32px; height: 32px; color: #059669; opacity: 0.8;" />
                                                <div style="font-weight: 700; font-size: 13px;">Tidak Ada Catatan Log Error</div>
                                                <div style="font-size: 11.5px;">Berkas log bersih atau sesuai kriteria filter.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL: Activity Detail -->
        @if ($selectedActivityId && $this->selectedActivity)
            @php $act = $this->selectedActivity; @endphp
            <div class="adm-modal-backdrop">
                <div class="adm-modal-box">
                    <div class="adm-modal-header">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="adm-pill adm-pill-emerald">{{ $act->action }}</span>
                            <span>Detail Audit #{{ $act->id }}</span>
                        </div>
                        <button type="button" wire:click="closeActivityDetail" style="background: none; border: none; cursor: pointer; color: var(--adm-muted);">
                            <x-heroicon-o-x-mark />
                        </button>
                    </div>

                    <div class="adm-modal-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: var(--adm-subtle); padding: 12px; border-radius: 8px; border: 1px solid var(--adm-border);">
                            <div>
                                <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--adm-muted); display: block;">Pengguna</span>
                                <span style="font-weight: 700;">{{ $act->user_name ?? 'Sistem' }}</span>
                                <span style="font-size: 11px; color: var(--adm-muted); display: block;">{{ $act->user_role ?? '-' }}</span>
                            </div>
                            <div>
                                <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--adm-muted); display: block;">Waktu Kejadian</span>
                                <span style="font-weight: 700;">{{ $act->created_at->format('d/m/Y H:i:s') }}</span>
                            </div>
                            <div>
                                <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--adm-muted); display: block;">Modul &amp; Aksi</span>
                                <span style="font-weight: 700;">{{ $act->module }} / {{ $act->action }}</span>
                            </div>
                            <div>
                                <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--adm-muted); display: block;">Alamat IP</span>
                                <span style="font-family: monospace; font-weight: 700;">{{ $act->ip_address ?? '-' }}</span>
                            </div>
                        </div>

                        <div>
                            <span style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; color: var(--adm-muted); display: block; margin-bottom: 4px;">Deskripsi</span>
                            <div style="padding: 10px 12px; background: var(--adm-subtle); border-radius: 8px; border: 1px solid var(--adm-border); line-height: 1.4;">
                                {{ $act->description }}
                            </div>
                        </div>

                        @if(!empty($act->properties))
                            <div>
                                <span style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; color: var(--adm-muted); display: block; margin-bottom: 4px;">Payload Perubahan Data</span>
                                <pre style="padding: 12px; background: #0f172a; color: #34d399; border-radius: 8px; font-family: monospace; font-size: 11px; overflow-x: auto; max-height: 200px;">{{ json_encode($act->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endif
                    </div>

                    <div class="adm-modal-footer">
                        <button type="button" wire:click="closeActivityDetail" class="adm-btn adm-btn-secondary">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL: System Log Trace -->
        @if ($selectedSystemLogId && $this->selectedSystemLog)
            @php $sys = $this->selectedSystemLog; @endphp
            <div class="adm-modal-backdrop">
                <div class="adm-modal-box" style="max-width: 720px;">
                    <div class="adm-modal-header" style="background: rgba(225, 29, 72, 0.08); color: #be123c;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="adm-pill adm-pill-rose">{{ $sys['level'] }}</span>
                            <span style="font-family: monospace; font-size: 11.5px;">[{{ $sys['date'] }}] {{ $sys['env'] }}</span>
                        </div>
                        <button type="button" wire:click="closeSystemLogDetail" style="background: none; border: none; cursor: pointer; color: var(--adm-muted);">
                            <x-heroicon-o-x-mark />
                        </button>
                    </div>

                    <div class="adm-modal-body">
                        <div>
                            <span style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; color: var(--adm-muted); display: block; margin-bottom: 4px;">Pesan Exception</span>
                            <div style="padding: 10px 12px; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; font-family: monospace; font-size: 11.5px; color: #9f1239; line-height: 1.4;">
                                {{ $sys['message'] }}
                            </div>
                        </div>

                        @if(!empty($sys['trace']))
                            <div>
                                <span style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; color: var(--adm-muted); display: block; margin-bottom: 4px;">Stack Trace</span>
                                <pre style="padding: 12px; background: #0f172a; color: #e2e8f0; border-radius: 8px; font-family: monospace; font-size: 11px; overflow-x: auto; max-height: 320px; white-space: pre-wrap; line-height: 1.4;">{{ $sys['trace'] }}</pre>
                            </div>
                        @endif
                    </div>

                    <div class="adm-modal-footer">
                        <button type="button" wire:click="closeSystemLogDetail" class="adm-btn adm-btn-secondary">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
