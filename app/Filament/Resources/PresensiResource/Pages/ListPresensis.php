<?php

namespace App\Filament\Resources\PresensiResource\Pages;

use App\Exports\LaporanRincianPresensiExport;
use App\Filament\Resources\PresensiResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListPresensis extends ListRecords
{
    protected static string $resource = PresensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return (new LaporanRincianPresensiExport(
                        bulan: (int) now()->month,
                        tahun: (int) now()->year,
                        statusKepegawaian: 'semua',
                        shiftId: null,
                        search: '',
                    ))->download();
                }),
        ];
    }
}
