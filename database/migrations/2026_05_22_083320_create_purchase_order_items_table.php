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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->foreignId('item_stock_id')->nullable()->constrained('item_stocks')->nullOnDelete()
                ->comment('Terisi setelah barang diterima, menunjuk batch yang dibuat');
            $table->integer('quantity_ordered');
            $table->integer('quantity_received')->default(0)->comment('Bisa berbeda jika ada kekurangan');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2)->storedAs('quantity_ordered * unit_price');
            $table->string('batch_number', 100)->nullable();
            $table->date('expired_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('purchase_order_id');
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
