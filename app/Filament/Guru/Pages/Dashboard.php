<?php

namespace App\Filament\Guru\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use UnitEnum;

class Dashboard extends BaseDashboard
{
    protected static string|UnitEnum|null $navigationGroup = 'Menu Utama';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard Portal Guru';

    public function getSubheading(): ?string
    {
        return 'Ringkasan presensi harian, jadwal kerja, dan catatan kedisiplinan Anda';
    }
}
