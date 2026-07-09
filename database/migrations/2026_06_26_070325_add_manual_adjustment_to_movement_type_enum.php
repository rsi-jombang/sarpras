<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN movement_type ENUM('in', 'out', 'adjustment', 'manual_adjustment', 'return')");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN movement_type ENUM('in', 'out', 'adjustment', 'return')");
    }
};
