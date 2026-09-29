<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaturan_sekolahs', function (Blueprint $table) {
            $table->boolean('wajib_device_binding')->default(false)->after('wajib_validasi_lokasi');
            $table->unsignedInteger('maksimal_akurasi_gps')->default(150)->after('radius_meter');
            $table->text('teks_pengumuman_display')->nullable()->after('logo');
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->string('device_id')->nullable()->after('user_id');
            $table->unsignedSmallInteger('kuota_cuti_tahunan')->default(12)->after('jumlah_jam');
        });

        Schema::table('pengajuan_izins', function (Blueprint $table) {
            $table->string('jenis')->change();
            $table->string('lokasi_tugas')->nullable()->after('alasan');
            $table->string('nomor_surat_tugas')->nullable()->after('lokasi_tugas');
        });

        Schema::table('presensis', function (Blueprint $table) {
            $table->string('status_kehadiran')->default('alpa')->change();
            $table->float('akurasi_masuk')->nullable()->after('lokasi_masuk_lng');
            $table->float('akurasi_pulang')->nullable()->after('lokasi_pulang_lng');
        });
    }

    public function down(): void
    {
        Schema::table('pengaturan_sekolahs', function (Blueprint $table) {
            $table->dropColumn(['wajib_device_binding', 'maksimal_akurasi_gps', 'teks_pengumuman_display']);
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn(['device_id', 'kuota_cuti_tahunan']);
        });

        Schema::table('pengajuan_izins', function (Blueprint $table) {
            $table->dropColumn(['lokasi_tugas', 'nomor_surat_tugas']);
        });

        Schema::table('presensis', function (Blueprint $table) {
            $table->dropColumn(['akurasi_masuk', 'akurasi_pulang']);
        });
    }
};
