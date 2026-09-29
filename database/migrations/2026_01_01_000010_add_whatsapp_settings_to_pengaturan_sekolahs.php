<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaturan_sekolahs', function (Blueprint $table) {
            $table->string('no_wa_kepala_sekolah', 30)->nullable()->after('nip_kepala_sekolah');
            $table->string('wa_provider', 50)->default('fonnte')->after('no_wa_kepala_sekolah'); // fonnte, wablas, generic
            $table->text('wa_api_token')->nullable()->after('wa_provider');
            $table->string('wa_api_endpoint')->nullable()->after('wa_api_token');
            $table->boolean('notif_terlambat_aktif')->default(false)->after('wa_api_endpoint');
            $table->unsignedTinyInteger('ambang_keterlambatan')->default(3)->after('notif_terlambat_aktif'); // Notif jika terlambat >= N kali sebulan
        });
    }

    public function down(): void
    {
        Schema::table('pengaturan_sekolahs', function (Blueprint $table) {
            $table->dropColumn([
                'no_wa_kepala_sekolah',
                'wa_provider',
                'wa_api_token',
                'wa_api_endpoint',
                'notif_terlambat_aktif',
                'ambang_keterlambatan',
            ]);
        });
    }
};
