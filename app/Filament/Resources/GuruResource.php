<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuruResource\Pages;
use App\Models\Guru;
use App\Models\Shift;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components as FormComponents;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Data Guru';

    protected static ?string $modelLabel = 'Guru & Tendik';

    protected static ?string $pluralModelLabel = 'Daftar Guru & Tendik';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Section::make('Profil & Identitas')
                    ->columns(3)
                    ->schema([
                        FormComponents\FileUpload::make('foto')
                            ->label('Foto Profil')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('guru-foto')
                            ->imageEditor()
                            ->columnSpan(1),

                        Group::make([
                            FormComponents\TextInput::make('nama')
                                ->label('Nama Lengkap & Gelar')
                                ->required()
                                ->maxLength(150),
                            FormComponents\Select::make('jenis_kelamin')
                                ->label('Jenis Kelamin')
                                ->options([
                                    'L' => 'Laki-laki',
                                    'P' => 'Perempuan',
                                ])
                                ->required(),
                            FormComponents\TextInput::make('no_hp')
                                ->label('No. WhatsApp / HP')
                                ->tel()
                                ->maxLength(25),
                        ])->columnSpan(2)->columns(2),
                    ]),

                Section::make('Data Kepegawaian')
                    ->columns(3)
                    ->schema([
                        FormComponents\TextInput::make('nip')
                            ->label('NIP')
                            ->maxLength(30)
                            ->helperText('Kosongkan jika belum memiliki NIP'),
                        FormComponents\TextInput::make('nuptk')
                            ->label('NUPTK')
                            ->maxLength(30),
                        FormComponents\Select::make('status_kepegawaian')
                            ->label('Status Kepegawaian')
                            ->options([
                                'pns' => 'PNS',
                                'pppk' => 'PPPK',
                                'non_pns' => 'Non-PNS / Honorer',
                            ])
                            ->required(),
                        FormComponents\TextInput::make('pangkat_golongan')
                            ->label('Pangkat / Golongan')
                            ->placeholder('Contoh: Penata Muda / III.a')
                            ->maxLength(100),
                        FormComponents\TextInput::make('jabatan')
                            ->label('Jabatan')
                            ->placeholder('Contoh: Guru Muda, Kepala Sekolah')
                            ->maxLength(100),
                        FormComponents\TextInput::make('jenis_guru')
                            ->label('Tugas Mengajar / Jenis Guru')
                            ->placeholder('Contoh: Guru Kelas V, Guru PJOK')
                            ->maxLength(150),
                        FormComponents\TextInput::make('jumlah_jam')
                            ->label('Jam Mengajar / Minggu')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(60)
                            ->default(24),
                        FormComponents\TextInput::make('kuota_cuti_tahunan')
                            ->label('Hak Kuota Cuti (Hari / Tahun)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(90)
                            ->default(12)
                            ->helperText('Standar ASN/PPPK adalah 12 hari cuti tahunan.'),
                        FormComponents\Toggle::make('aktif')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Jika non-aktif, guru tidak dihitung pada rekap presensi harian.'),
                    ]),

                Section::make('Pengaturan Presensi & Akun Login')
                    ->columns(3)
                    ->schema([
                        FormComponents\Select::make('shift_id')
                            ->label('Shift Kerja Default')
                            ->relationship('shift', 'nama')
                            ->required()
                            ->helperText('Jam kerja baku untuk guru ini.'),
                        FormComponents\Select::make('user_id')
                            ->label('Akun Login Pengguna')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Hubungkan akun login agar guru bisa melakukan presensi mandiri.'),
                        FormComponents\TextInput::make('device_id')
                            ->label('ID Perangkat Terikat (Device Binding)')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Belum ada perangkat terdaftar')
                            ->helperText('Otomatis terkunci saat guru pertama kali presensi. Jika guru ganti HP, gunakan tombol "Reset HP" di tabel atau bagian atas halaman edit ini.'),
                    ]),

                Section::make('Pola Shift Mingguan (Senin - Minggu)')
                    ->description('Tentukan shift jika guru memiliki jam kerja berbeda pada hari tertentu (misalnya: Selasa & Rabu Shift Pagi, selain itu Full Day). Kosongkan (Ikuti Shift Default) jika hari tersebut mengikuti Shift Kerja Default di atas.')
                    ->collapsible()
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'md' => 4,
                        'lg' => 7,
                    ])
                    ->schema([
                        FormComponents\Select::make('jadwal_mingguan.senin')
                            ->label('Senin')
                            ->placeholder('Ikuti Shift Default')
                            ->options(fn () => self::getShiftOptionsWithLibur())
                            ->nullable(),
                        FormComponents\Select::make('jadwal_mingguan.selasa')
                            ->label('Selasa')
                            ->placeholder('Ikuti Shift Default')
                            ->options(fn () => self::getShiftOptionsWithLibur())
                            ->nullable(),
                        FormComponents\Select::make('jadwal_mingguan.rabu')
                            ->label('Rabu')
                            ->placeholder('Ikuti Shift Default')
                            ->options(fn () => self::getShiftOptionsWithLibur())
                            ->nullable(),
                        FormComponents\Select::make('jadwal_mingguan.kamis')
                            ->label('Kamis')
                            ->placeholder('Ikuti Shift Default')
                            ->options(fn () => self::getShiftOptionsWithLibur())
                            ->nullable(),
                        FormComponents\Select::make('jadwal_mingguan.jumat')
                            ->label('Jumat')
                            ->placeholder('Ikuti Shift Default')
                            ->options(fn () => self::getShiftOptionsWithLibur())
                            ->nullable(),
                        FormComponents\Select::make('jadwal_mingguan.sabtu')
                            ->label('Sabtu')
                            ->placeholder('Ikuti Shift Default')
                            ->options(fn () => self::getShiftOptionsWithLibur())
                            ->nullable(),
                        FormComponents\Select::make('jadwal_mingguan.minggu')
                            ->label('Minggu')
                            ->placeholder('Ikuti Shift Default')
                            ->options(fn () => self::getShiftOptionsWithLibur())
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('nama', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => $record->foto_url),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Guru')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Guru $record) => $record->nip ? 'NIP: '.$record->nip : ($record->nuptk ? 'NUPTK: '.$record->nuptk : 'Non-NIP')),
                Tables\Columns\TextColumn::make('status_kepegawaian')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pns' => 'PNS', 'pppk' => 'PPPK', 'non_pns' => 'Non-PNS', default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pns' => 'success', 'pppk' => 'info', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('jenis_guru')
                    ->label('Tugas')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('shift.nama')
                    ->label('Shift Default')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('pola_shift')
                    ->label('Pola Shift')
                    ->getStateUsing(fn (Guru $record) => $record->ringkasanJadwalMingguan())
                    ->badge()
                    ->color(fn (string $state) => str_starts_with($state, 'Default:') ? 'gray' : 'info')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Akun Login')
                    ->badge()
                    ->color(fn (Guru $record) => $record->user_id ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state, Guru $record) => $record->user_id ? ($record->nip ? 'NIP: '.$record->nip : $record->user?->name) : 'Belum Ada Akun')
                    ->description(fn (Guru $record) => $record->user_id ? 'Password: NIP' : 'Perlu Disinkron'),
                Tables\Columns\TextColumn::make('device_id')
                    ->label('Perangkat HP')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Terikat' : 'Bebas')
                    ->color(fn ($state) => $state ? 'info' : 'gray')
                    ->description(fn ($state) => $state ? substr($state, 0, 8).'...' : 'Belum ada')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sisa_cuti_tahun_ini')
                    ->label('Sisa Cuti')
                    ->badge()
                    ->color(fn (int $state) => $state <= 2 ? 'danger' : 'success')
                    ->formatStateUsing(fn (int $state) => "{$state} Hari")
                    ->toggleable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('WhatsApp')
                    ->icon('heroicon-m-phone')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('aktif')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_kepegawaian')
                    ->options(['pns' => 'PNS', 'pppk' => 'PPPK', 'non_pns' => 'Non-PNS']),
                Tables\Filters\SelectFilter::make('shift_id')
                    ->relationship('shift', 'nama')
                    ->label('Shift'),
                Tables\Filters\TernaryFilter::make('aktif')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Action::make('aturShiftMingguan')
                    ->label('Atur Shift')
                    ->icon('heroicon-o-calendar-days')
                    ->color('info')
                    ->modalHeading(fn (Guru $record) => 'Atur Pola Shift Mingguan: '.$record->nama)
                    ->modalDescription(fn (Guru $record) => 'Tentukan shift kerja per hari untuk guru ini. Shift default saat ini: '.($record->shift?->nama ?? '-'))
                    ->form([
                        FormComponents\Select::make('shift_id')
                            ->label('Shift Kerja Default')
                            ->relationship('shift', 'nama')
                            ->required(),
                        Section::make('Pola Shift Mingguan (Senin - Minggu)')
                            ->columns([
                                'default' => 1,
                                'sm' => 2,
                                'md' => 4,
                            ])
                            ->schema([
                                FormComponents\Select::make('jadwal_mingguan.senin')
                                    ->label('Senin')
                                    ->placeholder('Ikuti Shift Default')
                                    ->options(fn () => self::getShiftOptionsWithLibur())
                                    ->nullable(),
                                FormComponents\Select::make('jadwal_mingguan.selasa')
                                    ->label('Selasa')
                                    ->placeholder('Ikuti Shift Default')
                                    ->options(fn () => self::getShiftOptionsWithLibur())
                                    ->nullable(),
                                FormComponents\Select::make('jadwal_mingguan.rabu')
                                    ->label('Rabu')
                                    ->placeholder('Ikuti Shift Default')
                                    ->options(fn () => self::getShiftOptionsWithLibur())
                                    ->nullable(),
                                FormComponents\Select::make('jadwal_mingguan.kamis')
                                    ->label('Kamis')
                                    ->placeholder('Ikuti Shift Default')
                                    ->options(fn () => self::getShiftOptionsWithLibur())
                                    ->nullable(),
                                FormComponents\Select::make('jadwal_mingguan.jumat')
                                    ->label('Jumat')
                                    ->placeholder('Ikuti Shift Default')
                                    ->options(fn () => self::getShiftOptionsWithLibur())
                                    ->nullable(),
                                FormComponents\Select::make('jadwal_mingguan.sabtu')
                                    ->label('Sabtu')
                                    ->placeholder('Ikuti Shift Default')
                                    ->options(fn () => self::getShiftOptionsWithLibur())
                                    ->nullable(),
                                FormComponents\Select::make('jadwal_mingguan.minggu')
                                    ->label('Minggu')
                                    ->placeholder('Ikuti Shift Default')
                                    ->options(fn () => self::getShiftOptionsWithLibur())
                                    ->nullable(),
                            ]),
                    ])
                    ->fillForm(fn (Guru $record) => [
                        'shift_id' => $record->shift_id,
                        'jadwal_mingguan' => $record->jadwal_mingguan ?? [],
                    ])
                    ->action(function (Guru $record, array $data) {
                        $record->update([
                            'shift_id' => $data['shift_id'],
                            'jadwal_mingguan' => $data['jadwal_mingguan'] ?? null,
                        ]);
                        Notification::make()
                            ->title('Pola Shift Berhasil Disimpan')
                            ->body("Pola shift mingguan untuk {$record->nama} telah diperbarui.")
                            ->success()
                            ->send();
                    }),
                Action::make('resetDevice')
                    ->label('Reset HP')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('danger')
                    ->visible(fn (Guru $record) => ! empty($record->device_id))
                    ->requiresConfirmation()
                    ->modalHeading('Reset Ikatan Perangkat HP')
                    ->modalDescription(fn (Guru $record) => "Apakah Anda yakin ingin melepas ikatan perangkat HP untuk {$record->nama}? Guru dapat mendaftarkan HP baru pada saat presensi berikutnya.")
                    ->action(function (Guru $record) {
                        $record->resetDeviceId();
                        Notification::make()
                            ->title('Perangkat Berhasil Direset')
                            ->body("Ikatan perangkat guru {$record->nama} telah dilepas.")
                            ->success()
                            ->send();
                    }),
                Action::make('resetPasswordNip')
                    ->label('Reset Pass ke NIP')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password ke NIP')
                    ->modalDescription(fn (Guru $record) => "Apakah Anda yakin ingin mereset password akun guru {$record->nama} kembali ke standar NIP ({$record->nip})?")
                    ->modalSubmitActionLabel('Ya, Reset Password')
                    ->action(function (Guru $record) {
                        $record->resetPasswordKeNip();
                        Notification::make()
                            ->title('Password Berhasil Direset')
                            ->body("Password login untuk {$record->nama} telah diatur ulang menjadi NIP: {$record->nip}")
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulkSyncUserNip')
                        ->label('Sinkron Akun & Reset Password NIP')
                        ->icon('heroicon-o-user-group')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $guru) {
                                $guru->resetPasswordKeNip();
                            }
                            Notification::make()
                                ->title('Sinkronisasi Akun Berhasil')
                                ->body(count($records).' akun guru telah diperbarui dengan password standar NIP.')
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('bulkResetDevice')
                        ->label('Reset Ikatan Perangkat HP')
                        ->icon('heroicon-o-device-phone-mobile')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Reset Ikatan Perangkat Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin melepas ikatan HP untuk guru-guru yang dipilih? Guru yang bersangkutan dapat mendaftarkan HP barunya pada saat presensi berikutnya.')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $guru) {
                                if ($guru->device_id) {
                                    $guru->resetDeviceId();
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title('Reset Perangkat Berhasil')
                                ->body("Ikatan perangkat {$count} guru berhasil dilepas.")
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getShiftOptionsWithLibur(): array
    {
        $shifts = Shift::all()->mapWithKeys(function ($shift) {
            $jamMasuk = $shift->jam_masuk?->format('H:i') ?? '-';
            $jamPulang = $shift->jam_pulang?->format('H:i') ?? '-';

            return [(string) $shift->id => "{$shift->nama} ({$jamMasuk} - {$jamPulang})"];
        })->toArray();

        return $shifts + [
            'libur' => '🏖️ Libur Rutin',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGurus::route('/'),
            'create' => Pages\CreateGuru::route('/create'),
            'edit' => Pages\EditGuru::route('/{record}/edit'),
        ];
    }
}
