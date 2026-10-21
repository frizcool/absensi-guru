<?php

namespace App\Filament\Pages;

use App\Services\BackupService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupRestorePage extends Page
{
    use WithFileUploads;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan & Sistem';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Backup & Restore';

    protected static ?string $slug = 'backup-restore';

    protected static ?string $title = 'Cadangan & Pemulihan Data (Backup & Restore)';

    protected string $view = 'filament.pages.backup-restore';

    // Create Modal state
    public bool $showCreateModal = false;

    public string $backupType = 'full'; // 'full' | 'database' | 'media'

    public string $backupNotes = '';

    // Restore Modal state
    public bool $showRestoreModal = false;

    public ?string $selectedBackupFilename = null;

    public string $confirmRestoreText = '';

    // Upload Modal state
    public bool $showUploadModal = false;

    public $uploadedBackupFile = null;

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin');
    }

    public function openCreateModal(): void
    {
        $this->backupType = 'full';
        $this->backupNotes = '';
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function createBackup(): void
    {
        try {
            $service = app(BackupService::class);
            $result = $service->createBackup(
                type: $this->backupType,
                notes: ! empty($this->backupNotes) ? trim($this->backupNotes) : null,
                user: auth()->user()
            );

            $this->showCreateModal = false;

            Notification::make()
                ->title('Cadangan Berhasil Dibuat!')
                ->body("Berkas {$result['filename']} ({$result['size_formatted']}) siap diunduh atau dipulihkan sewaktu-waktu.")
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Gagal Membuat Cadangan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function openRestoreModal(string $filename): void
    {
        $this->selectedBackupFilename = $filename;
        $this->confirmRestoreText = '';
        $this->showRestoreModal = true;
    }

    public function closeRestoreModal(): void
    {
        $this->showRestoreModal = false;
        $this->selectedBackupFilename = null;
        $this->confirmRestoreText = '';
    }

    public function restoreBackup(): void
    {
        if (strtoupper(trim($this->confirmRestoreText)) !== 'PULIHKAN') {
            Notification::make()
                ->title('Konfirmasi Tidak Sesuai')
                ->body('Ketik kata "PULIHKAN" dengan benar untuk mengonfirmasi proses penimpaan data.')
                ->warning()
                ->send();

            return;
        }

        try {
            $service = app(BackupService::class);
            $result = $service->restoreBackup(
                filename: $this->selectedBackupFilename,
                user: auth()->user()
            );

            $this->closeRestoreModal();

            Notification::make()
                ->title('Pemulihan Selesai!')
                ->body($result['message'].($result['safety_snapshot'] ? " (Snapshot cadangan keselamatan telah dibuat otomatis: {$result['safety_snapshot']})" : ''))
                ->success()
                ->duration(10000)
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Gagal Memulihkan Cadangan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function openUploadModal(): void
    {
        $this->uploadedBackupFile = null;
        $this->showUploadModal = true;
    }

    public function closeUploadModal(): void
    {
        $this->showUploadModal = false;
        $this->uploadedBackupFile = null;
    }

    public function uploadAndRestore(): void
    {
        $this->validate([
            'uploadedBackupFile' => 'required|file|mimes:zip|max:102400', // max 100MB
        ]);

        try {
            $service = app(BackupService::class);
            $result = $service->uploadAndRestore(
                file: $this->uploadedBackupFile,
                user: auth()->user()
            );

            $this->closeUploadModal();

            Notification::make()
                ->title('Unggah & Pemulihan Berhasil!')
                ->body($result['message'])
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Gagal Memulihkan File')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function downloadBackup(string $filename): ?BinaryFileResponse
    {
        $service = app(BackupService::class);
        $response = $service->downloadBackup($filename);

        if (! $response) {
            Notification::make()
                ->title('Gagal')
                ->body('Berkas cadangan tidak ditemukan.')
                ->danger()
                ->send();

            return null;
        }

        return $response;
    }

    public function deleteBackup(string $filename): void
    {
        $service = app(BackupService::class);
        $success = $service->deleteBackup($filename, auth()->user());

        if ($success) {
            Notification::make()
                ->title('Cadangan Dihapus')
                ->body("Berkas {$filename} berhasil dihapus.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Gagal')
                ->body('Berkas cadangan gagal dihapus.')
                ->danger()
                ->send();
        }
    }

    /**
     * Get list of backups.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getBackupsList(): array
    {
        $service = app(BackupService::class);

        return $service->getBackups();
    }

    /**
     * Get summary metrics for backup page.
     *
     * @return array<string, mixed>
     */
    public function getBackupStats(): array
    {
        $backups = $this->getBackupsList();
        $totalSize = array_sum(array_column($backups, 'size'));

        $totalSizeFormatted = $totalSize > 1048576
            ? round($totalSize / 1048576, 2).' MB'
            : round($totalSize / 1024, 2).' KB';

        $dbDriver = config('database.default');
        $dbSize = '0 KB';

        if ($dbDriver === 'sqlite') {
            $sqlitePath = config('database.connections.sqlite.database');
            if (File::exists($sqlitePath)) {
                $dbBytes = filesize($sqlitePath);
                $dbSize = $dbBytes > 1048576
                    ? round($dbBytes / 1048576, 2).' MB'
                    : round($dbBytes / 1024, 2).' KB';
            }
        }

        $latestBackup = ! empty($backups) ? $backups[0]['created_at'] : 'Belum Ada';

        return [
            'total_backups' => count($backups),
            'total_size' => $totalSizeFormatted,
            'db_driver' => strtoupper($dbDriver),
            'db_size' => $dbSize,
            'latest_backup' => $latestBackup,
        ];
    }
}
