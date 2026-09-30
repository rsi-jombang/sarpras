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
        $openOpname = StockOpname::whereIn('status', ['open', 'in_progress'])
            ->latest('id')
            ->first();

        if ($openOpname) {
            throw new \RuntimeException(
                'Masih ada stok opname ' . $openOpname->opname_number . ' yang belum ditutup. Selesaikan dulu sebelum membuat yang baru.'
            );
        }

        return DB::transaction(function () use ($creator, $data) {
            $opname = StockOpname::create([
                'opname_number' => $this->numberGenerator->generateOpnameNumber(),
                'created_by'    => $creator->id,
                'status'        => 'open',
                'opname_date'   => $data['opname_date'],
                'notes'         => $data['notes'] ?? null,
            ]);

            // Snapshot agregat per item (total seluruh batch, tanpa batch).
            // Catatan: tidak memakai chunk() karena chunk menambahkan
            // ORDER BY primary key yang melanggar ONLY_FULL_GROUP_BY
            // MySQL pada query GROUP BY.
            $rows = ItemStock::select('item_id', DB::raw('SUM(quantity) as total_qty'))
                ->groupBy('item_id')
                ->get();

            foreach ($rows as $row) {
                StockOpnameItem::create([
                    'stock_opname_id'   => $opname->id,
                    'item_stock_id'     => null,
                    'item_id'           => $row->item_id,
                    'system_quantity'   => (int) $row->total_qty,
                    'physical_quantity' => null,
                    'difference'        => null,
                ]);
            }

            return $opname;
        });
    }

    // -------------------------------------------------------------------------
    // Simpan hasil hitung fisik (bisa bertahap)
    // -------------------------------------------------------------------------
    public function updateItems(StockOpname $opname, array $data, User $counter): StockOpname
    {
        return DB::transaction(function () use ($opname, $data, $counter) {
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
                $opnameItem->counted_by        = $counter->id;
                $opnameItem->save();
            }

            if ($opname->status === 'open') {
                $opname->update(['status' => 'in_progress']);
            }

            return $opname->fresh();
        });
    }

    // -------------------------------------------------------------------------
    // Tarik item berstok yang belum tercakup ke dalam opname yang masih open
    // -------------------------------------------------------------------------
    public function syncItems(StockOpname $opname): int
    {
        return DB::transaction(function () use ($opname) {
            $existingItemIds = $opname->items()->pluck('item_id');
            $added = 0;

            // Catatan: tidak memakai chunk(), lihat komentar di create().
            $rows = ItemStock::select('item_id', DB::raw('SUM(quantity) as total_qty'))
                ->whereNotIn('item_id', $existingItemIds)
                ->groupBy('item_id')
                ->get();

            foreach ($rows as $row) {
                StockOpnameItem::create([
                    'stock_opname_id'   => $opname->id,
                    'item_stock_id'     => null,
                    'item_id'           => $row->item_id,
                    'system_quantity'   => (int) $row->total_qty,
                    'physical_quantity' => null,
                    'difference'        => null,
                ]);
                $added++;
            }

            return $added;
        });
    }

    // -------------------------------------------------------------------------
    // Tutup opname: validasi kelengkapan lalu buat adjustment via StockService
    // -------------------------------------------------------------------------
    public function close(StockOpname $opname, User $closer): StockOpname
    {
        $opname->refresh();

        if ($opname->status === 'closed') {
            throw new \RuntimeException('Stok opname sudah ditutup.');
        }

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
