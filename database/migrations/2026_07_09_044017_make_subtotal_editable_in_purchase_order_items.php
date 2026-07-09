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

        DB::statement("ALTER TABLE purchase_order_items DROP COLUMN subtotal");
        DB::statement("ALTER TABLE purchase_order_items ADD COLUMN subtotal decimal(15,2) NOT NULL DEFAULT 0 AFTER unit_price");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE purchase_order_items DROP COLUMN subtotal");
        DB::statement("ALTER TABLE purchase_order_items ADD COLUMN subtotal decimal(15,2) GENERATED ALWAYS AS (quantity_ordered * unit_price) STORED AFTER unit_price");
    }
};
