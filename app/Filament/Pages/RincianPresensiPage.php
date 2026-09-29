<?php

namespace App\Filament\Pages;

use App\Models\Presensi;
use App\Models\Shift;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class RincianPresensiPage extends Page
{
    use HasPageShield;
    use WithPagination;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Presensi & Kehadiran';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Rincian Presensi';

    protected static ?string $title = 'Rincian Jam Presensi Harian';

    protected string $view = 'filament.pages.rincian-presensi-page';

    public int $bulan;

    public int $tahun;

    public string $statusKepegawaian = 'semua';

    public ?int $shiftId = null;

    public string $search = '';

    public bool $cetakSemua = false;

    public static function canAccess(): bool
    {
        $user = Filament::auth()?->user();

        if (! $user) {
            return false;
        }

        if ($user->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah'])) {
            return true;
        }

        $permission = static::getPagePermission();

        return $permission ? $user->can($permission) : parent::canAccess();
    }

    public function mount(): void
    {
        $currentYear = (int) now()->year;
        $month = filter_var(request()->query('bulan'), FILTER_VALIDATE_INT);
        $year = filter_var(request()->query('tahun'), FILTER_VALIDATE_INT);
        $status = request()->query('statusKepegawaian');
        $shiftId = filter_var(request()->query('shiftId'), FILTER_VALIDATE_INT);
        $search = request()->query('search');
        $this->cetakSemua = request()->boolean('cetak');

        $this->bulan = $month !== false && $month >= 1 && $month <= 12
            ? $month
            : (int) now()->month;
        $this->tahun = $year !== false && $year >= $currentYear - 2 && $year <= $currentYear + 1
            ? $year
            : $currentYear;
        $this->statusKepegawaian = in_array($status, ['semua', 'pns', 'pppk', 'non_pns'], true)
            ? $status
            : 'semua';
        $this->shiftId = $shiftId !== false && Shift::query()->whereKey($shiftId)->exists()
            ? $shiftId
            : null;
        $this->search = is_string($search) ? mb_substr(trim($search), 0, 100) : '';
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['bulan', 'tahun', 'statusKepegawaian', 'shiftId', 'search'], true)) {
            $this->resetPage();
        }
    }

    public function getDaftarBulanProperty(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }

    public function getDaftarTahunProperty(): array
    {
        $currentYear = (int) now()->year;

        return [
            $currentYear - 2 => $currentYear - 2,
            $currentYear - 1 => $currentYear - 1,
            $currentYear => $currentYear,
            $currentYear + 1 => $currentYear + 1,
        ];
    }

    public function getDaftarShiftProperty()
    {
        return Shift::query()->orderBy('nama')->get();
    }

    public function getDaftarRincianProperty(): EloquentCollection|LengthAwarePaginator
    {
        $query = $this->getRincianQuery();

        return $this->cetakSemua ? $query->get() : $query->paginate(50);
    }

    public function getTotalRincianProperty(): int
    {
        $rincian = $this->daftarRincian;

        return $rincian instanceof LengthAwarePaginator ? $rincian->total() : $rincian->count();
    }

    private function getRincianQuery(): Builder
    {
        $currentYear = (int) now()->year;
        $bulan = min(max($this->bulan, 1), 12);
        $tahun = min(max($this->tahun, $currentYear - 2), $currentYear + 1);
        $status = in_array($this->statusKepegawaian, ['pns', 'pppk', 'non_pns'], true)
            ? $this->statusKepegawaian
            : null;
        $search = mb_substr(trim($this->search), 0, 100);
        $startDate = now()->setDate($tahun, $bulan, 1)->startOfMonth()->toDateString();
        $endDate = now()->setDate($tahun, $bulan, 1)->endOfMonth()->toDateString();

        return Presensi::query()
            ->with(['guru', 'shift'])
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->whereHas('guru', function (Builder $query) use ($status, $search): void {
                $query->where('aktif', true);

                if ($status) {
                    $query->where('status_kepegawaian', $status);
                }

                if ($this->shiftId) {
                    $query->where('shift_id', $this->shiftId);
                }

                if ($search !== '') {
                    $searchTerm = '%'.$search.'%';
                    $query->where(function (Builder $query) use ($searchTerm): void {
                        $query->where('nama', 'like', $searchTerm)
                            ->orWhere('nip', 'like', $searchTerm)
                            ->orWhere('jabatan', 'like', $searchTerm);
                    });
                }
            })
            ->orderBy('tanggal')
            ->orderBy('guru_id');
    }

    public function getUrlLaporanProperty(): string
    {
        return LaporanPresensiPage::getUrl([
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'statusKepegawaian' => $this->statusKepegawaian,
            'shiftId' => $this->shiftId,
            'search' => $this->search,
        ], panel: 'admin');
    }

    public function getUrlCetakProperty(): string
    {
        return self::getUrl([
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'statusKepegawaian' => $this->statusKepegawaian,
            'shiftId' => $this->shiftId,
            'search' => $this->search,
            'cetak' => 1,
        ], panel: 'admin');
    }

    public function getStatusKehadiranLabel(Presensi $presensi): string
    {
        return match ($presensi->status_kehadiran) {
            'hadir' => 'Hadir',
            'dinas_luar' => 'Dinas luar',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'cuti' => 'Cuti',
            'alpa' => 'Alpa',
            default => str($presensi->status_kehadiran)->replace('_', ' ')->title()->toString(),
        };
    }
}
