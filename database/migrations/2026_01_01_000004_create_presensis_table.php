<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shifts');
            $table->date('tanggal');

            $table->dateTime('jam_masuk')->nullable();
            $table->decimal('lokasi_masuk_lat', 10, 7)->nullable();
            $table->decimal('lokasi_masuk_lng', 10, 7)->nullable();
            $table->enum('status_masuk', ['tepat_waktu', 'terlambat'])->nullable();

            $table->dateTime('jam_pulang')->nullable();
            $table->decimal('lokasi_pulang_lat', 10, 7)->nullable();
            $table->decimal('lokasi_pulang_lng', 10, 7)->nullable();
            $table->enum('status_pulang', ['normal', 'pulang_cepat'])->nullable();

            // hadir = presensi normal, sisanya diisi otomatis saat pengajuan izin disetujui
            $table->enum('status_kehadiran', ['hadir', 'sakit', 'izin', 'cuti', 'alpa'])->default('alpa');
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->unique(['guru_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
