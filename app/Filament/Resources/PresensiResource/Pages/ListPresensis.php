<?php

namespace App\Filament\Resources\PresensiResource\Pages;

use App\Filament\Resources\PresensiResource;
use Filament\Resources\Pages\ListRecords;

class ListPresensis extends ListRecords
{
    protected static string $resource = PresensiResource::class;

    // Tambahkan header action export ke Excel/PDF di sini kalau butuh,
    // mis. pakai pxlrbt/filament-excel: ExportAction::make()->exports([...])
}
