<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components as FormComponents;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Lab404\Impersonate\Services\ImpersonateManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan & Sistem';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (! Auth::user()?->hasRole('super_admin')) {
            $query->whereDoesntHave('roles', fn (Builder $q): Builder => $q->where('name', 'super_admin'));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Section::make('Informasi Pengguna')
                    ->description('Lengkapi data profil dan akun pengguna sistem')
                    ->columns(2)
                    ->schema([
                        FormComponents\FileUpload::make('avatar_url')
                            ->label('Foto Profil / Avatar')
                            ->avatar()
                            ->image()
                            ->disk('public')
                            ->directory('avatars')
                            ->imageEditor()
                            ->columnSpan(2),

                        FormComponents\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Masukkan nama lengkap pengguna')
                            ->required()
                            ->maxLength(255),

                        FormComponents\TextInput::make('email')
                            ->label('Alamat Email')
                            ->placeholder('contoh@sekolah.sch.id')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        FormComponents\TextInput::make('nip')
                            ->label('NIP (Nomor Induk Pegawai)')
                            ->placeholder('Masukkan NIP jika ada')
                            ->maxLength(30)
                            ->helperText('Kosongkan jika bukan guru/staf ber-NIP'),

                        FormComponents\Select::make('roles')
                            ->label('Peran / Hak Akses (Role)')
                            ->relationship(
                                name: 'roles',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query): Builder {
                                    if (! Auth::user()?->hasRole('super_admin')) {
                                        return $query->where('name', '!=', 'super_admin');
                                    }

                                    return $query;
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn (Model $record): string => match ($record->name) {
                                'super_admin' => 'Super Administrator (Master)',
                                'admin' => 'Administrator Sekolah',
                                'kepala_sekolah' => 'Kepala Sekolah',
                                'guru' => 'Guru & Tenaga Pendidik',
                                default => str($record->name)->replace('_', ' ')->title(),
                            })
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('Pilih satu atau lebih hak akses untuk pengguna ini'),
                    ]),

                Section::make('Keamanan & Password')
                    ->description('Pengaturan kata sandi untuk login')
                    ->columns(2)
                    ->schema([
                        FormComponents\TextInput::make('password')
                            ->label('Password')
                            ->placeholder(fn (string $operation): string => $operation === 'create' ? 'Masukkan password baru' : 'Kosongkan jika tidak ingin mengubah password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->rule(PasswordRule::default())
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Biarkan kosong jika Anda tidak ingin mengubah password pengguna ini.' : null),

                        FormComponents\TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->placeholder('Ulangi password di atas')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->same('password')
                            ->dehydrated(false)
                            ->helperText('Pastikan password konfirmasi cocok dengan password yang dimasukkan'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\ImageColumn::make('avatar_url')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn (User $record): string => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&background=10b981&color=ffffff'),

                TableColumns\TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (User $record): string => $record->nip ? "NIP: {$record->nip}" : ($record->guru ? 'Guru' : '-')),

                TableColumns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),

                TableColumns\TextColumn::make('roles.name')
                    ->label('Hak Akses / Peran')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin Sekolah',
                        'kepala_sekolah' => 'Kepala Sekolah',
                        'guru' => 'Guru',
                        default => str($state)->replace('_', ' ')->title(),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'kepala_sekolah' => 'info',
                        'guru' => 'success',
                        default => 'gray',
                    }),

                TableColumns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Filter Peran')
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query): Builder {
                            if (! Auth::user()?->hasRole('super_admin')) {
                                return $query->where('name', '!=', 'super_admin');
                            }

                            return $query;
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn (Model $record): string => match ($record->name) {
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin Sekolah',
                        'kepala_sekolah' => 'Kepala Sekolah',
                        'guru' => 'Guru',
                        default => str($record->name)->replace('_', ' ')->title(),
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail Pengguna'),

                EditAction::make()
                    ->iconButton()
                    ->color('success')
                    ->tooltip('Ubah Data Pengguna'),

                Action::make('changePassword')
                    ->iconButton()
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->tooltip('Ganti Password')
                    ->modalHeading(fn (User $record): string => "Ganti Password: {$record->name}")
                    ->modalDescription('Masukkan password baru untuk pengguna ini.')
                    ->schema([
                        FormComponents\TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(PasswordRule::default()),
                        FormComponents\TextInput::make('new_password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('new_password'),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->password = $data['new_password'];
                        $record->save();

                        Notification::make()
                            ->title('Password Berhasil Diperbarui')
                            ->body("Password untuk {$record->name} berhasil diperbarui.")
                            ->success()
                            ->send();
                    }),

                DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->tooltip('Hapus Pengguna')
                    ->visible(fn (User $record): bool => $record->id !== Auth::id() && (! $record->hasRole('super_admin') || Auth::user()?->hasRole('super_admin')))
                    ->before(function (DeleteAction $action, User $record): void {
                        if ($record->hasRole('super_admin') && User::role('super_admin')->count() <= 1) {
                            Notification::make()
                                ->title('Aksi Ditolak')
                                ->body('Tidak dapat menghapus satu-satunya akun Super Administrator!')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),

                // Action::make('impersonate')
                //     ->iconButton()
                //     ->icon('heroicon-o-user-circle')
                //     ->color('info')
                //     ->tooltip('Login Sebagai Pengguna Ini')
                //     ->requiresConfirmation()
                //     ->modalHeading('Login Sebagai Pengguna')
                //     ->modalDescription(fn (User $record): string => "Apakah Anda yakin ingin login sebagai {$record->name}?")
                //     ->visible(fn (User $record): bool => Auth::id() !== $record->id && (! $record->hasRole('super_admin') || Auth::user()?->hasRole('super_admin')))
                //     ->action(function (User $record) {
                //         if (class_exists(ImpersonateManager::class)) {
                //             app(ImpersonateManager::class)->take(Auth::user(), $record, 'web');
                //         } else {
                //             Auth::login($record);
                //         }

                //         if ($record->hasRole('guru') || $record->guru !== null) {
                //             return redirect('/guru');
                //         }

                //         return redirect('/sekolahku/panel');
                //     }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $currentUserId = Auth::id();
                            $isSuperAdmin = Auth::user()?->hasRole('super_admin');

                            foreach ($records as $record) {
                                if ($record->id === $currentUserId) {
                                    continue;
                                }

                                if ($record->hasRole('super_admin') && ! $isSuperAdmin) {
                                    continue;
                                }

                                if ($record->hasRole('super_admin') && User::role('super_admin')->count() <= 1) {
                                    continue;
                                }

                                $record->delete();
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
