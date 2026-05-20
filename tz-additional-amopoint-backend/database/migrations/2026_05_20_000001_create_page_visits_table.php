<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_hash', 64)->index();
            $table->text('ip_address')->nullable();
            $table->string('ip_hash', 64)->index();
            $table->string('city', 120)->default('Unknown')->index();
            $table->char('country', 2)->nullable()->index();
            $table->string('device_type', 32)->default('unknown')->index();
            $table->string('browser', 120)->nullable();
            $table->string('platform', 120)->nullable();
            $table->unsignedSmallInteger('screen_width')->nullable();
            $table->unsignedSmallInteger('screen_height')->nullable();
            $table->string('language', 20)->nullable();
            $table->string('timezone', 80)->nullable();
            $table->string('user_agent_hash', 64)->nullable()->index();
            $table->string('site_host', 255)->nullable()->index();
            $table->string('page_title', 255)->nullable();
            $table->text('page_url')->nullable();
            $table->text('referrer')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();

            $table->index(['occurred_at', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
