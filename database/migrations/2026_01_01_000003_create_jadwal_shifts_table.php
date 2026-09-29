<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dipakai hanya kalau shift guru perlu berbeda dari shift default
        // di tanggal tertentu (misal tukar jadwal). Kalau tidak ada baris
        // untuk tanggal tsb, sistem pakai shift_id default dari tabel gurus.
        Schema::create('jadwal_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->date('tanggal');
            $table->timestamps();

            $table->unique(['guru_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_shifts');
    }
};
