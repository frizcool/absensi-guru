<?php

namespace App\Filament\Resources\HariLiburResource\Pages;

use App\Filament\Resources\HariLiburResource;
use App\Services\HariLiburService;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListHariLiburs extends ListRecords
{
    protected static string $resource = HariLiburResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncLiburNasional')
                ->label('Tarik Libur Nasional (API)')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->modalHeading('Sinkronisasi Hari Libur Nasional Resmi RI')
                ->modalDescription('Sistem akan otomatis mengambil dan memperbarui daftar hari libur nasional serta cuti bersama resmi dari kalender nasional Indonesia.')
                ->form([
                    Select::make('tahun')
                        ->label('Pilih Tahun')
                        ->options([
                            now()->year - 1 => (string) (now()->year - 1),
                            now()->year => now()->year.' (Tahun Berjalan)',
                            now()->year + 1 => (string) (now()->year + 1),
                        ])
                        ->default(now()->year)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $tahun = (int) $data['tahun'];
                    $res = HariLiburService::syncLiburNasional($tahun);

                    Notification::make()
                        ->title('Sinkronisasi Hari Libur Berhasil')
                        ->body("Berhasil menyinkronkan {$res['total']} hari libur nasional & cuti bersama tahun {$tahun} ({$res['inserted']} data baru, {$res['updated']} diperbarui).")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make()->label('Tambah Hari Libur Manual'),
        ];
    }
}
