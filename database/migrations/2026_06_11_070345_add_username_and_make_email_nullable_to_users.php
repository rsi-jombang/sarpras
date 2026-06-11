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
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username', 50)->nullable()->unique()->after('name');
            });
        }

        // Backfill username dari prefix email jika masih ada yang null
        foreach (DB::table('users')->whereNull('username')->get() as $user) {
            $prefix = str($user->email)->before('@')->toString();
            $username = $prefix;
            $suffix = 1;
            while (DB::table('users')->where('username', $username)->exists()) {
                $username = $prefix . $suffix;
                $suffix++;
            }
            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }

        // Buat username not null setelah backfill
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable(false)->change();
        });

        // Email jadi nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};
