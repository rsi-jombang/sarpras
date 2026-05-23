<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number', 50)->unique()->comment('Contoh: OPN-2024-01');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->date('opname_date');
            $table->date('closed_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opname')->cascadeOnDelete();
            $table->foreignId('item_stock_id')->constrained('item_stocks')->restrictOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();

            $table->integer('system_quantity')->comment('Stok menurut sistem saat opname dimulai');
            $table->integer('physical_quantity')->nullable()->comment('Stok fisik hasil hitung');
            $table->integer('difference')->nullable()
                ->comment('physical_quantity - system_quantity; negatif = selisih kurang');

            $table->boolean('is_adjusted')->default(false)->comment('Sudah dibuatkan stock_movement adjustment?');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('stock_opname_id');
            $table->index('item_id');

            // Satu item_stock hanya boleh muncul sekali per opname
            $table->unique(['stock_opname_id', 'item_stock_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opname');
    }
};
