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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('icon')->nullable(); // Font Awesome icon class or SVG path
            $table->text('short_description');
            $table->text('full_description')->nullable();
            $table->string('pricing_model')->nullable(); // 'fixed', 'hourly', 'custom'
            $table->decimal('base_price', 10, 2)->nullable();
            $table->string('price_display')->nullable(); // e.g., "Starting at $5,000" or "$150/hour"
            $table->json('features')->nullable(); // Array of key features
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
