<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SystemLogService
{
    /**
     * Get list of log files in storage/logs directory.
     *
     * @return array<string, string>
     */
    public function getLogFiles(): array
    {
        $logPath = storage_path('logs');
        if (! File::exists($logPath)) {
            return [];
        }

        $files = File::glob($logPath . '/*.log');
        $result = [];

        // Sort latest modified first
        usort($files, function ($a, $b) {
            return filemtime($b) <=> filemtime($a);
        });

        foreach ($files as $file) {
            $basename = basename($file);
            $size = round(filesize($file) / 1024, 2);
            $sizeStr = $size > 1024 ? round($size / 1024, 2) . ' MB' : $size . ' KB';
            $result[$basename] = "{$basename} ({$sizeStr})";
        }

        return $result;
    }

    /**
     * Get parsed log entries from a file.
     *
     * @return array{entries: array<int, array<string, mixed>>, total: int, file_size: string, last_modified: string}
     */
    public function getLogs(string $filename = 'laravel.log', ?string $level = null, ?string $search = null, int $limit = 200): array
    {
        $filename = basename($filename);
        $filePath = storage_path("logs/{$filename}");

        if (! File::exists($filePath)) {
            return [
                'entries' => [],
                'total' => 0,
                'file_size' => '0 KB',
                'last_modified' => '-',
            ];
        }

        $fileSize = filesize($filePath);
        $fileSizeFormatted = $fileSize > 1048576
            ? round($fileSize / 1048576, 2) . ' MB'
            : round($fileSize / 1024, 2) . ' KB';

        $lastModified = date('Y-m-d H:i:s', filemtime($filePath));

        // Read file (read last 2MB if file is very large to avoid memory exhaustion)
        $maxReadBytes = 2 * 1024 * 1024;
        if ($fileSize > $maxReadBytes) {
            $handle = fopen($filePath, 'r');
            fseek($handle, -$maxReadBytes, SEEK_END);
            $content = fread($handle, $maxReadBytes);
            fclose($handle);
            // discard partial first line if any
            $firstNewline = strpos($content, "\n");
            if ($firstNewline !== false) {
                $content = substr($content, $firstNewline + 1);
            }
        } else {
            $content = File::get($filePath);
        }

        $pattern = '/^\[(?<date>\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:[+-]\d{2}:?\d{2}|Z)?)\] (?<env>[a-zA-Z0-9_\-]+)\.(?<level>[A-Z]+): (?<message>[^\n]*)(?:\n(?<trace>(?:(?!^\[\d{4}-\d{2}-\d{2}).|\n)*))?/m';

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $entries = [];
        $id = 1;

        // Iterate backwards (latest logs first)
        for ($i = count($matches) - 1; $i >= 0; $i--) {
            $match = $matches[$i];
            $entryLevel = strtoupper(trim($match['level']));
            $entryMessage = trim($match['message']);
            $entryTrace = isset($match['trace']) ? trim($match['trace']) : '';

            // Apply Level filter
            if ($level && $level !== 'ALL' && $entryLevel !== strtoupper($level)) {
                continue;
            }

            // Apply Search filter
            if ($search) {
                $searchLower = strtolower($search);
                if (! str_contains(strtolower($entryMessage), $searchLower) && ! str_contains(strtolower($entryTrace), $searchLower)) {
                    continue;
                }
            }

            $entries[] = [
                'id' => $id++,
                'date' => $match['date'],
                'env' => $match['env'],
                'level' => $entryLevel,
                'message' => $entryMessage,
                'trace' => $entryTrace,
            ];

            if (count($entries) >= $limit) {
                break;
            }
        }

        return [
            'entries' => $entries,
            'total' => count($matches),
            'file_size' => $fileSizeFormatted,
            'last_modified' => $lastModified,
        ];
    }

    /**
     * Clear content of specified log file.
     */
    public function clearLog(string $filename = 'laravel.log'): bool
    {
        $filename = basename($filename);
        $filePath = storage_path("logs/{$filename}");

        if (! File::exists($filePath)) {
            return false;
        }

        File::put($filePath, '');

        ActivityLog::record(
            action: 'CLEAR_LOG',
            module: 'Sistem',
            description: "Super Admin mengosongkan berkas log: {$filename}",
            properties: ['filename' => $filename]
        );

        return true;
    }

    /**
     * Download the specified log file.
     */
    public function downloadLog(string $filename = 'laravel.log'): ?BinaryFileResponse
    {
        $filename = basename($filename);
        $filePath = storage_path("logs/{$filename}");

        if (! File::exists($filePath)) {
            return null;
        }

        return response()->download($filePath, $filename);
    }
}
