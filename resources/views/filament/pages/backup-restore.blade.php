<x-filament-panels::page>
    @php
        $stats = $this->getBackupStats();
        $backups = $this->getBackupsList();
    @endphp

    <style>
        .br-wrapper {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .br-stat-card {
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
        .br-stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .dark .br-stat-card {
            background: #111827;
            border-color: #1f2937;
            box-shadow: none;
        }

        .br-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .br-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            text-align: left;
            font-size: 13px;
        }
        .br-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        .dark .br-table th {
            background: #182234;
            color: #94a3b8;
            border-bottom-color: #1f2937;
        }
        .br-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .dark .br-table td {
            border-bottom-color: #1e293b;
        }
        .br-table tbody tr:hover {
            background: #f8fafc;
        }
        .dark .br-table tbody tr:hover {
            background: #182234;
        }

        .br-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
    </style>

    <div class="br-wrapper">
        <!-- 1. STATS METRICS (Clean 4-column layout) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Metric 1: Total Backups -->
            <div class="br-stat-card">
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Cadangan</span>
                    <span class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_backups'] }} file</span>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium mt-1">Tersimpan di storage</span>
                </div>
                <div class="br-icon-box bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                    <x-heroicon-o-circle-stack class="w-6 h-6" />
                </div>
            </div>

            <!-- Metric 2: Ukuran Arsip -->
            <div class="br-stat-card">
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Ukuran Arsip</span>
                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['total_size'] }}</span>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Kompresi format .zip</span>
                </div>
                <div class="br-icon-box bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                    <x-heroicon-o-archive-box class="w-6 h-6" />
                </div>
            </div>

            <!-- Metric 3: Database Aktif -->
            <div class="br-stat-card">
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Database Aktif</span>
                    <div class="flex items-baseline space-x-1.5 mt-1">
                        <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['db_driver'] }}</span>
                        <span class="text-xs text-gray-400 font-mono">({{ $stats['db_size'] }})</span>
                    </div>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Basis data sistem sekolah</span>
                </div>
                <div class="br-icon-box bg-purple-50 text-purple-600 dark:bg-purple-950/50 dark:text-purple-400">
                    <x-heroicon-o-circle-stack class="w-6 h-6" />
                </div>
            </div>

            <!-- Metric 4: Cadangan Terakhir -->
            <div class="br-stat-card">
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cadangan Terakhir</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white truncate max-w-[170px] mt-2" title="{{ $stats['latest_backup'] }}">
                        {{ $stats['latest_backup'] }}
                    </span>
                    <span class="text-[11px] text-amber-600 dark:text-amber-400 font-medium mt-1">Snapshot riwayat</span>
                </div>
                <div class="br-icon-box bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                    <x-heroicon-o-clock class="w-6 h-6" />
                </div>
            </div>
        </div>

        <!-- 2. ACTION BANNER -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Arsip Cadangan Sistem & Snapshot Keselamatan</span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Data dapat diunduh untuk penyimpanan off-site atau dipulihkan kembali sewaktu-waktu.
                </p>
            </div>

            <div class="flex items-center space-x-2.5 w-full sm:w-auto justify-end">
                <button
                    type="button"
                    wire:click="openUploadModal"
                    class="inline-flex items-center px-3.5 py-2 text-xs font-semibold rounded-xl text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition shadow-sm"
                >
                    <x-heroicon-o-cloud-arrow-up class="w-4 h-4 mr-1.5 text-gray-500 dark:text-gray-400" />
                    Upload & Restore
                </button>

                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm"
                >
                    <x-heroicon-o-plus class="w-4 h-4 mr-1.5" />
                    Buat Backup Baru
                </button>
            </div>
        </div>

        <!-- 3. TABLE OF BACKUPS -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="br-table">
                    <thead>
                        <tr>
                            <th>Nama Berkas Cadangan</th>
                            <th style="width: 140px;">Tipe</th>
                            <th style="width: 110px;">Ukuran</th>
                            <th style="width: 160px;">Waktu Pembuatan</th>
                            <th>Pembuat / Catatan</th>
                            <th style="width: 110px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $b)
                            <tr class="{{ $b['is_safety'] ? 'bg-amber-50/30 dark:bg-amber-950/10' : '' }}">
                                <td>
                                    <div class="flex items-center space-x-2.5">
                                        <div class="p-2 rounded-lg {{ $b['is_safety'] ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/60' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                            <x-heroicon-o-document-arrow-down class="w-4 h-4" />
                                        </div>
                                        <div>
                                            <div class="font-mono text-xs font-bold text-gray-900 dark:text-white">{{ $b['filename'] }}</div>
                                            @if($b['is_safety'])
                                                <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 mt-0.5">
                                                    Snapshot Otomatis Sebelum Restore
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $typeBadge = match($b['type']) {
                                            'full' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                                            'database' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300',
                                            'media' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300',
                                            'safety' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                        $typeLabel = match($b['type']) {
                                            'full' => 'Penuh (DB + Media)',
                                            'database' => 'Database Saja',
                                            'media' => 'Media Saja',
                                            'safety' => 'Safety Snapshot',
                                            default => strtoupper($b['type']),
                                        };
                                    @endphp
                                    <span class="br-badge {{ $typeBadge }}">
                                        {{ $typeLabel }}
                                    </span>
                                </td>
                                <td>
                                    <span class="font-mono text-xs font-bold text-gray-800 dark:text-gray-200">{{ $b['size_formatted'] }}</span>
                                </td>
                                <td>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $b['created_at'] }}</span>
                                </td>
                                <td>
                                    <div class="text-xs font-semibold text-gray-900 dark:text-white">{{ $b['created_by'] }}</div>
                                    <div class="text-xs text-gray-400 truncate max-w-xs">{{ $b['notes'] }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <!-- Download -->
                                        <button
                                            type="button"
                                            wire:click="downloadBackup('{{ $b['filename'] }}')"
                                            class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-950/50 transition"
                                            title="Unduh Berkas"
                                        >
                                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                                        </button>

                                        <!-- Restore -->
                                        <button
                                            type="button"
                                            wire:click="openRestoreModal('{{ $b['filename'] }}')"
                                            class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-950/50 transition"
                                            title="Pulihkan (Restore) Data"
                                        >
                                            <x-heroicon-o-arrow-path class="w-4 h-4" />
                                        </button>

                                        <!-- Delete -->
                                        <button
                                            type="button"
                                            wire:click="deleteBackup('{{ $b['filename'] }}')"
                                            wire:confirm="Hapus berkas cadangan {{ $b['filename'] }} secara permanen?"
                                            class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/50 transition"
                                            title="Hapus Berkas"
                                        >
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-14 text-center text-gray-500 dark:text-gray-400">
                                    <x-heroicon-o-circle-stack class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" />
                                    <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Belum Ada Berkas Cadangan</div>
                                    <div class="text-[11px] text-gray-400 mt-1">Klik tombol "Buat Backup Baru" di atas untuk mencadangkan sistem.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: Buat Backup Baru -->
        @if ($showCreateModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-gray-700 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50/80 dark:bg-gray-750">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-circle-stack class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Buat Cadangan Baru</h3>
                        </div>
                        <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-5 space-y-4 text-xs">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Komponen Cadangan</label>
                            <div class="space-y-2">
                                <label class="flex items-start p-3 border rounded-xl cursor-pointer transition {{ $backupType === 'full' ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input type="radio" wire:model.live="backupType" value="full" class="mt-0.5 text-emerald-600 focus:ring-emerald-500" />
                                    <div class="ml-2.5">
                                        <span class="block font-bold text-gray-900 dark:text-white">Cadangan Penuh (Database & File Media)</span>
                                        <span class="block text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Semua data tabel presensi, akun, dan berkas foto profil / absensi.</span>
                                    </div>
                                </label>

                                <label class="flex items-start p-3 border rounded-xl cursor-pointer transition {{ $backupType === 'database' ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input type="radio" wire:model.live="backupType" value="database" class="mt-0.5 text-emerald-600 focus:ring-emerald-500" />
                                    <div class="ml-2.5">
                                        <span class="block font-bold text-gray-900 dark:text-white">Hanya Database</span>
                                        <span class="block text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Hanya basis data SQL/SQLite (cepat & ringkas).</span>
                                    </div>
                                </label>

                                <label class="flex items-start p-3 border rounded-xl cursor-pointer transition {{ $backupType === 'media' ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input type="radio" wire:model.live="backupType" value="media" class="mt-0.5 text-emerald-600 focus:ring-emerald-500" />
                                    <div class="ml-2.5">
                                        <span class="block font-bold text-gray-900 dark:text-white">Hanya Berkas Media (Storage)</span>
                                        <span class="block text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Khusus foto dan lampiran yang diunggah.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Catatan Tambahan (Opsional)</label>
                            <input
                                wire:model="backupNotes"
                                type="text"
                                placeholder="Contoh: Sebelum update semester baru..."
                                class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 h-9"
                            />
                        </div>
                    </div>

                    <div class="p-3.5 border-t border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-750 flex justify-end space-x-2">
                        <button
                            type="button"
                            wire:click="closeCreateModal"
                            class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-white transition"
                        >
                            Batal
                        </button>

                        <button
                            type="button"
                            wire:click="createBackup"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-1.5 text-xs font-semibold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 transition"
                        >
                            <span wire:loading.remove wire:target="createBackup">Mulai Pencadangan</span>
                            <span wire:loading wire:target="createBackup">Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL: Restore Konfirmasi -->
        @if ($showRestoreModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-gray-700 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-rose-50/80 dark:bg-rose-950/40">
                        <div class="flex items-center space-x-2 text-rose-700 dark:text-rose-400">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                            <h3 class="text-sm font-bold">Konfirmasi Pemulihan (Restore)</h3>
                        </div>
                        <button wire:click="closeRestoreModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-5 space-y-3.5 text-xs">
                        <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/80 rounded-xl text-amber-900 dark:text-amber-200 space-y-1.5">
                            <div class="font-bold flex items-center gap-1.5">
                                <x-heroicon-o-shield-check class="w-4 h-4 text-emerald-600" />
                                <span>Perlindungan Otomatis Aktif</span>
                            </div>
                            <p class="leading-relaxed">Pemulihan dari berkas <strong class="font-mono text-gray-900 dark:text-white">{{ $selectedBackupFilename }}</strong> akan menimpa data aktif saat ini.</p>
                            <p class="text-emerald-700 dark:text-emerald-400 font-semibold">✓ Sistem otomatis membuat safety snapshot sebelum proses dimulai.</p>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                Ketik kata <span class="text-rose-600 font-black">PULIHKAN</span> untuk melanjutkan:
                            </label>
                            <input
                                wire:model="confirmRestoreText"
                                type="text"
                                placeholder="PULIHKAN"
                                class="w-full text-xs font-bold uppercase rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-rose-500 focus:border-rose-500 h-9"
                            />
                        </div>
                    </div>

                    <div class="p-3.5 border-t border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-750 flex justify-end space-x-2">
                        <button
                            type="button"
                            wire:click="closeRestoreModal"
                            class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-white transition"
                        >
                            Batal
                        </button>

                        <button
                            type="button"
                            wire:click="restoreBackup"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-1.5 text-xs font-semibold rounded-xl text-white bg-rose-600 hover:bg-rose-700 transition"
                        >
                            <span wire:loading.remove wire:target="restoreBackup">Pulihkan Data</span>
                            <span wire:loading wire:target="restoreBackup">Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL: Upload & Restore -->
        @if ($showUploadModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-gray-700 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50/80 dark:bg-gray-750">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-cloud-arrow-up class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Upload & Restore Arsip</h3>
                        </div>
                        <button wire:click="closeUploadModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-5 space-y-3.5 text-xs">
                        <p class="text-gray-500 dark:text-gray-400">Pilih berkas cadangan <strong class="font-mono">.zip</strong> yang sebelumnya diunduh dari sistem.</p>

                        <div class="p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl text-center">
                            <input
                                type="file"
                                wire:model="uploadedBackupFile"
                                accept=".zip"
                                class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950 dark:file:text-emerald-300"
                            />
                            @error('uploadedBackupFile')
                                <div class="text-[11px] text-rose-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-[11px] text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
                            Sistem otomatis membuat cadangan keselamatan sebelum menimpa data dengan file yang diunggah.
                        </div>
                    </div>

                    <div class="p-3.5 border-t border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-750 flex justify-end space-x-2">
                        <button
                            type="button"
                            wire:click="closeUploadModal"
                            class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-white transition"
                        >
                            Batal
                        </button>

                        <button
                            type="button"
                            wire:click="uploadAndRestore"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-1.5 text-xs font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition"
                        >
                            <span wire:loading.remove wire:target="uploadAndRestore">Unggah & Pulihkan</span>
                            <span wire:loading wire:target="uploadAndRestore">Mengunggah...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
