<x-filament-panels::page>
    @php
        $stats = $this->getSummaryStats();
        $filterOptions = $this->getFilterOptions();
        $logFiles = $this->getLogFiles();
    @endphp

    <div class="space-y-6">
        <!-- Top Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-lg">
                    <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Aktivitas Hari Ini</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['activity_today']) }}</div>
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-lg">
                    <x-heroicon-o-arrow-right-end-on-rectangle class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Login Berhasil Hari Ini</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['login_success_today']) }}</div>
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-lg">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Login Gagal Hari Ini</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['login_failed_today']) }}</div>
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
                <div class="p-3 bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 rounded-lg">
                    <x-heroicon-o-bug-ant class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Error Sistem Terdeteksi</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['system_errors']) }}</div>
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 rounded-lg">
                    <x-heroicon-o-server-stack class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Ukuran Log Sistem</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['log_file_size'] }}</div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                <button
                    wire:click="setTab('activity')"
                    type="button"
                    class="py-3 px-1 border-b-2 font-medium text-sm flex items-center space-x-2 transition-colors {{ $activeTab === 'activity' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:border-gray-300' }}"
                >
                    <x-heroicon-o-user-group class="w-5 h-5" />
                    <span>Log Aktivitas Pengguna (Audit Trail)</span>
                </button>

                <button
                    wire:click="setTab('system')"
                    type="button"
                    class="py-3 px-1 border-b-2 font-medium text-sm flex items-center space-x-2 transition-colors {{ $activeTab === 'system' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:border-gray-300' }}"
                >
                    <x-heroicon-o-command-line class="w-5 h-5" />
                    <span>Log Sistem & Error Laravel</span>
                    @if($stats['system_errors'] > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-300">
                            {{ $stats['system_errors'] }}
                        </span>
                    @endif
                </button>
            </nav>
        </div>

        <!-- TAB 1: ACTIVITY LOGS -->
        @if ($activeTab === 'activity')
            @php
                $activityLogs = $this->getActivityLogs();
            @endphp
            <div class="space-y-4">
                <!-- Filters & Actions -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 w-full md:w-auto flex-1">
                        <!-- Search -->
                        <div class="relative">
                            <input
                                wire:model.live.debounce.350ms="activitySearch"
                                type="text"
                                placeholder="Cari deskripsi, user, IP..."
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500"
                            />
                        </div>

                        <!-- Module Filter -->
                        <div>
                            <select
                                wire:model.live="activityModule"
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500"
                            >
                                <option value="all">Semua Modul</option>
                                @foreach($filterOptions['modules'] as $mod)
                                    <option value="{{ $mod }}">{{ $mod }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Filter -->
                        <div>
                            <select
                                wire:model.live="activityAction"
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500"
                            >
                                <option value="all">Semua Tipe Aksi</option>
                                @foreach($filterOptions['actions'] as $act)
                                    <option value="{{ $act }}">{{ $act }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Period Filter -->
                        <div>
                            <select
                                wire:model.live="activityPeriod"
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500"
                            >
                                <option value="all">Semua Waktu</option>
                                <option value="today">Hari Ini</option>
                                <option value="7_days">7 Hari Terakhir</option>
                                <option value="30_days">30 Hari Terakhir</option>
                            </select>
                        </div>
                    </div>

                    <!-- Clean Logs Action -->
                    <div class="flex items-center space-x-2 w-full md:w-auto justify-end">
                        <button
                            type="button"
                            wire:click="clearOldActivityLogs(30)"
                            wire:confirm="Apakah Anda yakin ingin menghapus catatan log yang lebih tua dari 30 hari?"
                            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/30 dark:text-rose-400 dark:hover:bg-rose-900/50 transition border border-rose-200 dark:border-rose-800"
                        >
                            <x-heroicon-o-trash class="w-4 h-4 mr-1.5" />
                            Bersihkan Log > 30 Hari
                        </button>
                    </div>
                </div>

                <!-- Activity Log Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-4 py-3">Waktu</th>
                                    <th class="px-4 py-3">Pengguna</th>
                                    <th class="px-4 py-3">Modul</th>
                                    <th class="px-4 py-3">Aksi</th>
                                    <th class="px-4 py-3">Deskripsi</th>
                                    <th class="px-4 py-3">IP & Perangkat</th>
                                    <th class="px-4 py-3 text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($activityLogs as $log)
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/40 transition">
                                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                            <div>{{ $log->created_at->format('d/m/Y') }}</div>
                                            <div class="font-mono text-gray-700 dark:text-gray-300 font-semibold">{{ $log->created_at->format('H:i:s') }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $log->user_name ?? 'Sistem' }}</div>
                                            @if($log->user_role)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ $log->user_role }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-700 dark:text-gray-300">
                                            {{ $log->module }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @php
                                                $actionBadge = match($log->action) {
                                                    'LOGIN' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300',
                                                    'LOGOUT' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                    'LOGIN_FAILED' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-300',
                                                    'CREATE', 'BACKUP_CREATE' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300',
                                                    'UPDATE', 'SETTINGS_UPDATE' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300',
                                                    'DELETE', 'BACKUP_DELETE' => 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300',
                                                    'BACKUP_RESTORE' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/60 dark:text-purple-300',
                                                    default => 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $actionBadge }}">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 max-w-md">
                                            {{ $log->description }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                            <div class="font-mono">{{ $log->ip_address ?? '-' }}</div>
                                            <div class="truncate max-w-[140px] text-[10px]" title="{{ $log->user_agent }}">
                                                {{ Str::limit($log->user_agent, 25) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <button
                                                type="button"
                                                wire:click="viewActivityDetail({{ $log->id }})"
                                                class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/40 dark:text-emerald-300 transition"
                                            >
                                                <x-heroicon-o-eye class="w-3.5 h-3.5 mr-1" />
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <x-heroicon-o-inbox class="w-8 h-8 mx-auto mb-2 text-gray-400" />
                                            Tidak ada catatan log aktivitas yang sesuai dengan filter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($activityLogs->hasPages())
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                Menampilkan {{ $activityLogs->firstItem() ?? 0 }} - {{ $activityLogs->lastItem() ?? 0 }} dari {{ $activityLogs->total() }} catatan
                            </div>
                            <div class="flex space-x-1">
                                @if(!$activityLogs->onFirstPage())
                                    <button
                                        wire:click="setActivityPage({{ $activityLogs->currentPage() - 1 }})"
                                        class="px-3 py-1 text-xs border rounded bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50"
                                    >
                                        Sebelumnya
                                    </button>
                                @endif
                                <span class="px-3 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300 rounded">
                                    Halaman {{ $activityLogs->currentPage() }} dari {{ $activityLogs->lastPage() }}
                                </span>
                                @if($activityLogs->hasMorePages())
                                    <button
                                        wire:click="setActivityPage({{ $activityLogs->currentPage() + 1 }})"
                                        class="px-3 py-1 text-xs border rounded bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50"
                                    >
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
            @php
                $systemLogs = $this->getSystemLogs();
            @endphp
            <div class="space-y-4">
                <!-- System Log Controls -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full md:w-auto flex-1">
                        <!-- File Selector -->
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Pilih Berkas Log</label>
                            <select
                                wire:model.live="selectedLogFile"
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500"
                            >
                                @forelse($logFiles as $fileKey => $fileLabel)
                                    <option value="{{ $fileKey }}">{{ $fileLabel }}</option>
                                @empty
                                    <option value="laravel.log">laravel.log (Kosong)</option>
                                @endforelse
                            </select>
                        </div>

                        <!-- Level Filter -->
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Tingkat Level</label>
                            <select
                                wire:model.live="systemLogLevel"
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500"
                            >
                                <option value="ALL">Semua Level</option>
                                <option value="EMERGENCY">EMERGENCY</option>
                                <option value="ALERT">ALERT</option>
                                <option value="CRITICAL">CRITICAL</option>
                                <option value="ERROR">ERROR</option>
                                <option value="WARNING">WARNING</option>
                                <option value="NOTICE">NOTICE</option>
                                <option value="INFO">INFO</option>
                                <option value="DEBUG">DEBUG</option>
                            </select>
                        </div>

                        <!-- Search -->
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Cari Kata Kunci</label>
                            <input
                                wire:model.live.debounce.350ms="systemLogSearch"
                                type="text"
                                placeholder="Cari exception, path, pesan..."
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500"
                            />
                        </div>
                    </div>

                    <!-- Actions: Download & Clear -->
                    <div class="flex items-center space-x-2 w-full md:w-auto justify-end pt-4 md:pt-0">
                        <button
                            type="button"
                            wire:click="downloadSystemLog"
                            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/40 dark:text-emerald-300 transition border border-emerald-200 dark:border-emerald-800"
                        >
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1.5" />
                            Unduh Berkas Log
                        </button>

                        <button
                            type="button"
                            wire:click="clearSystemLog"
                            wire:confirm="PERINGATAN: Seluruh isi berkas log ini akan dikosongkan secara permanen. Lanjutkan?"
                            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/40 dark:text-rose-400 transition border border-rose-200 dark:border-rose-800"
                        >
                            <x-heroicon-o-trash class="w-4 h-4 mr-1.5" />
                            Kosongkan Log
                        </button>
                    </div>
                </div>

                <!-- System Log Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                        <span>Menampilkan maks 200 entri terbaru dari berkas: <strong class="text-gray-700 dark:text-gray-200 font-mono">{{ $selectedLogFile }}</strong> (Ukuran: {{ $systemLogs['file_size'] }}, Terakhir diubah: {{ $systemLogs['last_modified'] }})</span>
                        <span>Ditemukan: <strong>{{ count($systemLogs['entries']) }}</strong> entri</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-4 py-3">Waktu</th>
                                    <th class="px-4 py-3">Level</th>
                                    <th class="px-4 py-3">Env</th>
                                    <th class="px-4 py-3">Pesan Log</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 font-mono text-xs">
                                @forelse($systemLogs['entries'] as $entry)
                                    @php
                                        $levelClass = match($entry['level']) {
                                            'EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-300',
                                            'WARNING' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300',
                                            'NOTICE', 'INFO' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300',
                                            'DEBUG' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                            default => 'bg-slate-100 text-slate-800',
                                        };
                                    @endphp
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/40 transition">
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            {{ $entry['date'] }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded font-bold text-[11px] {{ $levelClass }}">
                                                {{ $entry['level'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-500">
                                            {{ $entry['env'] }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200 max-w-xl truncate" title="{{ $entry['message'] }}">
                                            {{ $entry['message'] }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center font-sans">
                                            <button
                                                type="button"
                                                wire:click="viewSystemLogDetail({{ $entry['id'] }})"
                                                class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-300 transition"
                                            >
                                                <x-heroicon-o-code-bracket class="w-3.5 h-3.5 mr-1" />
                                                Lihat Trace
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 font-sans">
                                            <x-heroicon-o-check-circle class="w-8 h-8 mx-auto mb-2 text-emerald-500" />
                                            Tidak ada catatan log sistem yang cocok dengan kriteria filter.
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
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-750">
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                                {{ $act->action }}
                            </span>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Detail Log Aktivitas #{{ $act->id }}</h3>
                        </div>
                        <button wire:click="closeActivityDetail" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                        <div class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-700/40 p-4 rounded-xl border border-gray-100 dark:border-gray-600">
                            <div>
                                <span class="text-xs text-gray-500 uppercase block font-semibold">Pengguna</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $act->user_name ?? 'Sistem' }}</span>
                                <span class="text-xs text-gray-500 block">Role: {{ $act->user_role ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase block font-semibold">Waktu</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $act->created_at->format('d F Y, H:i:s') }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase block font-semibold">Modul & Aksi</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $act->module }} / {{ $act->action }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase block font-semibold">Alamat IP</span>
                                <span class="font-mono text-gray-900 dark:text-white">{{ $act->ip_address ?? '-' }}</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs text-gray-500 uppercase block font-semibold mb-1">Deskripsi</span>
                            <div class="p-3 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-100">
                                {{ $act->description }}
                            </div>
                        </div>

                        @if($act->user_agent)
                            <div>
                                <span class="text-xs text-gray-500 uppercase block font-semibold mb-1">User Agent / Perangkat</span>
                                <div class="p-2.5 bg-gray-50 dark:bg-gray-700 rounded-lg text-xs font-mono text-gray-600 dark:text-gray-300 break-all">
                                    {{ $act->user_agent }}
                                </div>
                            </div>
                        @endif

                        @if(!empty($act->properties))
                            <div>
                                <span class="text-xs text-gray-500 uppercase block font-semibold mb-1">Parameter / Payload Perubahan Data</span>
                                <pre class="p-4 bg-gray-900 text-emerald-400 rounded-xl text-xs font-mono overflow-x-auto max-h-60 border border-gray-800">{{ json_encode($act->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endif
                    </div>

                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750 flex justify-end">
                        <button
                            wire:click="closeActivityDetail"
                            class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL: System Log Stack Trace -->
        @if ($selectedSystemLogId && $this->selectedSystemLog)
            @php $sys = $this->selectedSystemLog; @endphp
            <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-750">
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded font-bold text-xs bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-300">
                                {{ $sys['level'] }}
                            </span>
                            <span class="text-xs font-mono text-gray-500">[{{ $sys['date'] }}] {{ $sys['env'] }}</span>
                        </div>
                        <button wire:click="closeSystemLogDetail" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                        <div>
                            <span class="text-xs text-gray-500 uppercase block font-semibold mb-1">Pesan Error / Log</span>
                            <div class="p-3 bg-rose-50 dark:bg-rose-950/40 rounded-lg border border-rose-200 dark:border-rose-900 text-sm font-mono text-rose-900 dark:text-rose-300 break-words">
                                {{ $sys['message'] }}
                            </div>
                        </div>

                        @if(!empty($sys['trace']))
                            <div>
                                <span class="text-xs text-gray-500 uppercase block font-semibold mb-1">Stack Trace & Context</span>
                                <pre class="p-4 bg-gray-950 text-gray-200 rounded-xl text-xs font-mono overflow-x-auto max-h-96 border border-gray-800 whitespace-pre-wrap">{{ $sys['trace'] }}</pre>
                            </div>
                        @else
                            <div class="text-xs text-gray-400 italic">Tidak ada stack trace tambahan pada entri ini.</div>
                        @endif
                    </div>

                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750 flex justify-end">
                        <button
                            wire:click="closeSystemLogDetail"
                            class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
