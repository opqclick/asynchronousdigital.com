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
        // Fix payment_model enum values
        DB::statement("ALTER TABLE users MODIFY payment_model ENUM('hourly', 'fixed', 'monthly') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old values if needed
        DB::statement("ALTER TABLE users MODIFY payment_model ENUM('hourly', 'monthly', 'project_based', 'task_based', 'contractual') NULL");
    }
};
