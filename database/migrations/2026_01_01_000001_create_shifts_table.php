<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Shift Pagi, Shift Kedua
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->unsignedSmallInteger('toleransi_menit')->default(15);
            $table->timestamps();
        });

        // Seed dua shift default sesuai kebutuhan sekolah
        DB::table('shifts')->insert([
            [
                'nama' => 'Shift Pagi',
                'jam_masuk' => '07:15:00',
                'jam_pulang' => '14:30:00',
                'toleransi_menit' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Shift Kedua',
                'jam_masuk' => '09:30:00',
                'jam_pulang' => '17:30:00',
                'toleransi_menit' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
