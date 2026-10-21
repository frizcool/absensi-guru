<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (! File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Get all backups ordered by latest first.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getBackups(): array
    {
        if (! File::exists($this->backupDir)) {
            return [];
        }

        $files = File::glob($this->backupDir.'/*.zip');
        $backups = [];

        // Sort latest first
        usort($files, function ($a, $b) {
            return filemtime($b) <=> filemtime($a);
        });

        foreach ($files as $filePath) {
            $filename = basename($filePath);
            $size = filesize($filePath);
            $sizeFormatted = $size > 1048576
                ? round($size / 1048576, 2).' MB'
                : round($size / 1024, 2).' KB';

            $meta = null;
            $zip = new ZipArchive;
            if ($zip->open($filePath) === true) {
                $metaContent = $zip->getFromName('backup-meta.json');
                if ($metaContent) {
                    $meta = json_decode($metaContent, true);
                }
                $zip->close();
            }

            $createdAt = $meta['created_at'] ?? date('Y-m-d H:i:s', filemtime($filePath));
            $type = $meta['type'] ?? (str_contains($filename, 'safety_snapshot') ? 'safety' : 'full');
            $notes = $meta['notes'] ?? (str_contains($filename, 'safety_snapshot') ? 'Snapshot Otomatis Sebelum Restore' : '-');
            $createdBy = $meta['created_by'] ?? 'Sistem';

            $backups[] = [
                'filename' => $filename,
                'path' => $filePath,
                'size' => $size,
                'size_formatted' => $sizeFormatted,
                'created_at' => $createdAt,
                'type' => $type,
                'notes' => $notes,
                'created_by' => $createdBy,
                'is_safety' => str_starts_with($filename, 'safety_snapshot_'),
            ];
        }

        return $backups;
    }

    /**
     * Create a backup archive.
     *
     * @return array<string, mixed>
     */
    public function createBackup(string $type = 'full', ?string $notes = null, ?User $user = null): array
    {
        $timestamp = date('Ymd_His');
        $random = Str::lower(Str::random(6));
        $filename = "backup_{$type}_{$timestamp}_{$random}.zip";
        $filePath = "{$this->backupDir}/{$filename}";

        $zip = new ZipArchive;
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Gagal membuat arsip cadangan zip di: {$filePath}");
        }

        $filesCount = 0;
        $dbDriver = config('database.default');

        // 1. Database backup
        if (in_array($type, ['full', 'database'], true)) {
            if ($dbDriver === 'sqlite') {
                $sqlitePath = config('database.connections.sqlite.database');
                if (File::exists($sqlitePath)) {
                    $zip->addFile($sqlitePath, 'database/database.sqlite');
                    $filesCount++;
                }
            } else {
                // Dump SQL for MySQL / PostgreSQL via queries
                $sqlContent = $this->generateSqlDump();
                $zip->addFromString('database/database.sql', $sqlContent);
                $filesCount++;
            }
        }

        // 2. Storage / Media backup
        if (in_array($type, ['full', 'media'], true)) {
            $publicStoragePath = storage_path('app/public');
            if (File::exists($publicStoragePath)) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($publicStoragePath, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $file) {
                    if (! $file->isDir()) {
                        $realPath = $file->getRealPath();
                        $relativePath = 'storage/'.substr($realPath, strlen($publicStoragePath) + 1);
                        $zip->addFile($realPath, str_replace('\\', '/', $relativePath));
                        $filesCount++;
                    }
                }
            }
        }

        // 3. Metadata
        $creatorName = $user?->name ?? auth()->user()?->name ?? 'Super Admin';
        $metadata = [
            'version' => 1,
            'app_name' => config('app.name'),
            'type' => $type,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $creatorName,
            'notes' => $notes,
            'db_driver' => $dbDriver,
            'files_count' => $filesCount,
        ];

        $zip->addFromString('backup-meta.json', json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $zip->close();

        $size = filesize($filePath);
        $sizeFormatted = $size > 1048576
            ? round($size / 1048576, 2).' MB'
            : round($size / 1024, 2).' KB';

        ActivityLog::record(
            action: 'BACKUP_CREATE',
            module: 'Backup',
            description: "Cadangan sistem dibuat: {$filename} (Tipe: {$type}, Ukuran: {$sizeFormatted})",
            properties: [
                'filename' => $filename,
                'type' => $type,
                'size' => $sizeFormatted,
                'notes' => $notes,
            ],
            user: $user
        );

        return [
            'filename' => $filename,
            'path' => $filePath,
            'size_formatted' => $sizeFormatted,
            'type' => $type,
            'files_count' => $filesCount,
        ];
    }

    /**
     * Restore database and storage from a backup file.
     *
     * @return array<string, mixed>
     */
    public function restoreBackup(string $filename, ?User $user = null): array
    {
        $filename = basename($filename);
        $filePath = "{$this->backupDir}/{$filename}";

        if (! File::exists($filePath)) {
            throw new \RuntimeException("Berkas cadangan tidak ditemukan: {$filename}");
        }

        // SAFETY FIRST: Create automatic safety snapshot of current database
        $safetySnapshot = $this->createSafetySnapshot();

        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            throw new \RuntimeException("Berkas arsip ZIP cadangan rusak atau tidak dapat dibuka: {$filename}");
        }

        $metaContent = $zip->getFromName('backup-meta.json');
        $meta = $metaContent ? json_decode($metaContent, true) : null;
        $restoredItems = [];

        // 1. Restore Database if present
        $sqliteContent = $zip->getFromName('database/database.sqlite');
        $sqlContent = $zip->getFromName('database/database.sql');

        if ($sqliteContent !== false) {
            $sqlitePath = config('database.connections.sqlite.database');
            // Disconnect PDO before replacing file
            DB::disconnect();

            // Write extracted SQLite file
            File::put($sqlitePath, $sqliteContent);

            // Reconnect
            DB::reconnect();
            $restoredItems[] = 'Database SQLite';
        } elseif ($sqlContent !== false) {
            // Restore SQL statements
            DB::unprepared($sqlContent);
            $restoredItems[] = 'Database SQL';
        }

        // 2. Restore Storage if present
        $extractedFiles = 0;
        $publicStoragePath = storage_path('app/public');

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryName = $zip->getNameIndex($i);
            if (str_starts_with($entryName, 'storage/') && ! str_ends_with($entryName, '/')) {
                $subPath = substr($entryName, strlen('storage/'));
                $targetFile = "{$publicStoragePath}/{$subPath}";
                $targetDir = dirname($targetFile);

                if (! File::exists($targetDir)) {
                    File::makeDirectory($targetDir, 0755, true);
                }

                $fileData = $zip->getFromIndex($i);
                if ($fileData !== false) {
                    File::put($targetFile, $fileData);
                    $extractedFiles++;
                }
            }
        }

        if ($extractedFiles > 0) {
            $restoredItems[] = "Berkas Media/Storage ({$extractedFiles} file)";
        }

        $zip->close();

        // Clear application caches
        try {
            Artisan::call('cache:clear');
        } catch (\Throwable $e) {
            // ignore cache clear errors
        }

        ActivityLog::record(
            action: 'BACKUP_RESTORE',
            module: 'Backup',
            description: "Super Admin memulihkan cadangan: {$filename} (".implode(', ', $restoredItems).')',
            properties: [
                'filename' => $filename,
                'restored_items' => $restoredItems,
                'safety_snapshot' => $safetySnapshot['filename'] ?? null,
            ],
            user: $user
        );

        return [
            'success' => true,
            'message' => 'Cadangan berhasil dipulihkan: '.implode(', ', $restoredItems),
            'safety_snapshot' => $safetySnapshot['filename'] ?? null,
        ];
    }

    /**
     * Upload an external backup file and restore it.
     *
     * @return array<string, mixed>
     */
    public function uploadAndRestore(UploadedFile $file, ?User $user = null): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if ($extension !== 'zip') {
            throw new \InvalidArgumentException('Format berkas harus berupa arsip .zip');
        }

        $timestamp = date('Ymd_His');
        $random = Str::lower(Str::random(4));
        $savedFilename = "uploaded_backup_{$timestamp}_{$random}.zip";
        $file->move($this->backupDir, $savedFilename);

        return $this->restoreBackup($savedFilename, $user);
    }

    /**
     * Delete a backup file.
     */
    public function deleteBackup(string $filename, ?User $user = null): bool
    {
        $filename = basename($filename);
        $filePath = "{$this->backupDir}/{$filename}";

        if (! File::exists($filePath)) {
            return false;
        }

        File::delete($filePath);

        ActivityLog::record(
            action: 'BACKUP_DELETE',
            module: 'Backup',
            description: "Super Admin menghapus berkas cadangan: {$filename}",
            properties: ['filename' => $filename],
            user: $user
        );

        return true;
    }

    /**
     * Download a backup file.
     */
    public function downloadBackup(string $filename): ?BinaryFileResponse
    {
        $filename = basename($filename);
        $filePath = "{$this->backupDir}/{$filename}";

        if (! File::exists($filePath)) {
            return null;
        }

        return response()->download($filePath, $filename);
    }

    /**
     * Automatically create a safety snapshot before restoring.
     *
     * @return array<string, mixed>
     */
    protected function createSafetySnapshot(): array
    {
        $timestamp = date('Ymd_His');
        $filename = "safety_snapshot_{$timestamp}.zip";
        $filePath = "{$this->backupDir}/{$filename}";

        $zip = new ZipArchive;
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $dbDriver = config('database.default');
            if ($dbDriver === 'sqlite') {
                $sqlitePath = config('database.connections.sqlite.database');
                if (File::exists($sqlitePath)) {
                    $zip->addFile($sqlitePath, 'database/database.sqlite');
                }
            } else {
                $zip->addFromString('database/database.sql', $this->generateSqlDump());
            }

            $meta = [
                'type' => 'safety',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 'Sistem (Snapshot Keselamatan Otomatis)',
                'notes' => 'Cadangan keselamatan otomatis sebelum pemulihan dijalankan.',
            ];
            $zip->addFromString('backup-meta.json', json_encode($meta, JSON_PRETTY_PRINT));
            $zip->close();
        }

        return [
            'filename' => $filename,
            'path' => $filePath,
        ];
    }

    /**
     * Simple SQL table dump generator for MySQL / SQLite fallback.
     */
    protected function generateSqlDump(): string
    {
        $output = "-- Absensi Guru Database Dump\n";
        $output .= '-- Generated: '.date('Y-m-d H:i:s')."\n\n";

        try {
            $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        } catch (\Throwable $e) {
            $tables = [];
        }

        foreach ($tables as $table) {
            $rows = DB::table($table)->get();
            if ($rows->isEmpty()) {
                continue;
            }

            $output .= "-- Table: {$table}\n";
            foreach ($rows as $row) {
                $array = (array) $row;
                $keys = array_keys($array);
                $values = array_map(function ($val) {
                    if (is_null($val)) {
                        return 'NULL';
                    }

                    return "'".addslashes((string) $val)."'";
                }, array_values($array));

                $output .= sprintf(
                    "INSERT INTO `%s` (`%s`) VALUES (%s);\n",
                    $table,
                    implode('`, `', $keys),
                    implode(', ', $values)
                );
            }
            $output .= "\n";
        }

        return $output;
    }
}
