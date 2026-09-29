<?php

namespace App\Filament\Guru\Widgets;

use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use App\Models\Shift;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class GuruStatusPresensiHariIniWidget extends Widget
{
    protected string $view = 'filament.guru.widgets.guru-status-presensi-hari-ini-widget';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public ?Guru $guru = null;

    public ?Presensi $presensiHariIni = null;

    public ?Shift $shiftHariIni = null;

    public ?PengaturanSekolah $pengaturan = null;

    public bool $isLibur = false;

    public ?string $infoLibur = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->guru = $user?->guru ?? Guru::where('user_id', $user?->id)->first();
        $this->pengaturan = PengaturanSekolah::getSetting();

        $today = Carbon::today();
        $this->isLibur = HariLibur::isLibur($today);
        $this->infoLibur = HariLibur::getInfoLibur($today);

        if ($this->guru) {
            $this->shiftHariIni = $this->guru->shiftPadaTanggal($today->toDateString());
            $this->presensiHariIni = Presensi::where('guru_id', $this->guru->id)
                ->whereDate('tanggal', $today->toDateString())
                ->first();
        }
    }
}
