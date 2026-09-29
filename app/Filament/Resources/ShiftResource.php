<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShiftResource\Pages;
use App\Models\Shift;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components as FormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Shift Kerja';

    protected static ?string $modelLabel = 'Shift Kerja';

    protected static ?string $pluralModelLabel = 'Daftar Shift Kerja';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Section::make('Identitas Shift')
                    ->schema([
                        FormComponents\TextInput::make('nama')
                            ->label('Nama Shift')
                            ->placeholder('Contoh: Shift Pagi, Shift Kedua / Sore')
                            ->required()
                            ->maxLength(50),
                        FormComponents\TextInput::make('toleransi_menit')
                            ->label('Toleransi Keterlambatan (menit)')
                            ->numeric()
                            ->default(15)
                            ->required()
                            ->helperText('Contoh: 15 menit setelah jam masuk tetap dihitung tepat waktu.'),
                    ])->columns(2),

                Section::make('Jadwal Presensi Masuk')
                    ->description('Tentukan jam masuk resmi dan rentang waktu tombol check-in aktif.')
                    ->schema([
                        FormComponents\TimePicker::make('jam_masuk')
                            ->label('Jam Masuk Resmi')
                            ->required()
                            ->seconds(false),
                        FormComponents\TimePicker::make('jam_buka_masuk')
                            ->label('Buka Absen Masuk')
                            ->seconds(false)
                            ->helperText('Jam mulai guru diperbolehkan check-in (opsional).'),
                        FormComponents\TimePicker::make('jam_tutup_masuk')
                            ->label('Tutup Absen Masuk')
                            ->seconds(false)
                            ->helperText('Batas akhir jam check-in diperbolehkan (opsional).'),
                    ])->columns(3),

                Section::make('Jadwal Presensi Pulang')
                    ->description('Tentukan jam pulang resmi dan rentang waktu tombol check-out aktif.')
                    ->schema([
                        FormComponents\TimePicker::make('jam_pulang')
                            ->label('Jam Pulang Resmi')
                            ->required()
                            ->seconds(false),
                        FormComponents\TimePicker::make('jam_buka_pulang')
                            ->label('Buka Absen Pulang')
                            ->seconds(false)
                            ->helperText('Jam mulai tombol check-out bisa ditekan (opsional).'),
                        FormComponents\TimePicker::make('jam_tutup_pulang')
                            ->label('Tutup Absen Pulang')
                            ->seconds(false)
                            ->helperText('Batas akhir tombol check-out aktif (opsional).'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Shift')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Masuk')
                    ->time('H:i')
                    ->description(fn (Shift $r) => $r->jam_buka_masuk ? 'Rentang: '.$r->jam_buka_masuk?->format('H:i').' - '.($r->jam_tutup_masuk?->format('H:i') ?? 'dst') : null),
                Tables\Columns\TextColumn::make('jam_pulang')
                    ->label('Pulang')
                    ->time('H:i')
                    ->description(fn (Shift $r) => $r->jam_buka_pulang ? 'Rentang: '.$r->jam_buka_pulang?->format('H:i').' - '.($r->jam_tutup_pulang?->format('H:i') ?? 'dst') : null),
                Tables\Columns\TextColumn::make('toleransi_menit')
                    ->label('Toleransi')
                    ->suffix(' menit')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('gurus_count')
                    ->counts('gurus')
                    ->label('Jumlah Guru')
                    ->badge()
                    ->color('info'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShifts::route('/'),
            'create' => Pages\CreateShift::route('/create'),
            'edit' => Pages\EditShift::route('/{record}/edit'),
        ];
    }
}
