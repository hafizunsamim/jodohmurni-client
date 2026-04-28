<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Status keahlian: LITE (default), ACTIVE, GRADUATE, HYPE
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status_keahlian', 20)->default('LITE')->after('remember_token');
            $table->boolean('lite_education_seen')->default(false)->after('status_keahlian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status_keahlian', 'lite_education_seen']);
        });
    }
};
