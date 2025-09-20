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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('url');
            $table->string('icon')->nullable();
            $table->string('icon_type')->default('material'); // material, fontawesome, custom
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('target')->default('_self'); // _self, _blank
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
