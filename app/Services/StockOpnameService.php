<?php

namespace App\Services;

use App\Models\ItemStock;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockOpnameService
{
    public function __construct(
        private readonly NumberGeneratorService $numberGenerator,
        private readonly StockService           $stockService,
    ) {}

    // -------------------------------------------------------------------------
    // Buat opname baru + snapshot stok sistem saat ini
    // -------------------------------------------------------------------------
    public function create(User $creator, array $data): StockOpname
    {
        return DB::transaction(function () use ($creator, $data) {
            $opname = StockOpname::create([
                'opname_number' => $this->numberGenerator->generateOpnameNumber(),
                'created_by'    => $creator->id,
                'status'        => 'open',
                'opname_date'   => $data['opname_date'],
                'notes'         => $data['notes'] ?? null,
            ]);

            // Snapshot seluruh item_stock yang ada saat ini
            ItemStock::chunk(200, function ($stocks) use ($opname) {
                foreach ($stocks as $stock) {
                    StockOpnameItem::create([
                        'stock_opname_id'   => $opname->id,
                        'item_stock_id'     => $stock->id,
                        'item_id'           => $stock->item_id,
                        'system_quantity'   => $stock->quantity,
                        'physical_quantity' => null,
                        'difference'        => null,
                    ]);
                }
            });

            return $opname;
        });
    }

    // -------------------------------------------------------------------------
    // Simpan hasil hitung fisik (bisa bertahap)
    // -------------------------------------------------------------------------
    public function updateItems(StockOpname $opname, array $data): StockOpname
    {
        return DB::transaction(function () use ($opname, $data) {
            foreach ($data['items'] as $itemData) {
                $opnameItem = StockOpnameItem::findOrFail($itemData['opname_item_id']);

                abort_if(
                    $opnameItem->stock_opname_id !== $opname->id,
                    403,
                    'Item tidak termasuk dalam opname ini.'
                );

                // Setter di model otomatis hitung difference
                $opnameItem->physical_quantity = (int) $itemData['physical_quantity'];
                $opnameItem->notes             = $itemData['notes'] ?? null;
                $opnameItem->save();
            }

            if ($opname->status === 'open') {
                $opname->update(['status' => 'in_progress']);
            }

            return $opname->fresh();
        });
    }

    // -------------------------------------------------------------------------
    // Tutup opname: validasi kelengkapan lalu buat adjustment via StockService
    // -------------------------------------------------------------------------
    public function close(StockOpname $opname, User $closer): StockOpname
    {
        $uncounted = $opname->items()->whereNull('physical_quantity')->count();

        if ($uncounted > 0) {
            throw new \RuntimeException(
                "Masih ada {$uncounted} item yang belum dihitung secara fisik."
            );
        }

        $this->stockService->closeStockOpname($opname, $closer->id);

        return $opname->fresh();
    }
}
