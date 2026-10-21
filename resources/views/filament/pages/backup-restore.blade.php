<x-filament-panels::page>
    @php
        $stats = $this->getBackupStats();
        $backups = $this->getBackupsList();
    @endphp

    <div class="space-y-6">
        <!-- Top Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-lg">
                    <x-heroicon-o-circle-stack class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total Arsip Cadangan</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['total_backups'] }} file</div>
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-lg">
                    <x-heroicon-o-archive-box class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total Ukuran Penyimpanan</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['total_size'] }}</div>
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 rounded-lg">
                    <x-heroicon-o-circle-stack class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Database Aktif ({{ $stats['db_driver'] }})</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['db_size'] }}</div>
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-lg">
                    <x-heroicon-o-clock class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Cadangan Terakhir</div>
                    <div class="text-sm font-bold text-gray-900 dark:text-white truncate max-w-[180px]">{{ $stats['latest_backup'] }}</div>
                </div>
            </div>
        </div>

        <!-- Action Header -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Daftar Cadangan Tersimpan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Pencadangan rutin direkomendasikan sebelum pembaruan aplikasi atau pergantian tahun ajaran sekolah.</p>
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <button
                    type="button"
                    wire:click="openUploadModal"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition shadow-sm"
                >
                    <x-heroicon-o-cloud-arrow-up class="w-5 h-5 mr-1.5 text-gray-500 dark:text-gray-400" />
                    Upload & Restore
                </button>

                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm"
                >
                    <x-heroicon-o-plus class="w-5 h-5 mr-1.5" />
                    Buat Backup Baru
                </button>
            </div>
        </div>

        <!-- Table of Backups -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3">Nama Berkas Cadangan</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Ukuran</th>
                            <th class="px-4 py-3">Waktu Pembuatan</th>
                            <th class="px-4 py-3">Pembuat / Catatan</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($backups as $b)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/40 transition {{ $b['is_safety'] ? 'bg-amber-50/40 dark:bg-amber-950/20' : '' }}">
                                <td class="px-4 py-3">
                                    <div class="font-mono text-xs font-bold text-gray-900 dark:text-white flex items-center space-x-2">
                                        <x-heroicon-o-document-arrow-down class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                        <span>{{ $b['filename'] }}</span>
                                    </div>
                                    @if($b['is_safety'])
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 mt-1">
                                            Snapshot Otomatis Keselamatan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $typeBadge = match($b['type']) {
                                            'full' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300',
                                            'database' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300',
                                            'media' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/60 dark:text-purple-300',
                                            'safety' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                        $typeLabel = match($b['type']) {
                                            'full' => 'Penuh (DB + Media)',
                                            'database' => 'Database Saja',
                                            'media' => 'Media Saja',
                                            'safety' => 'Snapshot Safety',
                                            default => strtoupper($b['type']),
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $typeBadge }}">
                                        {{ $typeLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap font-mono text-xs text-gray-700 dark:text-gray-300">
                                    {{ $b['size_formatted'] }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                    {{ $b['created_at'] }}
                                </td>
                                <td class="px-4 py-3 text-xs max-w-xs">
                                    <div class="font-medium text-gray-800 dark:text-gray-200">{{ $b['created_by'] }}</div>
                                    <div class="text-gray-500 dark:text-gray-400 truncate">{{ $b['notes'] }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Download -->
                                        <button
                                            type="button"
                                            wire:click="downloadBackup('{{ $b['filename'] }}')"
                                            title="Unduh Berkas"
                                            class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/40 transition"
                                        >
                                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                                        </button>

                                        <!-- Restore -->
                                        <button
                                            type="button"
                                            wire:click="openRestoreModal('{{ $b['filename'] }}')"
                                            title="Pulihkan (Restore) Data"
                                            class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-900/40 transition"
                                        >
                                            <x-heroicon-o-arrow-path class="w-4 h-4" />
                                        </button>

                                        <!-- Delete -->
                                        <button
                                            type="button"
                                            wire:click="deleteBackup('{{ $b['filename'] }}')"
                                            wire:confirm="Hapus berkas cadangan {{ $b['filename'] }} secara permanen?"
                                            title="Hapus Berkas"
                                            class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-900/40 transition"
                                        >
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <x-heroicon-o-circle-stack class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                                    <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">Belum Ada Berkas Cadangan</div>
                                    <div class="text-xs text-gray-400 mt-1">Klik tombol "Buat Backup Baru" di atas untuk membuat cadangan pertama Anda.</div>
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
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-750">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-plus-circle class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Buat Cadangan Sistem Baru</h3>
                        </div>
                        <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                    </div>

                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Pilih Komponen Cadangan</label>
                            <div class="space-y-2">
                                <label class="flex items-start p-3 border rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ $backupType === 'full' ? 'border-emerald-500 bg-emerald-50/30 dark:bg-emerald-950/20' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input type="radio" wire:model.live="backupType" value="full" class="mt-1 text-emerald-600 focus:ring-emerald-500" />
                                    <div class="ml-3">
                                        <span class="block text-sm font-semibold text-gray-900 dark:text-white">Cadangan Penuh (Database & Media / Uploads)</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Mencakup semua data presensi, tabel akun, foto profil guru, foto absensi, dan logo sekolah. (Sangat Direkomendasikan)</span>
                                    </div>
                                </label>

                                <label class="flex items-start p-3 border rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ $backupType === 'database' ? 'border-emerald-500 bg-emerald-50/30 dark:bg-emerald-950/20' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input type="radio" wire:model.live="backupType" value="database" class="mt-1 text-emerald-600 focus:ring-emerald-500" />
                                    <div class="ml-3">
                                        <span class="block text-sm font-semibold text-gray-900 dark:text-white">Hanya Database</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Hanya mencadangkan basis data (cepat dan ukuran berkas kecil).</span>
                                    </div>
                                </label>

                                <label class="flex items-start p-3 border rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ $backupType === 'media' ? 'border-emerald-500 bg-emerald-50/30 dark:bg-emerald-950/20' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input type="radio" wire:model.live="backupType" value="media" class="mt-1 text-emerald-600 focus:ring-emerald-500" />
                                    <div class="ml-3">
                                        <span class="block text-sm font-semibold text-gray-900 dark:text-white">Hanya Berkas Media (Storage)</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Hanya mencadangkan berkas foto yang diunggah ke storage aplikasi.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea
                                wire:model="backupNotes"
                                rows="2"
                                placeholder="Contoh: Backup akhir semester genap 2026..."
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500"
                            ></textarea>
                        </div>
                    </div>

                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750 flex justify-end space-x-3">
                        <button
                            type="button"
                            wire:click="closeCreateModal"
                            class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 transition"
                        >
                            Batal
                        </button>

                        <button
                            type="button"
                            wire:click="createBackup"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 transition"
                        >
                            <span wire:loading.remove wire:target="createBackup">Mulai Pencadangan</span>
                            <span wire:loading wire:target="createBackup" class="flex items-center space-x-2">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL: Restore Konfirmasi -->
        @if ($showRestoreModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-rose-50 dark:bg-rose-950/40">
                        <div class="flex items-center space-x-2 text-rose-700 dark:text-rose-400">
                            <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                            <h3 class="text-base font-bold">Konfirmasi Pemulihan Cadangan (Restore)</h3>
                        </div>
                        <button wire:click="closeRestoreModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 rounded-xl text-xs text-amber-900 dark:text-amber-200 space-y-2">
                            <div class="font-bold text-sm flex items-center space-x-1.5">
                                <x-heroicon-o-shield-check class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                                <span>Perhatian: Data Saat Ini Akan Ditimpa</span>
                            </div>
                            <p>Proses pemulihan akan menimpa basis data & berkas media saat ini dengan isi arsip: <strong class="font-mono">{{ $selectedBackupFilename }}</strong>.</p>
                            <p class="font-semibold text-emerald-700 dark:text-emerald-400">✓ Sistem akan membuat snapshot cadangan keselamatan secara otomatis sebelum proses penimpaan dimulai.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                Ketik kata <strong class="text-rose-600">PULIHKAN</strong> untuk mengonfirmasi:
                            </label>
                            <input
                                wire:model="confirmRestoreText"
                                type="text"
                                placeholder="PULIHKAN"
                                class="w-full text-sm font-bold uppercase rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-rose-500 focus:border-rose-500"
                            />
                        </div>
                    </div>

                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750 flex justify-end space-x-3">
                        <button
                            type="button"
                            wire:click="closeRestoreModal"
                            class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 transition"
                        >
                            Batal
                        </button>

                        <button
                            type="button"
                            wire:click="restoreBackup"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg text-white bg-rose-600 hover:bg-rose-700 transition"
                        >
                            <span wire:loading.remove wire:target="restoreBackup">Pulihkan Data Sekarang</span>
                            <span wire:loading wire:target="restoreBackup" class="flex items-center space-x-2">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memulihkan...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL: Upload & Restore -->
        @if ($showUploadModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-750">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-cloud-arrow-up class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Upload & Restore Arsip Cadangan</h3>
                        </div>
                        <button wire:click="closeUploadModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pilih berkas cadangan dengan ekstensi <strong class="font-mono">.zip</strong> yang sebelumnya pernah Anda unduh dari sistem ini.</p>

                        <div class="p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl text-center">
                            <input
                                type="file"
                                wire:model="uploadedBackupFile"
                                accept=".zip"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950 dark:file:text-emerald-300"
                            />
                            @error('uploadedBackupFile')
                                <div class="text-xs text-rose-600 mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-[11px] text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 p-3 rounded-lg border border-amber-200 dark:border-amber-900">
                            <strong>Catatan:</strong> Setelah berkas berhasil diunggah, sistem otomatis membuat snapshot keselamatan dan memulihkan basis data serta berkas media yang ada di dalam arsip ZIP tersebut.
                        </div>
                    </div>

                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750 flex justify-end space-x-3">
                        <button
                            type="button"
                            wire:click="closeUploadModal"
                            class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 transition"
                        >
                            Batal
                        </button>

                        <button
                            type="button"
                            wire:click="uploadAndRestore"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition"
                        >
                            <span wire:loading.remove wire:target="uploadAndRestore">Unggah & Pulihkan</span>
                            <span wire:loading wire:target="uploadAndRestore" class="flex items-center space-x-2">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mengunggah...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
