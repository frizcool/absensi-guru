<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalShiftResource\Pages;
use App\Models\JadwalShift;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components as FormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class JadwalShiftResource extends Resource
{
    protected static ?string $model = JadwalShift::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static string|\UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Tukar / Jadwal Shift';

    protected static ?string $modelLabel = 'Jadwal Override Shift';

    protected static ?string $pluralModelLabel = 'Jadwal Khusus Shift';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Section::make('Penetapan Shift Khusus / Tukar Jadwal')
                    ->description('Gunakan fitur ini jika guru perlu bertukar shift atau memiliki jam kerja berbeda pada tanggal tertentu.')
                    ->schema([
                        FormComponents\Select::make('guru_id')
                            ->label('Guru')
                            ->relationship('guru', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                        FormComponents\Select::make('shift_id')
                            ->label('Shift Pengganti / Khusus')
                            ->relationship('shift', 'nama')
                            ->required(),
                        FormComponents\DatePicker::make('tanggal')
                            ->label('Tanggal Berlaku')
                            ->required()
                            ->native(false),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 1,
                        'md' => 3,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('guru.nama')
                    ->label('Nama Guru')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('guru.nip')
                    ->label('NIP')
                    ->searchable()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('shift.nama')
                    ->label('Shift Pengganti')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('shift.jam_masuk')
                    ->label('Jam Kerja')
                    ->formatStateUsing(fn ($record) => $record->shift ? $record->shift->jam_masuk?->format('H:i').' - '.$record->shift->jam_pulang?->format('H:i') : '-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('shift_id')
                    ->relationship('shift', 'nama')
                    ->label('Filter Shift'),
                Tables\Filters\Filter::make('hari_ini')
                    ->label('Hari Ini & Mendatang')
                    ->query(fn ($query) => $query->whereDate('tanggal', '>=', now()->toDateString())),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwalShifts::route('/'),
            'create' => Pages\CreateJadwalShift::route('/create'),
            'edit' => Pages\EditJadwalShift::route('/{record}/edit'),
        ];
    }
}
