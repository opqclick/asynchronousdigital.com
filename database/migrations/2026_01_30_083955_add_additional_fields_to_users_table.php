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
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('address');
            $table->date('joining_date')->nullable()->after('date_of_birth');
            $table->string('profile_picture')->nullable()->after('joining_date');
            $table->json('documents')->nullable()->after('profile_picture')->comment('Array of document file paths');
            $table->string('bank_name')->nullable()->after('documents');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_holder')->nullable()->after('bank_account_number');
            $table->string('bank_routing_number')->nullable()->after('bank_account_holder');
            $table->string('bank_swift_code')->nullable()->after('bank_routing_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'joining_date',
                'profile_picture',
                'documents',
                'bank_name',
                'bank_account_number',
                'bank_account_holder',
                'bank_routing_number',
                'bank_swift_code',
            ]);
        });
    }
};
