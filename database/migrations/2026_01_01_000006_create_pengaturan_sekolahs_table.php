<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('radius_meter')->default(100);
            $table->boolean('wajib_validasi_lokasi')->default(false);
            $table->timestamps();
        });

        DB::table('pengaturan_sekolahs')->insert([
            'nama_sekolah' => 'UPTD SPF SD Inpres Rappojawa',
            'radius_meter' => 100,
            'wajib_validasi_lokasi' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_sekolahs');
    }
};
