<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'role_id']);
        });

        if (Schema::hasColumn('users', 'active_role_id')) {
            DB::table('users')
                ->select('id', 'role_id', 'active_role_id')
                ->orderBy('id')
                ->chunkById(200, function ($users) {
                    foreach ($users as $user) {
                        if (!$user->role_id) {
                            continue;
                        }

                        DB::table('user_roles')->updateOrInsert(
                            ['user_id' => $user->id, 'role_id' => $user->role_id],
                            ['created_at' => now(), 'updated_at' => now()]
                        );

                        if (!$user->active_role_id) {
                            DB::table('users')
                                ->where('id', $user->id)
                                ->update(['active_role_id' => $user->role_id]);
                        }
                    }
                });

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('active_role_id')->nullable()->after('role_id')->constrained('roles')->nullOnDelete();
        });

        DB::table('users')
            ->select('id', 'role_id')
            ->whereNotNull('role_id')
            ->orderBy('id')
            ->chunkById(200, function ($users) {
                foreach ($users as $user) {
                    DB::table('user_roles')->updateOrInsert(
                        ['user_id' => $user->id, 'role_id' => $user->role_id],
                        ['created_at' => now(), 'updated_at' => now()]
                    );

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['active_role_id' => $user->role_id]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'active_role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['active_role_id']);
                $table->dropColumn('active_role_id');
            });
        }

        Schema::dropIfExists('user_roles');
    }
};
