<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use App\Services\SystemLogService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LogMonitoringPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan & Sistem';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Monitoring Log';

    protected static ?string $title = 'Pusat Monitoring Log & Audit Sistem';

    protected string $view = 'filament.pages.log-monitoring';

    // Tabs
    public string $activeTab = 'activity'; // 'activity' | 'system'

    // Activity Log Filters
    public string $activitySearch = '';

    public string $activityModule = 'all';

    public string $activityAction = 'all';

    public string $activityPeriod = 'all'; // 'today', '7_days', '30_days', 'all'

    public int $activityPage = 1;

    public int $activityPerPage = 20;

    public ?int $selectedActivityId = null;

    // System Log Filters
    public string $selectedLogFile = 'laravel.log';

    public string $systemLogLevel = 'ALL';

    public string $systemLogSearch = '';

    public ?int $selectedSystemLogId = null;

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function updatedActivitySearch(): void
    {
        $this->activityPage = 1;
    }

    public function updatedActivityModule(): void
    {
        $this->activityPage = 1;
    }

    public function updatedActivityAction(): void
    {
        $this->activityPage = 1;
    }

    public function updatedActivityPeriod(): void
    {
        $this->activityPage = 1;
    }

    public function setActivityPage(int $page): void
    {
        $this->activityPage = max(1, $page);
    }

    public function viewActivityDetail(int $id): void
    {
        $this->selectedActivityId = $id;
    }

    public function closeActivityDetail(): void
    {
        $this->selectedActivityId = null;
    }

    public function viewSystemLogDetail(int $id): void
    {
        $this->selectedSystemLogId = $id;
    }

    public function closeSystemLogDetail(): void
    {
        $this->selectedSystemLogId = null;
    }

    public function clearOldActivityLogs(int $days = 30): void
    {
        $cutoffDate = Carbon::now()->subDays($days);
        $deleted = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        ActivityLog::record(
            action: 'CLEAR_LOG',
            module: 'Sistem',
            description: "Super Admin membersihkan {$deleted} catatan log aktivitas yang berusia lebih dari {$days} hari."
        );

        Notification::make()
            ->title('Log Berhasil Dibersihkan')
            ->body("Sebanyak {$deleted} catatan log lama berhasil dihapus.")
            ->success()
            ->send();
    }

    public function clearSystemLog(): void
    {
        $service = app(SystemLogService::class);
        $success = $service->clearLog($this->selectedLogFile);

        if ($success) {
            $this->selectedSystemLogId = null;
            Notification::make()
                ->title('Berkas Log Dikosongkan')
                ->body("Berkas {$this->selectedLogFile} berhasil dibersihkan.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Gagal')
                ->body('Berkas log tidak dapat ditemukan.')
                ->danger()
                ->send();
        }
    }

    public function downloadSystemLog(): ?BinaryFileResponse
    {
        $service = app(SystemLogService::class);
        $response = $service->downloadLog($this->selectedLogFile);

        if (! $response) {
            Notification::make()
                ->title('Gagal Mengunduh')
                ->body('Berkas log tidak ditemukan.')
                ->danger()
                ->send();

            return null;
        }

        return $response;
    }

    /**
     * Get paginated activity logs.
     */
    public function getActivityLogs(): LengthAwarePaginator
    {
        $query = ActivityLog::query()->latest('id');

        if (! empty($this->activitySearch)) {
            $search = '%' . trim($this->activitySearch) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', $search)
                    ->orWhere('user_name', 'like', $search)
                    ->orWhere('ip_address', 'like', $search);
            });
        }

        if ($this->activityModule !== 'all') {
            $query->where('module', $this->activityModule);
        }

        if ($this->activityAction !== 'all') {
            $query->where('action', $this->activityAction);
        }

        if ($this->activityPeriod === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($this->activityPeriod === '7_days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($this->activityPeriod === '30_days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }

        return $query->paginate($this->activityPerPage, ['*'], 'page', $this->activityPage);
    }

    /**
     * Get distinct modules and actions for filters.
     *
     * @return array{modules: array<int, string>, actions: array<int, string>}
     */
    public function getFilterOptions(): array
    {
        return [
            'modules' => ActivityLog::distinct()->pluck('module')->filter()->values()->toArray(),
            'actions' => ActivityLog::distinct()->pluck('action')->filter()->values()->toArray(),
        ];
    }

    /**
     * Get system logs.
     *
     * @return array{entries: array<int, array<string, mixed>>, total: int, file_size: string, last_modified: string}
     */
    public function getSystemLogs(): array
    {
        $service = app(SystemLogService::class);

        return $service->getLogs(
            filename: $this->selectedLogFile,
            level: $this->systemLogLevel,
            search: $this->systemLogSearch,
            limit: 200
        );
    }

    /**
     * Get log file list.
     *
     * @return array<string, string>
     */
    public function getLogFiles(): array
    {
        $service = app(SystemLogService::class);

        return $service->getLogFiles();
    }

    /**
     * Get summary metrics for the page header.
     *
     * @return array<string, mixed>
     */
    public function getSummaryStats(): array
    {
        $today = Carbon::today();

        $activityCountToday = ActivityLog::whereDate('created_at', $today)->count();
        $loginSuccessToday = ActivityLog::whereDate('created_at', $today)->where('action', 'LOGIN')->count();
        $loginFailedToday = ActivityLog::whereDate('created_at', $today)->where('action', 'LOGIN_FAILED')->count();

        $systemLogs = $this->getSystemLogs();
        $errorCount = 0;
        foreach ($systemLogs['entries'] as $entry) {
            if (in_array($entry['level'], ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR'], true)) {
                $errorCount++;
            }
        }

        return [
            'activity_today' => $activityCountToday,
            'login_success_today' => $loginSuccessToday,
            'login_failed_today' => $loginFailedToday,
            'system_errors' => $errorCount,
            'log_file_size' => $systemLogs['file_size'] ?? '0 KB',
            'last_modified' => $systemLogs['last_modified'] ?? '-',
        ];
    }

    public function getSelectedActivityProperty(): ?ActivityLog
    {
        return $this->selectedActivityId ? ActivityLog::find($this->selectedActivityId) : null;
    }

    public function getSelectedSystemLogProperty(): ?array
    {
        if (! $this->selectedSystemLogId) {
            return null;
        }

        $logs = $this->getSystemLogs();
        foreach ($logs['entries'] as $entry) {
            if ($entry['id'] === $this->selectedSystemLogId) {
                return $entry;
            }
        }

        return null;
    }
}
