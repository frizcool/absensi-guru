<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengajuanIzinResource\Pages;
use App\Models\PengajuanIzin;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components as FormComponents;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PengajuanIzinResource extends Resource
{
    protected static ?string $model = PengajuanIzin::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Presensi & Kehadiran';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Izin & Cuti';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'menunggu')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Section::make('Informasi Pengajuan Izin / Cuti')
                    ->description('Masukkan data pengajuan izin atau sakit untuk guru yang bersangkutan.')
                    ->schema([
                        FormComponents\Select::make('guru_id')
                            ->relationship('guru', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                        FormComponents\Select::make('jenis')
                            ->options([
                                'sakit' => 'Sakit',
                                'izin' => 'Izin',
                                'cuti' => 'Cuti',
                                'dinas_luar' => 'Tugas Luar / Dinas Luar (SPPD)',
                            ])
                            ->live()
                            ->required(),
                        FormComponents\DatePicker::make('tanggal_mulai')
                            ->native(false)
                            ->required(),
                        FormComponents\DatePicker::make('tanggal_selesai')
                            ->native(false)
                            ->required()
                            ->afterOrEqual('tanggal_mulai'),
                        FormComponents\TextInput::make('lokasi_tugas')
                            ->label('Lokasi Tugas / Instansi')
                            ->placeholder('Contoh: Hotel Claro Makassar / Dinas Pendidikan')
                            ->visible(fn ($get) => $get('jenis') === 'dinas_luar'),
                        FormComponents\TextInput::make('nomor_surat_tugas')
                            ->label('No. Surat Tugas (SPPD)')
                            ->placeholder('Contoh: 800/123/DISDIK/IX/2026')
                            ->visible(fn ($get) => $get('jenis') === 'dinas_luar'),
                        FormComponents\Textarea::make('alasan')
                            ->label('Alasan / Uraian Tugas')
                            ->placeholder('Alasan atau keterangan berhalangan hadir...')
                            ->required()
                            ->columnSpanFull(),
                        FormComponents\FileUpload::make('lampiran')
                            ->label('Lampiran (Surat Dokter / Surat Tugas / Dokumen Pendukung)')
                            ->directory('lampiran-izin')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('guru.nama')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'dinas_luar' => 'Dinas Luar',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'cuti' => 'Cuti',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state) => match ($state) {
                        'dinas_luar' => 'info',
                        'cuti' => 'primary',
                        'sakit' => 'warning',
                        'izin' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tanggal_mulai')->date('d M Y'),
                Tables\Columns\TextColumn::make('tanggal_selesai')->date('d M Y'),
                Tables\Columns\TextColumn::make('alasan')
                    ->limit(35)
                    ->description(fn (PengajuanIzin $r) => $r->lokasi_tugas ? 'Lokasi: '.$r->lokasi_tugas : null),
                Tables\Columns\IconColumn::make('lampiran')
                    ->label('Lampiran')
                    ->icon(fn (?string $state): ?string => $state ? 'heroicon-o-paper-clip' : null)
                    ->color('info')
                    ->url(fn (PengajuanIzin $record): ?string => $record->lampiran ? asset('storage/'.$record->lampiran) : null, true)
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'disetujui' => 'success', 'ditolak' => 'danger', default => 'warning',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak']),
            ])
            ->actions([
                Action::make('lihatLampiran')
                    ->label('Bukti')
                    ->icon('heroicon-o-paper-clip')
                    ->color('info')
                    ->url(fn (PengajuanIzin $record): ?string => asset('storage/'.$record->lampiran))
                    ->openUrlInNewTab()
                    ->visible(fn (PengajuanIzin $record): bool => ! empty($record->lampiran)),
                Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (PengajuanIzin $record) => $record->status === 'menunggu')
                    ->requiresConfirmation()
                    ->form([
                        FormComponents\Textarea::make('catatan')->label('Catatan (opsional)'),
                    ])
                    ->action(function (PengajuanIzin $record, array $data): void {
                        $record->setujui(Auth::user(), $data['catatan'] ?? null);

                        Notification::make()
                            ->title('Pengajuan disetujui')
                            ->body('Status kehadiran '.$record->guru->nama.' telah diperbarui otomatis.')
                            ->success()
                            ->send();
                    }),
                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (PengajuanIzin $record) => $record->status === 'menunggu')
                    ->requiresConfirmation()
                    ->form([
                        FormComponents\Textarea::make('catatan')->label('Alasan penolakan')->required(),
                    ])
                    ->action(function (PengajuanIzin $record, array $data): void {
                        $record->tolak(Auth::user(), $data['catatan']);

                        Notification::make()
                            ->title('Pengajuan ditolak')
                            ->warning()
                            ->send();
                    }),
                EditAction::make()
                    ->visible(fn (PengajuanIzin $record) => $record->status === 'menunggu'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajuanIzins::route('/'),
            'create' => Pages\CreatePengajuanIzin::route('/create'),
        ];
    }
}
