<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Resources\GuruResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditGuru extends EditRecord
{
    protected static string $resource = GuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('resetDevice')
                ->label('Reset HP')
                ->icon('heroicon-o-device-phone-mobile')
                ->color('danger')
                ->visible(fn (): bool => ! empty($this->record->device_id))
                ->requiresConfirmation()
                ->modalHeading('Reset Ikatan Perangkat HP')
                ->modalDescription(fn (): string => "Apakah Anda yakin ingin melepas ikatan perangkat HP untuk {$this->record->nama}? Guru dapat mendaftarkan HP baru pada saat presensi berikutnya.")
                ->action(function (): void {
                    $this->record->resetDeviceId();
                    $this->fillForm();
                    Notification::make()
                        ->title('Perangkat Berhasil Direset')
                        ->body("Ikatan perangkat guru {$this->record->nama} telah dilepas.")
                        ->success()
                        ->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
