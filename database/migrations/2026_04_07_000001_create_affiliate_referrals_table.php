<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_referrals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('referrer_user_id', 36);
            $table->char('referred_user_id', 36)->unique();
            $table->string('affiliate_code_used', 32);
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index('referrer_user_id', 'idx_aff_ref_referrer');
            $table->index('affiliate_code_used', 'idx_aff_ref_code');

            $table->foreign('referrer_user_id', 'affiliate_referrals_fk_referrer')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('referred_user_id', 'affiliate_referrals_fk_referred')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_referrals');
    }
};

