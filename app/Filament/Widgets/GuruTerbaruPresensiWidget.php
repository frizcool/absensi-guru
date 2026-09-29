<?php

namespace App\Filament\Widgets;

use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\Action;
use Filament\Infolists;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class GuruTerbaruPresensiWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $pengaturan = PengaturanSekolah::getSetting();

        return $table
            ->heading('⚡ Feed Aktivitas Presensi Real-Time Hari Ini')
            ->description('Daftar guru yang telah melakukan absen masuk / pulang beserta verifikasi foto biometrik dan koordinat GPS.')
            ->query(
                Presensi::query()
                    ->whereDate('tanggal', Carbon::today())
                    ->whereNotNull('jam_masuk')
                    ->latest('updated_at')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('foto_masuk')
                    ->label('Selfie')
                    ->circular()
                    ->defaultImageUrl(fn (Presensi $record) => $record->foto_masuk_url ?? $record->guru?->foto_url),

                Tables\Columns\TextColumn::make('guru.nama')
                    ->label('Nama Guru')
                    ->weight('bold')
                    ->searchable()
                    ->description(fn (Presensi $r) => $r->guru?->nip ? 'NIP. '.$r->guru->nip.' • '.($r->guru->jabatan ?: 'Guru') : ($r->guru?->jabatan ?: 'Guru')),

                Tables\Columns\TextColumn::make('shift.nama')
                    ->label('Shift Kerja')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Waktu Masuk')
                    ->time('H:i')
                    ->suffix(' WITA')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status_masuk')
                    ->label('Status Masuk')
                    ->formatStateUsing(fn ($state) => $state === 'terlambat' ? 'Terlambat' : 'Tepat Waktu')
                    ->badge()
                    ->color(fn ($state) => $state === 'terlambat' ? 'warning' : 'success')
                    ->description(fn (Presensi $r) => $r->keterangan),

                Tables\Columns\TextColumn::make('jam_pulang')
                    ->label('Waktu Pulang')
                    ->time('H:i')
                    ->suffix(' WITA')
                    ->placeholder('Sedang Bertugas')
                    ->badge()
                    ->color(fn ($state) => $state ? 'info' : 'gray'),

                Tables\Columns\TextColumn::make('jarak_geofence')
                    ->label('Jarak GPS')
                    ->state(function (Presensi $record) use ($pengaturan) {
                        if (! $record->lokasi_masuk_lat || ! $record->lokasi_masuk_lng || ! $pengaturan->latitude || ! $pengaturan->longitude) {
                            return 'Tanpa GPS';
                        }
                        $jarak = round(Presensi::hitungJarakMeter(
                            $pengaturan->latitude,
                            $pengaturan->longitude,
                            $record->lokasi_masuk_lat,
                            $record->lokasi_masuk_lng
                        ));

                        return "📍 {$jarak}m";
                    })
                    ->badge()
                    ->color(function (Presensi $record) use ($pengaturan) {
                        if (! $record->lokasi_masuk_lat || ! $pengaturan->latitude) {
                            return 'gray';
                        }
                        $jarak = Presensi::hitungJarakMeter(
                            $pengaturan->latitude,
                            $pengaturan->longitude,
                            $record->lokasi_masuk_lat,
                            $record->lokasi_masuk_lng
                        );

                        return $jarak <= $pengaturan->radius_meter ? 'success' : 'danger';
                    })
                    ->description(fn (Presensi $r) => $r->lokasi_masuk_lat ? "({$r->lokasi_masuk_lat}, {$r->lokasi_masuk_lng})" : null),
            ])
            ->actions([
                Action::make('lihatDetail')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading('Detail Presensi Guru')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist([
                        Section::make('Verifikasi Foto Selfie & Lokasi')
                            ->schema([
                                Infolists\Components\ImageEntry::make('foto_masuk')
                                    ->label('Foto Masuk')
                                    ->defaultImageUrl(fn (Presensi $r) => $r->foto_masuk_url),
                                Infolists\Components\ImageEntry::make('foto_pulang')
                                    ->label('Foto Pulang')
                                    ->defaultImageUrl(fn (Presensi $r) => $r->foto_pulang_url)
                                    ->placeholder('Belum checkout pulang'),
                                Infolists\Components\TextEntry::make('guru.nama')
                                    ->label('Nama Guru'),
                                Infolists\Components\TextEntry::make('tanggal')
                                    ->date('l, d F Y'),
                                Infolists\Components\TextEntry::make('jam_masuk')
                                    ->time('H:i:s WITA'),
                                Infolists\Components\TextEntry::make('status_masuk')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('jam_pulang')
                                    ->time('H:i:s WITA')
                                    ->placeholder('Belum checkout'),
                                Infolists\Components\TextEntry::make('keterangan')
                                    ->placeholder('Tidak ada keterangan'),
                            ])->columns(2),
                    ]),
            ])
            ->paginated([5, 10, 25]);
    }
}
