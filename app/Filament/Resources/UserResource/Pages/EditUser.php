<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => $this->getRecord()->id !== Auth::id() && (! $this->getRecord()->hasRole('super_admin') || Auth::user()?->hasRole('super_admin')))
                ->before(function (DeleteAction $action): void {
                    $record = $this->getRecord();
                    if ($record->hasRole('super_admin') && User::role('super_admin')->count() <= 1) {
                        Notification::make()
                            ->title('Aksi Ditolak')
                            ->body('Tidak dapat menghapus satu-satunya akun Super Administrator!')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }

    protected function afterSave(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
