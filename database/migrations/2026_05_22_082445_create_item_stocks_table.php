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
        Schema::create('item_stocks', function (Blueprint $table) {
           $table->id();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->decimal('purchase_price', 15, 2)->default(0)->comment('Harga beli per satuan');
            $table->decimal('selling_price', 15, 2)->default(0)->comment('Harga keluar/transfer internal');
            $table->integer('quantity')->default(0)->comment('Stok tersisa di batch ini');
            $table->integer('minimum_stock')->default(0)->comment('Batas minimum untuk notifikasi');
            $table->string('batch_number', 100)->nullable()->comment('Nomor batch / lot dari supplier');
            $table->date('expired_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index untuk mempercepat query FIFO
            $table->index(['item_id', 'created_at']);
            $table->index(['item_id', 'quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_stocks');
    }
};
