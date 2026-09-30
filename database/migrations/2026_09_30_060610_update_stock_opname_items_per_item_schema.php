<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rebuild stock_opname_items ke skema per-item:
     * - item_stock_id menjadi nullable (diisi saat close dengan batch yang disesuaikan)
     * - unique lama (stock_opname_id, item_stock_id) dihapus
     * - tambah counted_by (audit siapa menghitung, P3)
     * Unique baru (stock_opname_id, item_id) ditambahkan setelah agregasi data.
     */
    public function up(): void
    {
        Schema::create('stock_opname_items_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opname')->cascadeOnDelete();
            $table->foreignId('item_stock_id')->nullable()->constrained('item_stocks')->restrictOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->integer('system_quantity')->comment('Total stok sistem per item saat snapshot');
            $table->integer('physical_quantity')->nullable()->comment('Total stok fisik hasil hitung per item');
            $table->integer('difference')->nullable()
                ->comment('physical_quantity - system_quantity; negatif = selisih kurang');
            $table->boolean('is_adjusted')->default(false)->comment('Sudah dibuatkan stock_movement adjustment?');
            $table->foreignId('counted_by')->nullable()->constrained('users')->nullOnDelete()
                ->comment('User terakhir yang menginput hasil fisik baris ini');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('stock_opname_id');
            $table->index('item_id');
        });

        DB::statement('INSERT INTO stock_opname_items_new (id, stock_opname_id, item_stock_id, item_id, system_quantity, physical_quantity, difference, is_adjusted, counted_by, notes, created_at, updated_at) SELECT id, stock_opname_id, item_stock_id, item_id, system_quantity, physical_quantity, difference, is_adjusted, NULL, notes, created_at, updated_at FROM stock_opname_items');

        Schema::drop('stock_opname_items');
        Schema::rename('stock_opname_items_new', 'stock_opname_items');
    }

    /**
     * Hanya valid bila migrasi agregasi belum berjalan.
     * Rollback setelah agregasi: restore dari backup pra-deploy.
     */
    public function down(): void
    {
        Schema::create('stock_opname_items_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opname')->cascadeOnDelete();
            $table->foreignId('item_stock_id')->constrained('item_stocks')->restrictOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->integer('system_quantity');
            $table->integer('physical_quantity')->nullable();
            $table->integer('difference')->nullable();
            $table->boolean('is_adjusted')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('stock_opname_id');
            $table->index('item_id');
            $table->unique(['stock_opname_id', 'item_stock_id']);
        });

        DB::statement('INSERT INTO stock_opname_items_old (id, stock_opname_id, item_stock_id, item_id, system_quantity, physical_quantity, difference, is_adjusted, notes, created_at, updated_at) SELECT id, stock_opname_id, item_stock_id, item_id, system_quantity, physical_quantity, difference, is_adjusted, notes, created_at, updated_at FROM stock_opname_items');

        Schema::drop('stock_opname_items');
        Schema::rename('stock_opname_items_old', 'stock_opname_items');
    }
};
