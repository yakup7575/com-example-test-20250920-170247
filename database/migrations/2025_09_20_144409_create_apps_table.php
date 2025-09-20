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
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('package_name')->unique();
            $table->string('bundle_id')->nullable(); // iOS için
            $table->text('description')->nullable();
            $table->string('website_url');
            $table->string('app_icon')->nullable();
            $table->string('splash_image')->nullable();
            $table->string('primary_color')->default('#2196F3');
            $table->string('secondary_color')->default('#FFC107');
            $table->string('platform')->default('both'); // android, ios, both
            $table->string('status')->default('draft'); // draft, building, completed, published
            $table->string('onesignal_app_id')->nullable();
            $table->string('onesignal_api_key')->nullable();
            $table->json('build_settings')->nullable();
            $table->string('codemagic_build_id')->nullable();
            $table->string('android_build_url')->nullable();
            $table->string('ios_build_url')->nullable();
            $table->timestamp('last_build_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
