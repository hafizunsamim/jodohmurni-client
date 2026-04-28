<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_affiliate_applications', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('full_name', 190);
            $table->string('email', 190)->index();
            $table->string('phone_number', 50);
            $table->text('reason');

            $table->string('status', 20)->default('pending')->index();

            // linked system user (created on approval)
            $table->uuid('user_id')->nullable()->index();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by_admin_id')->nullable();
            $table->text('admin_feedback')->nullable();

            $table->timestamps();

            // soft-uniqueness enforcement via app logic:
            // - block if pending exists for email
            // - block if approved exists for email
            // - allow new row if latest is rejected
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_affiliate_applications');
    }
};

