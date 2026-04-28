<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        if (!DB::table('settings')->where('key', 'is_early_bird_active')->exists()) {
            DB::table('settings')->insert([
                'key' => 'is_early_bird_active',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!Schema::hasTable('subscription_packages')) {
            return;
        }

        $exists = DB::table('subscription_packages')->where('code', 'HYPE')->exists();
        if (!$exists) {
            DB::table('subscription_packages')->insert([
                'code' => 'HYPE',
                'gender' => 'any',
                'path' => null,
                'poligami_level' => null,
                'poligami_situation' => null,
                'name' => 'HYPE (Early Bird)',
                'price_sen' => 7000,
                'currency' => 'MYR',
                'duration_days' => 365,
                'affiliate_percent' => 0,
                'ebook_path' => null,
                'is_active' => 1,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            DB::table('settings')->where('key', 'is_early_bird_active')->delete();
        }
        if (Schema::hasTable('subscription_packages')) {
            DB::table('subscription_packages')->where('code', 'HYPE')->delete();
        }
    }
};
