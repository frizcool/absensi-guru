<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JurnalPembelajaranResource\Pages;
use App\Models\JurnalPembelajaran;
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

class JurnalPembelajaranResource extends Resource
{
    protected static ?string $model = JurnalPembelajaran::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'KBM & Kurikulum';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Jurnal Mengajar Guru';

    protected static ?string $modelLabel = 'Jurnal Pembelajaran';

    protected static ?string $pluralModelLabel = 'Jurnal Pembelajaran Guru';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Section::make('Catatan Pembelajaran / Kinerja KBM')
                    ->description('Rincian materi yang diajarkan guru pada jam tatap muka.')
                    ->columns(3)
                    ->schema([
                        FormComponents\Select::make('guru_id')
                            ->label('Guru Pengajar')
                            ->relationship('guru', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                        FormComponents\DatePicker::make('tanggal')
                            ->label('Tanggal KBM')
                            ->default(now())
                            ->required()
                            ->native(false),
                        FormComponents\TextInput::make('jumlah_jam')
                            ->label('Jumlah JP (Jam Pelajaran)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12)
                            ->default(2)
                            ->suffix('JP')
                            ->required(),
                        FormComponents\TextInput::make('kelas')
                            ->label('Kelas / Rombel')
                            ->placeholder('Contoh: Kelas IV-A, Kelas VI')
                            ->required()
                            ->maxLength(50),
                        FormComponents\TextInput::make('mata_pelajaran')
                            ->label('Mata Pelajaran')
                            ->placeholder('Contoh: Matematika, IPAS, Bahasa Indonesia')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(2),
                        FormComponents\Textarea::make('materi_kegiatan')
                            ->label('Materi Pokok & Uraian Kegiatan')
                            ->placeholder('Tuliskan pokok bahasan, kompetensi/tujuan pembelajaran, dan aktivitas siswa di kelas...')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        FormComponents\Textarea::make('keterangan')
                            ->label('Catatan Khusus / Evaluasi (Opsional)')
                            ->placeholder('Catatan perkembangan siswa, kendala KBM, atau PR/tugas...')
                            ->columnSpanFull(),
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
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('guru.nama')
                    ->label('Nama Guru')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (JurnalPembelajaran $r) => $r->guru?->nip ? 'NIP: '.$r->guru->nip : null),
                Tables\Columns\TextColumn::make('kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mata_pelajaran')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('materi_kegiatan')
                    ->label('Uraian Materi & Aktivitas')
                    ->limit(60)
                    ->tooltip(fn (JurnalPembelajaran $r) => $r->materi_kegiatan),
                Tables\Columns\TextColumn::make('jumlah_jam')
                    ->label('Beban')
                    ->formatStateUsing(fn ($state) => $state.' JP')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Rekam')
                    ->time('H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('guru_id')
                    ->relationship('guru', 'nama')
                    ->searchable()
                    ->preload()
                    ->label('Guru'),
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
            'index' => Pages\ListJurnalPembelajarans::route('/'),
            'create' => Pages\CreateJurnalPembelajaran::route('/create'),
            'edit' => Pages\EditJurnalPembelajaran::route('/{record}/edit'),
        ];
    }
}
