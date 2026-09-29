<?php

namespace App\Filament\Resources\PengajuanIzinResource\Pages;

use App\Filament\Resources\PengajuanIzinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengajuanIzins extends ListRecords
{
    protected static string $resource = PengajuanIzinResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
