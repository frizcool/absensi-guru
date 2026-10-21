<x-filament-panels::page>
    @php
        $stats = $this->getSummaryStats();
        $filterOptions = $this->getFilterOptions();
        $logFiles = $this->getLogFiles();
    @endphp

    <style>
        .lm-wrapper {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .lm-stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .lm-stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .dark .lm-stat-card {
            background: #111827;
            border-color: #1f2937;
            box-shadow: none;
        }

        .lm-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .lm-tab-container {
            display: inline-flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
            gap: 4px;
        }
        .dark .lm-tab-container {
            background: #1e293b;
        }

        .lm-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            background: transparent;
        }
        .dark .lm-tab-btn {
            color: #94a3b8;
        }

        .lm-tab-btn.active {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .dark .lm-tab-btn.active {
            background: #0f172a;
            color: #f8fafc;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }

        .lm-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            text-align: left;
            font-size: 13px;
        }
        .lm-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        .dark .lm-table th {
            background: #182234;
            color: #94a3b8;
            border-bottom-color: #1f2937;
        }
        .lm-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .dark .lm-table td {
            border-bottom-color: #1e293b;
        }
        .lm-table tbody tr:hover {
            background: #f8fafc;
        }
        .dark .lm-table tbody tr:hover {
            background: #182234;
        }

        .lm-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
    </style>

    <div class="lm-wrapper">
        <!-- 1. STATS METRICS (Clean 4-column layout) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Metric 1: Aktivitas Hari Ini -->
            <div class="lm-stat-card">
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aktivitas Hari Ini</span>
                    <span class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['activity_today']) }}</span>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium mt-1">Audit log tercatat</span>
                </div>
                <div class="lm-icon-box bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                    <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
                </div>
            </div>

            <!-- Metric 2: Autentikasi Hari Ini -->
            <div class="lm-stat-card">
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Login Hari Ini</span>
                    <div class="flex items-baseline space-x-2 mt-1">
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['login_success_today'] }}</span>
                        <span class="text-xs text-gray-400">/</span>
                        <span class="text-sm font-bold {{ $stats['login_failed_today'] > 0 ? 'text-rose-500' : 'text-gray-400' }}">
                            {{ $stats['login_failed_today'] }} Gagal
                        </span>
                    </div>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Akses akun pengguna</span>
                </div>
                <div class="lm-icon-box bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                    <x-heroicon-o-shield-check class="w-6 h-6" />
                </div>
            </div>

            <!-- Metric 3: Error Sistem Terdeteksi -->
            <div class="lm-stat-card">
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Error Sistem</span>
                    <span class="text-2xl font-bold {{ $stats['system_errors'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-900 dark:text-white' }} mt-1">
                        {{ number_format($stats['system_errors']) }}
                    </span>
                    <span class="text-[11px] {{ $stats['system_errors'] > 0 ? 'text-rose-500 font-semibold' : 'text-emerald-600' }} mt-1">
                        {{ $stats['system_errors'] > 0 ? 'Perlu perhatian admin' : 'Sistem beroperasi normal' }}
                    </span>
                </div>
                <div class="lm-icon-box {{ $stats['system_errors'] > 0 ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800' }}">
                    <x-heroicon-o-bug-ant class="w-6 h-6" />
                </div>
            </div>

            <!-- Metric 4: Berkas Log & Status -->
            <div class="lm-stat-card">
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ukuran Berkas Log</span>
                    <span class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['log_file_size'] }}</span>
                    <span class="text-[11px] text-gray-400 truncate max-w-[140px] mt-1" title="{{ $stats['last_modified'] }}">
                        Ubah: {{ $stats['last_modified'] }}
                    </span>
                </div>
                <div class="lm-icon-box bg-purple-50 text-purple-600 dark:bg-purple-950/50 dark:text-purple-400">
                    <x-heroicon-o-server-stack class="w-6 h-6" />
                </div>
            </div>
        </div>

        <!-- 2. SEGMENTED TAB SWITCHER -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="lm-tab-container">
                <button
                    type="button"
                    wire:click="setTab('activity')"
                    class="lm-tab-btn {{ $activeTab === 'activity' ? 'active' : '' }}"
                >
                    <x-heroicon-o-user-group class="w-4 h-4" />
                    <span>Log Aktivitas Pengguna (Audit Trail)</span>
                </button>

                <button
                    type="button"
                    wire:click="setTab('system')"
                    class="lm-tab-btn {{ $activeTab === 'system' ? 'active' : '' }}"
                >
                    <x-heroicon-o-command-line class="w-4 h-4" />
                    <span>Log Sistem & Error Laravel</span>
                    @if($stats['system_errors'] > 0)
                        <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white">
                            {{ $stats['system_errors'] }}
                        </span>
                    @endif
                </button>
            </div>

            <!-- Contextual Quick Action -->
            @if ($activeTab === 'activity')
                <button
                    type="button"
                    wire:click="clearOldActivityLogs(30)"
                    wire:confirm="Hapus catatan audit log aktivitas yang berusia lebih dari 30 hari?"
                    class="inline-flex items-center px-3.5 py-2 text-xs font-semibold rounded-xl text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-900/60 transition"
                >
                    <x-heroicon-o-trash class="w-4 h-4 mr-1.5" />
                    Bersihkan Log > 30 Hari
                </button>
            @else
                <div class="flex items-center space-x-2">
                    <button
                        type="button"
                        wire:click="downloadSystemLog"
                        class="inline-flex items-center px-3.5 py-2 text-xs font-semibold rounded-xl text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/60 transition"
                    >
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1.5" />
                        Unduh File
                    </button>
                    <button
                        type="button"
                        wire:click="clearSystemLog"
                        wire:confirm="PERINGATAN: Berkas log ini akan dikosongkan secara permanen. Lanjutkan?"
                        class="inline-flex items-center px-3.5 py-2 text-xs font-semibold rounded-xl text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-900/60 transition"
                    >
                        <x-heroicon-o-trash class="w-4 h-4 mr-1.5" />
                        Kosongkan
                    </button>
                </div>
            @endif
        </div>

        <!-- 3. TAB CONTENT -->
        @if ($activeTab === 'activity')
            @php $activityLogs = $this->getActivityLogs(); @endphp
            <div class="space-y-4">
                <!-- Filter Toolbar -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- Search Input -->
                        <div class="relative">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Cari Aktivitas</label>
                            <input
                                wire:model.live.debounce.300ms="activitySearch"
                                type="text"
                                placeholder="Deskripsi, nama, atau IP..."
                                class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 h-9"
                            />
                        </div>

                        <!-- Modul Select -->
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Modul</label>
                            <select
                                wire:model.live="activityModule"
                                class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 h-9"
                            >
                                <option value="all">Semua Modul</option>
                                @foreach($filterOptions['modules'] as $mod)
                                    <option value="{{ $mod }}">{{ $mod }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Aksi Select -->
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Tipe Aksi</label>
                            <select
                                wire:model.live="activityAction"
                                class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 h-9"
                            >
                                <option value="all">Semua Aksi</option>
                                @foreach($filterOptions['actions'] as $act)
                                    <option value="{{ $act }}">{{ $act }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Periode Select -->
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Periode Waktu</label>
                            <select
                                wire:model.live="activityPeriod"
                                class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 h-9"
                            >
                                <option value="all">Semua Waktu</option>
                                <option value="today">Hari Ini Saja</option>
                                <option value="7_days">7 Hari Terakhir</option>
                                <option value="30_days">30 Hari Terakhir</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="lm-table">
                            <thead>
                                <tr>
                                    <th style="width: 130px;">Waktu</th>
                                    <th style="width: 170px;">Pengguna</th>
                                    <th style="width: 110px;">Modul</th>
                                    <th style="width: 120px;">Aksi</th>
                                    <th>Deskripsi Kejadian</th>
                                    <th style="width: 130px;">IP Address</th>
                                    <th style="width: 80px;" class="text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activityLogs as $log)
                                    <tr>
                                        <td>
                                            <div class="font-semibold text-gray-800 dark:text-gray-200 text-xs">{{ $log->created_at->format('d/m/Y') }}</div>
                                            <div class="font-mono text-[11px] text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                                        </td>
                                        <td>
                                            <div class="font-semibold text-gray-900 dark:text-white text-xs truncate max-w-[150px]">{{ $log->user_name ?? 'Sistem' }}</div>
                                            @if($log->user_role)
                                                <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ $log->user_role }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="font-semibold text-gray-700 dark:text-gray-300 text-xs">
                                                {{ $log->module }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $actionClass = match($log->action) {
                                                    'LOGIN' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                                                    'LOGOUT' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                                    'LOGIN_FAILED' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                                                    'CREATE', 'BACKUP_CREATE' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300',
                                                    'UPDATE', 'SETTINGS_UPDATE' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                                                    'DELETE', 'BACKUP_DELETE' => 'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300',
                                                    'BACKUP_RESTORE' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300',
                                                    default => 'bg-slate-100 text-slate-800',
                                                };
                                            @endphp
                                            <span class="lm-badge {{ $actionClass }}">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-xs text-gray-800 dark:text-gray-200 line-clamp-2">
                                                {{ $log->description }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="font-mono text-xs text-gray-600 dark:text-gray-400">{{ $log->ip_address ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button
                                                type="button"
                                                wire:click="viewActivityDetail({{ $log->id }})"
                                                class="inline-flex items-center justify-center p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-950/50 transition"
                                                title="Lihat Detail Payload"
                                            >
                                                <x-heroicon-o-eye class="w-4 h-4" />
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                            <x-heroicon-o-inbox class="w-8 h-8 mx-auto mb-2 text-gray-400" />
                                            <div>Tidak ada catatan log aktivitas yang cocok.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($activityLogs->hasPages())
                        <div class="p-3.5 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                            <span class="text-gray-500">
                                Menampilkan {{ $activityLogs->firstItem() ?? 0 }} - {{ $activityLogs->lastItem() ?? 0 }} dari {{ $activityLogs->total() }} catatan
                            </span>
                            <div class="flex items-center space-x-1.5">
                                @if(!$activityLogs->onFirstPage())
                                    <button wire:click="setActivityPage({{ $activityLogs->currentPage() - 1 }})" class="px-2.5 py-1 rounded-lg border bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 shadow-sm">
                                        Sebelumnya
                                    </button>
                                @endif
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-bold">
                                    {{ $activityLogs->currentPage() }} / {{ $activityLogs->lastPage() }}
                                </span>
                                @if($activityLogs->hasMorePages())
                                    <button wire:click="setActivityPage({{ $activityLogs->currentPage() + 1 }})" class="px-2.5 py-1 rounded-lg border bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 shadow-sm">
                                        Selanjutnya
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- TAB 2: SYSTEM LOGS -->
        @if ($activeTab === 'system')
            @php $systemLogs = $this->getSystemLogs(); @endphp
            <div class="space-y-4">
                <!-- Toolbar -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Pilih Berkas</label>
                            <select
                                wire:model.live="selectedLogFile"
                                class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 h-9 font-mono"
                            >
                                @forelse($logFiles as $fileKey => $fileLabel)
                                    <option value="{{ $fileKey }}">{{ $fileLabel }}</option>
                                @empty
                                    <option value="laravel.log">laravel.log</option>
                                @endforelse
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Level Minimal</label>
                            <select
                                wire:model.live="systemLogLevel"
                                class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 h-9"
                            >
                                <option value="ALL">Semua Level</option>
                                <option value="ERROR">ERROR / CRITICAL</option>
                                <option value="WARNING">WARNING</option>
                                <option value="INFO">INFO</option>
                                <option value="DEBUG">DEBUG</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Cari Kata Kunci</label>
                            <input
                                wire:model.live.debounce.300ms="systemLogSearch"
                                type="text"
                                placeholder="Exception, kata kunci, file..."
                                class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 h-9"
                            />
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="p-3 bg-gray-50 dark:bg-gray-750 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                        <span>Berkas: <strong class="font-mono text-gray-800 dark:text-gray-200">{{ $selectedLogFile }}</strong> ({{ $systemLogs['file_size'] }})</span>
                        <span>Ditemukan: <strong class="text-gray-800 dark:text-gray-200">{{ count($systemLogs['entries']) }}</strong> entri</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="lm-table">
                            <thead>
                                <tr>
                                    <th style="width: 150px;">Waktu</th>
                                    <th style="width: 90px;">Level</th>
                                    <th style="width: 70px;">Env</th>
                                    <th>Pesan Log Exception</th>
                                    <th style="width: 90px;" class="text-center">Trace</th>
                                </tr>
                            </thead>
                            <tbody class="font-mono text-xs">
                                @forelse($systemLogs['entries'] as $entry)
                                    @php
                                        $levelClass = match($entry['level']) {
                                            'EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                                            'WARNING' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                                            'NOTICE', 'INFO' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300',
                                            'DEBUG' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                            default => 'bg-slate-100 text-slate-800',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="text-gray-500 whitespace-nowrap">{{ $entry['date'] }}</td>
                                        <td>
                                            <span class="lm-badge font-sans {{ $levelClass }}">{{ $entry['level'] }}</span>
                                        </td>
                                        <td class="text-gray-400 uppercase text-[10px]">{{ $entry['env'] }}</td>
                                        <td class="text-gray-800 dark:text-gray-200 break-words max-w-xl">
                                            <div class="line-clamp-2">{{ $entry['message'] }}</div>
                                        </td>
                                        <td class="text-center font-sans">
                                            <button
                                                type="button"
                                                wire:click="viewSystemLogDetail({{ $entry['id'] }})"
                                                class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/50 dark:text-indigo-300 transition"
                                            >
                                                Trace
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-gray-500 dark:text-gray-400 font-sans">
                                            <x-heroicon-o-check-circle class="w-8 h-8 mx-auto mb-2 text-emerald-500" />
                                            <div>Tidak ada catatan log sistem yang ditemukan.</div>
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
            <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-xl w-full border border-gray-200 dark:border-gray-700 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50/80 dark:bg-gray-750">
                        <div class="flex items-center space-x-2">
                            <span class="lm-badge bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                {{ $act->action }}
                            </span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Detail Audit #{{ $act->id }}</span>
                        </div>
                        <button wire:click="closeActivityDetail" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto text-xs">
                        <div class="grid grid-cols-2 gap-3 bg-gray-50 dark:bg-gray-700/30 p-3.5 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase block">Pengguna</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $act->user_name ?? 'Sistem' }}</span>
                                <span class="text-gray-500 block text-[11px]">{{ $act->user_role ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase block">Waktu</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $act->created_at->format('d/m/Y H:i:s') }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase block">Modul</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $act->module }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase block">Alamat IP</span>
                                <span class="font-mono text-gray-900 dark:text-white">{{ $act->ip_address ?? '-' }}</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase block mb-1">Deskripsi</span>
                            <div class="p-3 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200 leading-relaxed">
                                {{ $act->description }}
                            </div>
                        </div>

                        @if(!empty($act->properties))
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase block mb-1">Payload Perubahan</span>
                                <pre class="p-3.5 bg-gray-900 text-emerald-400 rounded-xl font-mono text-[11px] overflow-x-auto max-h-52 border border-gray-800">{{ json_encode($act->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endif
                    </div>

                    <div class="p-3.5 border-t border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-750 flex justify-end">
                        <button
                            type="button"
                            wire:click="closeActivityDetail"
                            class="px-4 py-1.5 text-xs font-semibold rounded-xl bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-white transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL: System Log Trace -->
        @if ($selectedSystemLogId && $this->selectedSystemLog)
            @php $sys = $this->selectedSystemLog; @endphp
            <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-3xl w-full border border-gray-200 dark:border-gray-700 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50/80 dark:bg-gray-750">
                        <div class="flex items-center space-x-2">
                            <span class="lm-badge bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 font-mono">
                                {{ $sys['level'] }}
                            </span>
                            <span class="text-xs font-mono text-gray-500">[{{ $sys['date'] }}] {{ $sys['env'] }}</span>
                        </div>
                        <button wire:click="closeSystemLogDetail" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto text-xs">
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase block mb-1">Pesan Exception</span>
                            <div class="p-3 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 rounded-xl font-mono text-rose-900 dark:text-rose-300 break-words">
                                {{ $sys['message'] }}
                            </div>
                        </div>

                        @if(!empty($sys['trace']))
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase block mb-1">Stack Trace</span>
                                <pre class="p-3.5 bg-gray-950 text-gray-300 rounded-xl font-mono text-[11px] overflow-x-auto max-h-80 border border-gray-800 whitespace-pre-wrap leading-relaxed">{{ $sys['trace'] }}</pre>
                            </div>
                        @endif
                    </div>

                    <div class="p-3.5 border-t border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-750 flex justify-end">
                        <button
                            type="button"
                            wire:click="closeSystemLogDetail"
                            class="px-4 py-1.5 text-xs font-semibold rounded-xl bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-white transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
