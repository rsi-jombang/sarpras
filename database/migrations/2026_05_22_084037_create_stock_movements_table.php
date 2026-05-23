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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_stock_id')->constrained('item_stocks')->restrictOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();

            $table->enum('movement_type', [
                'in',           // Barang masuk dari pengadaan (PO)
                'out',          // Barang keluar dari permintaan
                'adjustment',   // Penyesuaian dari stok opname
                'return',       // Barang dikembalikan dari unit
            ]);

            $table->integer('quantity')->comment('Positif untuk masuk, negatif untuk keluar');
            $table->integer('stock_before')->comment('Stok sebelum pergerakan');
            $table->integer('stock_after')->comment('Stok setelah pergerakan');

            // Referensi ke sumber pergerakan (polymorphic manual)
            $table->string('reference_type')->nullable()
                ->comment('App\\Models\\PurchaseOrder | App\\Models\\Request | App\\Models\\StockOpname');
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->foreignId('performed_by')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['item_stock_id', 'created_at']);
            $table->index(['item_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('movement_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
