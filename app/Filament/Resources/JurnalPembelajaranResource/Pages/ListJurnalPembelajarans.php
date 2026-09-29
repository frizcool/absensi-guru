<?php

namespace App\Filament\Resources\JurnalPembelajaranResource\Pages;

use App\Filament\Resources\JurnalPembelajaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJurnalPembelajarans extends ListRecords
{
    protected static string $resource = JurnalPembelajaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Catat Jurnal Baru'),
        ];
    }
}
