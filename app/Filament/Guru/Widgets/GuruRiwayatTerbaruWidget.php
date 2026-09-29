<?php

namespace App\Filament\Guru\Widgets;

use App\Filament\Guru\Pages\RiwayatPresensiGuruPage;
use App\Models\Guru;
use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class GuruRiwayatTerbaruWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $guru = $user?->guru ?? Guru::where('user_id', $user?->id)->first();
        $guruId = $guru?->id ?? 0;

        return $table
            ->heading('📅 Riwayat Kehadiran 7 Hari Terakhir')
            ->headerActions([
                Action::make('lihat_semua')
                    ->label('Buka Rekap Bulanan')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn () => RiwayatPresensiGuruPage::getUrl()),
            ])
            ->query(
                Presensi::query()
                    ->where('guru_id', $guruId)
                    ->whereDate('tanggal', '>=', Carbon::today()->subDays(7)->toDateString())
                    ->orderBy('tanggal', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->description(fn (Presensi $r) => $r->tanggal ? $r->tanggal->translatedFormat('l') : '')
                    ->weight('bold'),

                Tables\Columns\ImageColumn::make('foto_masuk')
                    ->label('Selfie In')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => $record->foto_masuk_url ?? null)
                    ->url(fn ($record) => $record->foto_masuk_url, true),

                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->badge()
                    ->color(fn (Presensi $r) => $r->status_masuk === 'terlambat' ? 'danger' : 'success')
                    ->description(fn (Presensi $r) => $r->status_masuk ? ucfirst(str_replace('_', ' ', $r->status_masuk)) : '-'),

                Tables\Columns\ImageColumn::make('foto_pulang')
                    ->label('Selfie Out')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => $record->foto_pulang_url ?? null)
                    ->url(fn ($record) => $record->foto_pulang_url, true),

                Tables\Columns\TextColumn::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->time('H:i')
                    ->placeholder('Belum checkout')
                    ->badge()
                    ->color(fn (Presensi $r) => $r->status_pulang === 'pulang_cepat' ? 'warning' : ($r->jam_pulang ? 'success' : 'gray')),

                Tables\Columns\TextColumn::make('status_kehadiran')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state) => match ($state) {
                        'hadir' => 'success',
                        'sakit' => 'info',
                        'izin' => 'warning',
                        'cuti' => 'primary',
                        'alpa' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan / Catatan')
                    ->limit(35)
                    ->placeholder('-'),
            ])
            ->paginated(false);
    }
}
