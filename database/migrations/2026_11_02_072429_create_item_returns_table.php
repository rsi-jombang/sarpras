<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number', 50)->unique();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->enum('status', ['draft', 'submitted', 'received', 'cancelled'])->default('draft');
            $table->string('reason', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
            $table->index('created_by');
            $table->index('unit_id');
        });

        Schema::create('item_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_return_id')->constrained('item_returns')->cascadeOnDelete();
            $table->foreignId('request_item_id')->nullable()->constrained('request_items')->nullOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->foreignId('item_stock_id')->nullable()->constrained('item_stocks')->nullOnDelete();
            $table->integer('quantity_returned');
            $table->enum('condition', ['usable', 'damaged'])->default('usable');
            $table->integer('quantity_accepted')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('item_return_id');
            $table->index('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_return_items');
        Schema::dropIfExists('item_returns');
    }
};