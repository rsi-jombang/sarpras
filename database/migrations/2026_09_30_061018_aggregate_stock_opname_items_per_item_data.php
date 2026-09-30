<?php

use App\Models\StockOpnameItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gabungkan baris per batch menjadi satu baris per item untuk setiap opname.
     * Aturan NULL: physical_quantity gabungan = NULL bila ada satu saja baris
     * asal yang belum dihitung, agar guard close tidak lolos atas hitungan
     * tak lengkap. Idempoten: aman dijalankan ulang (grup berisi 1 baris
     * akan dilewati).
     */
    public function up(): void
    {
        StockOpnameItem::select('stock_opname_id')
            ->distinct()
            ->orderBy('stock_opname_id')
            ->chunk(50, function ($opnames) {
                foreach ($opnames as $opname) {
                    StockOpnameItem::where('stock_opname_id', $opname->stock_opname_id)
                        ->get()
                        ->groupBy('item_id')
                        ->each(function ($group) use ($opname) {
                            if ($group->count() <= 1) {
                                return;
                            }

                            $allCounted = $group->every(fn($r) => $r->physical_quantity !== null);
                            $system     = (int) $group->sum('system_quantity');
                            $physical   = $allCounted ? (int) $group->sum('physical_quantity') : null;

                            $keep = $group->first();
                            // Urutan kunci penting: system dulu agar mutator
                            // physical_quantity menghitung difference dengan benar.
                            $keep->update([
                                'item_stock_id'     => null,
                                'system_quantity'   => $system,
                                'physical_quantity' => $physical,
                                'is_adjusted'       => $group->every(fn($r) => (bool) $r->is_adjusted),
                                'counted_by'        => null,
                                'notes'             => $group->pluck('notes')->filter()->unique()->implode('; ') ?: null,
                            ]);

                            StockOpnameItem::where('stock_opname_id', $opname->stock_opname_id)
                                ->where('item_id', $keep->item_id)
                                ->where('id', '!=', $keep->id)
                                ->delete();
                        });
                }
            });

        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->unique(['stock_opname_id', 'item_id']);
        });
    }

    /**
     * Tidak reversibel: agregasi menghancurkan rincian per batch.
     * Rollback = restore dari backup pra-deploy.
     */
    public function down(): void
    {
        throw new \RuntimeException(
            'Migrasi agregasi stock opname tidak dapat di-rollback. Restore database dari backup pra-deploy.'
        );
    }
};
