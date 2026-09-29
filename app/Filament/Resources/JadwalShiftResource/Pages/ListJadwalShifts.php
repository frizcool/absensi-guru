<?php

namespace App\Filament\Resources\JadwalShiftResource\Pages;

use App\Filament\Resources\JadwalShiftResource;
use App\Models\Guru;
use App\Models\JadwalShift;
use App\Models\Shift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components as FormComponents;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;

class ListJadwalShifts extends ListRecords
{
    protected static string $resource = JadwalShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('terapkanShiftMassal')
                ->label('Terapkan Shift Massal (Bulan / Hari Tertentu)')
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                ->modalHeading('Terapkan Shift Berdasarkan Hari (Rentang Tanggal / Sebulan)')
                ->modalDescription('Fitur ini memudahkan Anda menerapkan shift tertentu pada hari-hari tertentu (misal: setiap Selasa & Rabu) untuk satu atau seluruh guru dalam rentang tanggal / bulan yang dipilih.')
                ->form([
                    FormComponents\Select::make('guru_ids')
                        ->label('Target Guru')
                        ->options(fn () => Guru::where('aktif', true)->orderBy('nama')->pluck('nama', 'id'))
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->helperText('Pilih satu atau beberapa guru. Jika dikosongkan, shift akan diterapkan ke SEMUA guru aktif.'),
                    FormComponents\Select::make('shift_id')
                        ->label('Shift yang Diterapkan')
                        ->options(fn () => Shift::all()->mapWithKeys(fn ($s) => [
                            $s->id => "{$s->nama} (".($s->jam_masuk?->format('H:i') ?? '-').' - '.($s->jam_pulang?->format('H:i') ?? '-').')',
                        ]))
                        ->required(),
                    Grid::make(2)
                        ->schema([
                            FormComponents\DatePicker::make('tanggal_mulai')
                                ->label('Tanggal Mulai')
                                ->default(now()->startOfMonth())
                                ->required()
                                ->native(false),
                            FormComponents\DatePicker::make('tanggal_selesai')
                                ->label('Tanggal Selesai')
                                ->default(now()->endOfMonth())
                                ->required()
                                ->native(false),
                        ]),
                    FormComponents\CheckboxList::make('hari')
                        ->label('Pilih Hari yang Diterapkan Shift Ini')
                        ->options([
                            1 => 'Senin',
                            2 => 'Selasa',
                            3 => 'Rabu',
                            4 => 'Kamis',
                            5 => 'Jumat',
                            6 => 'Sabtu',
                            0 => 'Minggu',
                        ])
                        ->columns(4)
                        ->required()
                        ->helperText('Contoh: Centang Selasa & Rabu untuk menerapkan shift pagi di setiap hari Selasa & Rabu pada rentang tanggal tersebut.'),
                ])
                ->action(function (array $data) {
                    $start = Carbon::parse($data['tanggal_mulai']);
                    $end = Carbon::parse($data['tanggal_selesai']);

                    if ($end->lt($start)) {
                        Notification::make()
                            ->title('Rentang Tanggal Tidak Valid')
                            ->body('Tanggal selesai harus sama atau setelah tanggal mulai.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $selectedDays = array_map('intval', $data['hari']);
                    $shiftId = (int) $data['shift_id'];

                    $gurus = ! empty($data['guru_ids'])
                        ? Guru::whereIn('id', $data['guru_ids'])->get()
                        : Guru::where('aktif', true)->get();

                    if ($gurus->isEmpty()) {
                        Notification::make()
                            ->title('Tidak Ada Guru yang Dipilih')
                            ->body('Tidak ditemukan data guru aktif.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $period = CarbonPeriod::create($start, $end);
                    $totalJadwal = 0;

                    foreach ($period as $date) {
                        if (in_array($date->dayOfWeek, $selectedDays, true)) {
                            $tglStr = $date->toDateString();
                            foreach ($gurus as $guru) {
                                JadwalShift::updateOrCreate(
                                    [
                                        'guru_id' => $guru->id,
                                        'tanggal' => $tglStr,
                                    ],
                                    [
                                        'shift_id' => $shiftId,
                                    ]
                                );
                                $totalJadwal++;
                            }
                        }
                    }

                    $shiftNama = Shift::find($shiftId)?->nama ?? 'Shift';
                    Notification::make()
                        ->title('Shift Massal Berhasil Diterapkan')
                        ->body("Berhasil menerapkan {$shiftNama} ke {$totalJadwal} jadwal kerja guru.")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make()->label('Tambah Tukar/Jadwal Shift'),
        ];
    }
}
