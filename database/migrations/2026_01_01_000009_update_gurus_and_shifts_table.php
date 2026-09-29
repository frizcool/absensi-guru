<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('user_id');
            $table->string('nuptk')->nullable()->after('nip');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('nama');
            $table->string('no_hp', 25)->nullable()->after('status_kepegawaian');
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->time('jam_buka_masuk')->nullable()->after('jam_masuk');
            $table->time('jam_tutup_masuk')->nullable()->after('jam_buka_masuk');
            $table->time('jam_buka_pulang')->nullable()->after('jam_pulang');
            $table->time('jam_tutup_pulang')->nullable()->after('jam_buka_pulang');
        });

        Schema::table('pengaturan_sekolahs', function (Blueprint $table) {
            $table->string('npsn')->nullable()->after('nama_sekolah');
            $table->text('alamat')->nullable()->after('npsn');
            $table->string('telepon', 30)->nullable()->after('alamat');
            $table->string('email', 100)->nullable()->after('telepon');
            $table->string('kepala_sekolah')->nullable()->after('email');
            $table->string('nip_kepala_sekolah')->nullable()->after('kepala_sekolah');
            $table->string('logo')->nullable()->after('nip_kepala_sekolah');
        });
    }

    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn(['foto', 'nuptk', 'jenis_kelamin', 'no_hp']);
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['jam_buka_masuk', 'jam_tutup_masuk', 'jam_buka_pulang', 'jam_tutup_pulang']);
        });

        Schema::table('pengaturan_sekolahs', function (Blueprint $table) {
            $table->dropColumn(['npsn', 'alamat', 'telepon', 'email', 'kepala_sekolah', 'nip_kepala_sekolah', 'logo']);
        });
    }
};
