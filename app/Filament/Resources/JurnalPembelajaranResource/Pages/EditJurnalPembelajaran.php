<?php

namespace App\Filament\Resources\JurnalPembelajaranResource\Pages;

use App\Filament\Resources\JurnalPembelajaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJurnalPembelajaran extends EditRecord
{
    protected static string $resource = JurnalPembelajaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
