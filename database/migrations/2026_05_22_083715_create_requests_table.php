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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 50)->unique()->comment('Nomor permintaan otomatis, contoh: REQ-2024-0001');

            // Pihak pengaju
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();

            // Status & prioritas
            $table->enum('status', [
                'draft',        // Masih draft, belum diajukan
                'pending',      // Baru diajukan, menunggu review sarpras
                'approved',     // Disetujui sarpras, menunggu pengambilan
                'partial',      // Sebagian disetujui (stok tidak cukup)
                'rejected',     // Ditolak sarpras
                'ready',        // Barang sudah disiapkan, menunggu diambil
                'completed',    // Barang sudah diambil oleh unit
                'cancelled',    // Dibatalkan oleh pengaju
            ])->default('pending');

            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            // Waktu
            $table->date('request_date');
            $table->date('needed_date')->nullable()->comment('Tanggal dibutuhkan');

            // Keterangan
            $table->text('purpose')->nullable()->comment('Keperluan / tujuan penggunaan');
            $table->text('notes')->nullable();

            // Petugas sarpras yang mereview (approve/reject)
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            // Petugas sarpras yang menyiapkan & menyerahkan barang
            $table->foreignId('served_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('served_at')->nullable();

            // Konfirmasi pengambilan oleh unit (siapa yang mengambil)
            $table->string('received_by_name')->nullable()->comment('Nama petugas unit yang mengambil');
            $table->timestamp('received_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('requested_by');
            $table->index('unit_id');
            $table->index('request_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
