<?php

namespace App\Filament\Resources\JadwalShiftResource\Pages;

use App\Filament\Resources\JadwalShiftResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJadwalShift extends CreateRecord
{
    protected static string $resource = JadwalShiftResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
