<?php

namespace App\Filament\Widgets;

use App\Models\Guru;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LeaderboardDisiplinWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    public function table(Table $table): Table
    {
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        $namaBulan = Carbon::now()->translatedFormat('F Y');

        return $table
            ->heading("🏆 Leaderboard Kedisiplinan Guru ({$namaBulan})")
            ->description('Guru dengan akumulasi kehadiran tepat waktu tertinggi pada bulan berjalan.')
            ->query(
                Guru::query()
                    ->where('aktif', true)
                    ->withCount([
                        'presensis as total_hadir' => fn ($q) => $q->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->where('status_kehadiran', 'hadir'),
                        'presensis as total_tepat_waktu' => fn ($q) => $q->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->where('status_masuk', 'tepat_waktu'),
                        'presensis as total_terlambat' => fn ($q) => $q->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->where('status_masuk', 'terlambat'),
                    ])
                    ->orderByDesc('total_tepat_waktu')
                    ->orderBy('total_terlambat')
            )
            ->columns([
                Tables\Columns\TextColumn::make('ranking')
                    ->label('#')
                    ->rowIndex()
                    ->badge()
                    ->color(fn ($rowLoop) => match ($rowLoop->iteration) {
                        1 => 'warning',
                        2 => 'gray',
                        3 => 'danger',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn ($state, $rowLoop) => match ($rowLoop->iteration) {
                        1 => '🥇 #1',
                        2 => '🥈 #2',
                        3 => '🥉 #3',
                        default => '#'.$rowLoop->iteration,
                    }),

                Tables\Columns\ImageColumn::make('foto')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn (Guru $record) => $record->foto_url),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Guru')
                    ->weight('bold')
                    ->description(fn (Guru $record) => $record->jabatan ?: $record->jenis_guru),

                Tables\Columns\TextColumn::make('total_tepat_waktu')
                    ->label('Tepat Waktu')
                    ->badge()
                    ->color('success')
                    ->suffix(' Hari'),

                Tables\Columns\TextColumn::make('total_terlambat')
                    ->label('Terlambat')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray')
                    ->suffix(' Hari'),

                Tables\Columns\TextColumn::make('skor_disiplin')
                    ->label('Tingkat Disiplin')
                    ->state(function (Guru $record) {
                        $total = $record->total_hadir;
                        if ($total == 0) {
                            return '100%';
                        }
                        $persen = round(($record->total_tepat_waktu / $total) * 100);

                        return "{$persen}%";
                    })
                    ->badge()
                    ->color(function (Guru $record) {
                        $total = $record->total_hadir;
                        if ($total == 0) {
                            return 'success';
                        }
                        $persen = round(($record->total_tepat_waktu / $total) * 100);

                        return $persen >= 90 ? 'success' : ($persen >= 75 ? 'warning' : 'danger');
                    }),
            ])
            ->paginated([5]);
    }
}
