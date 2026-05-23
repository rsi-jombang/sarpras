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
        Schema::create('request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();

            // Dari batch/stok mana barang diambil (FIFO)
            $table->foreignId('item_stock_id')->nullable()->constrained('item_stocks')->nullOnDelete()
                ->comment('Diisi saat sarpras menyiapkan barang (FIFO)');

            $table->integer('quantity_requested')->comment('Jumlah yang diminta');
            $table->integer('quantity_approved')->nullable()->comment('Jumlah yang disetujui (bisa lebih kecil jika stok terbatas)');
            $table->integer('quantity_given')->nullable()->comment('Jumlah aktual yang diberikan');

            $table->enum('status', ['pending', 'approved', 'partial', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('request_id');
            $table->index('item_id');
            $table->index('item_stock_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_items');
    }
};
