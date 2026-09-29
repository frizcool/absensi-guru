<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_pembelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('presensi_id')->nullable()->constrained('presensis')->nullOnDelete();
            $table->date('tanggal');
            $table->string('kelas', 50);
            $table->string('mata_pelajaran', 100);
            $table->text('materi_kegiatan');
            $table->unsignedSmallInteger('jumlah_jam')->default(2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_pembelajarans');
    }
};
