<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresensiResource\Pages;
use App\Models\Presensi;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components as FormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Presensi & Kehadiran';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Data Presensi';

    protected static ?string $modelLabel = 'Data Presensi';

    protected static ?string $pluralModelLabel = 'Data Presensi Guru';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Section::make('Koreksi / Detail Presensi')
                    ->description('Hanya gunakan jika diperlukan koreksi administratif oleh Admin/Kepala Sekolah.')
                    ->schema([
                        FormComponents\Select::make('status_kehadiran')
                            ->label('Status Kehadiran')
                            ->options([
                                'hadir' => 'Hadir',
                                'dinas_luar' => 'Tugas Luar / Dinas Luar',
                                'sakit' => 'Sakit',
                                'izin' => 'Izin',
                                'cuti' => 'Cuti',
                                'alpa' => 'Alpa',
                            ])
                            ->required(),
                        FormComponents\Select::make('status_masuk')
                            ->label('Status Masuk')
                            ->options([
                                'tepat_waktu' => 'Tepat Waktu',
                                'terlambat' => 'Terlambat',
                            ]),
                        FormComponents\Select::make('status_pulang')
                            ->label('Status Pulang')
                            ->options([
                                'normal' => 'Normal',
                                'pulang_cepat' => 'Pulang Cepat',
                            ]),
                        FormComponents\DateTimePicker::make('jam_masuk')
                            ->label('Waktu Masuk'),
                        FormComponents\DateTimePicker::make('jam_pulang')
                            ->label('Waktu Pulang'),
                        FormComponents\Textarea::make('keterangan')
                            ->label('Alasan Koreksi / Keterangan')
                            ->columnSpanFull(),
                    ])->columns(3),
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
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('guru.nama')
                    ->label('Nama Guru')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Presensi $r) => $r->guru?->nip ? 'NIP: '.$r->guru->nip : 'Non-NIP'),

                Tables\Columns\ImageColumn::make('foto_masuk')
                    ->label('Foto In')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => $record->foto_masuk_url ?? null)
                    ->url(fn ($record) => $record->foto_masuk_url, true),

                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Masuk')
                    ->time('H:i')
                    ->description(fn (Presensi $r) => $r->status_masuk ? ucfirst(str_replace('_', ' ', $r->status_masuk)) : null)
                    ->color(fn (Presensi $r) => $r->status_masuk === 'terlambat' ? 'danger' : 'success')
                    ->url(fn (Presensi $r) => $r->lokasi_masuk_lat && $r->lokasi_masuk_lng
                        ? "https://www.google.com/maps?q={$r->lokasi_masuk_lat},{$r->lokasi_masuk_lng}"
                        : null, true),

                Tables\Columns\ImageColumn::make('foto_pulang')
                    ->label('Foto Out')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => $record->foto_pulang_url ?? null)
                    ->url(fn ($record) => $record->foto_pulang_url, true),

                Tables\Columns\TextColumn::make('jam_pulang')
                    ->label('Pulang')
                    ->time('H:i')
                    ->description(fn (Presensi $r) => $r->status_pulang ? ucfirst(str_replace('_', ' ', $r->status_pulang)) : null)
                    ->color(fn (Presensi $r) => $r->status_pulang === 'pulang_cepat' ? 'warning' : 'success')
                    ->url(fn (Presensi $r) => $r->lokasi_pulang_lat && $r->lokasi_pulang_lng
                        ? "https://www.google.com/maps?q={$r->lokasi_pulang_lat},{$r->lokasi_pulang_lng}"
                        : null, true),

                Tables\Columns\TextColumn::make('status_kehadiran')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'hadir' => 'Hadir', 'dinas_luar' => 'Dinas Luar', 'sakit' => 'Sakit', 'izin' => 'Izin',
                        'cuti' => 'Cuti', 'alpa' => 'Alpa', default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->color(fn (string $state) => match ($state) {
                        'hadir' => 'success',
                        'dinas_luar' => 'info',
                        'sakit' => 'info',
                        'izin' => 'warning',
                        'cuti' => 'primary',
                        'alpa' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('shift.nama')
                    ->label('Shift')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_kehadiran')
                    ->label('Status Kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'dinas_luar' => 'Dinas Luar',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'cuti' => 'Cuti',
                        'alpa' => 'Alpa',
                    ]),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        FormComponents\DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        FormComponents\DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari_tanggal'], fn ($q, $tgl) => $q->whereDate('tanggal', '>=', $tgl))
                            ->when($data['sampai_tanggal'], fn ($q, $tgl) => $q->whereDate('tanggal', '<=', $tgl));
                    }),
                Tables\Filters\Filter::make('anomali')
                    ->label('⚠️ Hanya Anomali (Terlambat / Pulang Cepat / Alpa)')
                    ->query(fn ($query) => $query->where(function ($q) {
                        $q->where('status_masuk', 'terlambat')
                            ->orWhere('status_pulang', 'pulang_cepat')
                            ->orWhere('status_kehadiran', 'alpa');
                    })),
            ])
            ->actions([
                EditAction::make()->label('Koreksi'),
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
            'index' => Pages\ListPresensis::route('/'),
        ];
    }
}
