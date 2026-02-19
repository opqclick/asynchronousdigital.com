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
        Schema::table('payments', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('salaries', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('team_contents', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('salaries', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('team_contents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
