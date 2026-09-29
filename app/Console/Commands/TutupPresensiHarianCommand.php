<?php

namespace App\Console\Commands;

use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TutupPresensiHarianCommand extends Command
{
    protected $signature = 'presensi:tutup-harian {--tanggal= : Tanggal yang ingin diproses (format Y-m-d)}';

    protected $description = 'Menutup presensi harian dan otomatis menetapkan status alpa bagi guru aktif yang tidak hadir tanpa izin pada hari kerja';

    public function handle(): int
    {
        $inputTanggal = $this->option('tanggal');
        $tanggal = $inputTanggal ? Carbon::parse($inputTanggal) : Carbon::today();
        $tanggalString = $tanggal->toDateString();

        $this->info("Menjalankan penutupan presensi untuk tanggal: {$tanggalString}");

        // Cek apakah tanggal adalah hari libur atau hari Minggu
        if (HariLibur::isLibur($tanggal)) {
            $infoLibur = HariLibur::getInfoLibur($tanggal) ?? 'Hari Libur';
            $this->warn("Tanggal {$tanggalString} adalah {$infoLibur}. Proses Alpa dilewati.");

            return Command::SUCCESS;
        }

        $gurus = Guru::where('aktif', true)->get();
        $totalAlpa = 0;
        $totalSudahTercatat = 0;

        foreach ($gurus as $guru) {
            $presensi = Presensi::where('guru_id', $guru->id)
                ->whereDate('tanggal', $tanggalString)
                ->first();

            if (! $presensi) {
                // Jangan tandai alpa jika hari ini adalah libur rutin guru tersebut
                if ($guru->isLiburPadaTanggal($tanggal)) {
                    $this->line("<fg=yellow>-</> {$guru->nama} dilewati (Hari Libur Rutin)");

                    continue;
                }

                // Guru tidak melakukan presensi sama sekali dan tidak ada izin disetujui
                $shift = $guru->shiftPadaTanggal($tanggal) ?? $guru->shift;

                Presensi::updateOrCreate(
                    [
                        'guru_id' => $guru->id,
                        'tanggal' => $tanggalString,
                    ],
                    [
                        'shift_id' => $shift?->id ?? 1,
                        'status_kehadiran' => 'alpa',
                        'keterangan' => 'Tidak hadir tanpa keterangan (Otomatis oleh sistem)',
                    ]
                );

                $totalAlpa++;
                $this->line("<fg=red>✗</> {$guru->nama} ditandai sebagai <fg=red;options=bold>ALPA</>");
            } else {
                $totalSudahTercatat++;
            }
        }

        $this->newLine();
        $this->info("=== Ringkasan Penutupan Presensi ({$tanggalString}) ===");
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Guru Aktif', $gurus->count()],
                ['Sudah Hadir / Izin Disetujui', $totalSudahTercatat],
                ['Otomatis Ditandai Alpa', $totalAlpa],
            ]
        );

        return Command::SUCCESS;
    }
}
