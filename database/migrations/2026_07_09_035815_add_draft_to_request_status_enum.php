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

        DB::statement("ALTER TABLE requests MODIFY COLUMN status ENUM('draft','pending','approved','partial','rejected','ready','completed','cancelled') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE requests MODIFY COLUMN status ENUM('pending','approved','partial','rejected','ready','completed','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
