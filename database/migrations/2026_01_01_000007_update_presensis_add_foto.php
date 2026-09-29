<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->string('foto_masuk')->nullable()->after('lokasi_masuk_lng');
            $table->string('foto_pulang')->nullable()->after('lokasi_pulang_lng');
        });
    }

    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->dropColumn(['foto_masuk', 'foto_pulang']);
        });
    }
};
