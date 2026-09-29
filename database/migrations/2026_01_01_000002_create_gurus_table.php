<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nip')->nullable()->unique(); // Non-PNS honor kadang belum punya NIP
            $table->string('nama');
            $table->string('pangkat_golongan')->nullable();
            $table->string('jabatan')->nullable(); // Guru Muda, Kepala Sekolah, Guru Kelas, dst
            $table->string('jenis_guru')->nullable(); // Guru Kelas ..., Guru PAI, Guru PJOK, dst
            $table->enum('status_kepegawaian', ['pns', 'pppk', 'non_pns']);
            $table->unsignedTinyInteger('jumlah_jam')->nullable(); // jam mengajar per minggu
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
