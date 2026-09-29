<?php

namespace App\Services;

use App\Models\HariLibur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HariLiburService
{
    /**
     * Sinkronkan hari libur nasional untuk tahun tertentu ke database.
     * Menggunakan API eksternal dengan fallback data standar resmi Indonesia.
     */
    public static function syncLiburNasional(int $tahun): array
    {
        $dataLibur = self::fetchFromApi($tahun);

        if (empty($dataLibur)) {
            $dataLibur = self::getFallbackLiburNasional($tahun);
        }

        $inserted = 0;
        $updated = 0;

        foreach ($dataLibur as $item) {
            $tanggal = Carbon::parse($item['tanggal'])->toDateString();
            $nama = trim($item['nama']);
            $isCutiBersama = (bool) ($item['is_cuti_bersama'] ?? false);

            $record = HariLibur::whereDate('tanggal_mulai', $tanggal)
                ->whereDate('tanggal_selesai', $tanggal)
                ->first();

            if (! $record) {
                HariLibur::create([
                    'nama' => $nama,
                    'tanggal_mulai' => $tanggal,
                    'tanggal_selesai' => $tanggal,
                    'is_libur_nasional' => true,
                    'keterangan' => $isCutiBersama ? 'Cuti Bersama Resmi Pemerintah RI' : 'Hari Libur Nasional Resmi',
                ]);
                $inserted++;
            } else {
                $record->update([
                    'nama' => $nama,
                    'is_libur_nasional' => true,
                    'keterangan' => $isCutiBersama ? 'Cuti Bersama Resmi Pemerintah RI' : 'Hari Libur Nasional Resmi',
                ]);
                $updated++;
            }
        }

        return [
            'success' => true,
            'tahun' => $tahun,
            'total' => count($dataLibur),
            'inserted' => $inserted,
            'updated' => $updated,
        ];
    }

    protected static function fetchFromApi(int $tahun): array
    {
        try {
            $response = Http::timeout(8)->get("https://dayoffapi.vercel.app/api?year={$tahun}");

            if ($response->successful()) {
                $results = [];
                $json = $response->json();

                if (is_array($json)) {
                    foreach ($json as $item) {
                        $isCuti = (bool) ($item['is_cuti'] ?? false);
                        $results[] = [
                            'tanggal' => $item['tanggal'],
                            'nama' => $item['keterangan'] ?? 'Libur Nasional',
                            'is_cuti_bersama' => $isCuti,
                        ];
                    }

                    return $results;
                }
            }
        } catch (\Throwable $e) {
            Log::info("Fetch API Hari Libur Nasional gagal ({$e->getMessage()}), beralih ke dataset standar.");
        }

        return [];
    }

    /**
     * Fallback data libur nasional resmi Republik Indonesia.
     */
    protected static function getFallbackLiburNasional(int $tahun): array
    {
        return [
            ['tanggal' => "{$tahun}-01-01", 'nama' => 'Tahun Baru Masehi', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-01-16", 'nama' => "Isra Mi'raj Nabi Muhammad SAW", 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-02-17", 'nama' => 'Tahun Baru Imlek 2577 Kongzili', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-03-20", 'nama' => 'Hari Suci Nyepi (Tahun Baru Saka)', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-03-21", 'nama' => 'Hari Raya Idul Fitri 1447 H (Hari 1)', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-03-22", 'nama' => 'Hari Raya Idul Fitri 1447 H (Hari 2)', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-03-23", 'nama' => 'Cuti Bersama Hari Raya Idul Fitri', 'is_cuti_bersama' => true],
            ['tanggal' => "{$tahun}-03-24", 'nama' => 'Cuti Bersama Hari Raya Idul Fitri', 'is_cuti_bersama' => true],
            ['tanggal' => "{$tahun}-04-03", 'nama' => 'Wafat Yesus Kristus', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-05-01", 'nama' => 'Hari Buruh Internasional', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-05-14", 'nama' => 'Kenaikan Yesus Kristus', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-05-27", 'nama' => 'Hari Raya Idul Adha 1447 H', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-05-31", 'nama' => 'Hari Raya Waisak 2570 BE', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-06-01", 'nama' => 'Hari Lahir Pancasila', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-06-16", 'nama' => 'Tahun Baru Islam 1448 H', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-08-17", 'nama' => 'Hari Kemerdekaan Republik Indonesia', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-08-25", 'nama' => 'Maulid Nabi Muhammad SAW', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-12-25", 'nama' => 'Hari Raya Natal', 'is_cuti_bersama' => false],
            ['tanggal' => "{$tahun}-12-26", 'nama' => 'Cuti Bersama Hari Raya Natal', 'is_cuti_bersama' => true],
        ];
    }
}
