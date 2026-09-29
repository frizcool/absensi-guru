<?php

namespace App\Filament\Widgets;

use App\Models\Guru;
use App\Models\PengaturanSekolah;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PeringatanKeterlambatanWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    public function table(Table $table): Table
    {
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        $setting = PengaturanSekolah::getSetting();
        $ambang = $setting->ambang_keterlambatan ?: 3;

        return $table
            ->heading('⚠️ Monitoring Keterlambatan Berulang')
            ->description("Daftar guru dengan frekuensi terlambat bulan ini (Batas Notifikasi WA: {$ambang}x).")
            ->query(
                Guru::query()
                    ->where('aktif', true)
                    ->whereHas('presensis', fn ($q) => $q->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->where('status_masuk', 'terlambat'))
                    ->withCount([
                        'presensis as total_terlambat' => fn ($q) => $q->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->where('status_masuk', 'terlambat'),
                    ])
                    ->orderByDesc('total_terlambat')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn (Guru $record) => $record->foto_url),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Guru')
                    ->weight('bold')
                    ->description(fn (Guru $record) => $record->nip ? 'NIP. '.$record->nip : ($record->jabatan ?: 'Guru')),

                Tables\Columns\TextColumn::make('total_terlambat')
                    ->label('Frekuensi')
                    ->badge()
                    ->color(fn ($state) => $state >= $ambang ? 'danger' : ($state >= 2 ? 'warning' : 'gray'))
                    ->formatStateUsing(fn ($state) => "{$state}x Terlambat"),

                Tables\Columns\TextColumn::make('status_ambang')
                    ->label('Status Peringatan')
                    ->state(function (Guru $record) use ($ambang) {
                        if ($record->total_terlambat >= $ambang) {
                            return '🚨 Capai Ambang WA';
                        }
                        if ($record->total_terlambat >= $ambang - 1) {
                            return '⚠️ Siaga Ambang';
                        }

                        return 'Catatan Ringan';
                    })
                    ->badge()
                    ->color(function (Guru $record) use ($ambang) {
                        if ($record->total_terlambat >= $ambang) {
                            return 'danger';
                        }
                        if ($record->total_terlambat >= $ambang - 1) {
                            return 'warning';
                        }

                        return 'gray';
                    }),
            ])
            ->actions([
                Action::make('kirimWaGuru')
                    ->label('WA Guru')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(function (Guru $record) use ($setting) {
                        $noWa = preg_replace('/[^0-9]/', '', $record->no_hp ?? '');
                        if (str_starts_with($noWa, '0')) {
                            $noWa = '62'.substr($noWa, 1);
                        }
                        if (! $noWa) {
                            return null;
                        }
                        $pesan = urlencode("Halo Bapak/Ibu {$record->nama}, mohon perhatian terkait kedisiplinan waktu presensi Anda di {$setting->nama_sekolah}. Terima kasih.");

                        return "https://wa.me/{$noWa}?text={$pesan}";
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (Guru $record) => ! empty($record->no_hp)),
            ])
            ->paginated([5])
            ->emptyStateHeading('Semua Guru Disiplin Tepat Waktu')
            ->emptyStateDescription('Belum ada guru yang tercatat terlambat pada bulan ini.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
