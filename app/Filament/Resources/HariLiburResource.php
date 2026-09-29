<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HariLiburResource\Pages;
use App\Models\HariLibur;
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

class HariLiburResource extends Resource
{
    protected static ?string $model = HariLibur::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Hari Libur';

    protected static ?string $modelLabel = 'Hari Libur';

    protected static ?string $pluralModelLabel = 'Daftar Hari Libur';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Section::make('Informasi Hari Libur')
                    ->schema([
                        FormComponents\TextInput::make('nama')
                            ->label('Nama Hari Libur / Agenda')
                            ->placeholder('Contoh: Libur Hari Raya Idul Fitri, Libur Semester Ganjil')
                            ->required()
                            ->maxLength(150),
                        FormComponents\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->native(false),
                        FormComponents\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->afterOrEqual('tanggal_mulai')
                            ->native(false),
                        FormComponents\Toggle::make('is_libur_nasional')
                            ->label('Libur Resmi / Nasional')
                            ->default(true)
                            ->helperText('Jika aktif, seluruh presensi pada tanggal ini otomatis dibebaskan.'),
                        FormComponents\Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->placeholder('Catatan atau nomor surat edaran libur...')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_mulai', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Libur')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_libur_nasional')
                    ->label('Libur Resmi')
                    ->boolean(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->limit(40)
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('mendatang')
                    ->label('Libur Mendatang & Bulan Ini')
                    ->query(fn ($query) => $query->where('tanggal_selesai', '>=', now()->toDateString())),
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
            'index' => Pages\ListHariLiburs::route('/'),
            'create' => Pages\CreateHariLibur::route('/create'),
            'edit' => Pages\EditHariLibur::route('/{record}/edit'),
        ];
    }
}
