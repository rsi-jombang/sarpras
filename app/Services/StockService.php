<?php

namespace App\Services;

use App\Enums\PurchaseOrderStatus;
use App\Enums\RequestStatus;
use App\Enums\StockMovementType;
use App\Models\ItemStock;
use App\Models\PurchaseOrder;
use App\Models\Request;
use App\Models\StockMovement;
use App\Models\StockOpname;
use Illuminate\Support\Facades\DB;

class StockService
{
    // =========================================================================
    // PENGADAAN (Purchase Order)
    // =========================================================================

    /**
     * Terima barang dari PO: buat/perbarui batch stok, catat stock_movement.
     * Dipanggil saat PO berubah status ke 'received'.
     */
    public function receivePurchaseOrder(PurchaseOrder $po, int $receivedBy): void
    {
        DB::transaction(function () use ($po, $receivedBy) {
            foreach ($po->items as $poItem) {
                $qty = $poItem->quantity_received ?: $poItem->quantity_ordered;

                // Buat batch stok baru (karena harga bisa berbeda)
                $stock = ItemStock::create([
                    'item_id'        => $poItem->item_id,
                    'purchase_price' => $poItem->unit_price,
                    'selling_price'  => $poItem->unit_price,
                    'quantity'       => $qty,
                    'batch_number'   => $poItem->batch_number,
                    'expired_date'   => $poItem->expired_date,
                    'notes'          => "Dari PO: {$po->po_number}",
                ]);

                // Hubungkan PO item ke batch yang baru dibuat
                $poItem->update(['item_stock_id' => $stock->id, 'quantity_received' => $qty]);

                // Catat pergerakan stok
                $this->recordMovement(
                    itemStock:     $stock,
                    type:          StockMovementType::In,
                    qty:           $qty,
                    stockBefore:   0,
                    stockAfter:    $qty,
                    reference:     $po,
                    performedBy:   $receivedBy,
                    notes:         "Penerimaan dari PO {$po->po_number}",
                );
            }

            $po->update([
                'status'        => PurchaseOrderStatus::Received,
                'received_date' => now()->toDateString(),
                'approved_by'   => $receivedBy,
            ]);
        });
    }

    // =========================================================================
    // PERMINTAAN (Request)
    // =========================================================================

    /**
     * Siapkan barang dari permintaan: kurangi stok (FIFO), catat pergerakan.
     * Dipanggil saat sarpras mengubah status request ke 'ready'.
     */
    public function fulfillRequest(Request $request, int $servedBy): void
    {
        DB::transaction(function () use ($request, $servedBy) {
            foreach ($request->items as $reqItem) {
                $qtyNeeded = $reqItem->quantity_approved ?? $reqItem->quantity_requested;

                // FIFO: ambil dari batch terlama yang masih ada stok
                $availableStocks = ItemStock::where('item_id', $reqItem->item_id)
                    ->where('quantity', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->get();

                $qtyRemaining = $qtyNeeded;
                $firstStockId = null;

                foreach ($availableStocks as $stock) {
                    if ($qtyRemaining <= 0) break;

                    $taken        = min($stock->quantity, $qtyRemaining);
                    $stockBefore  = $stock->quantity;
                    $stockAfter   = $stock->quantity - $taken;

                    $stock->decrement('quantity', $taken);

                    $this->recordMovement(
                        itemStock:   $stock,
                        type:        StockMovementType::Out,
                        qty:         -$taken,
                        stockBefore: $stockBefore,
                        stockAfter:  $stockAfter,
                        reference:   $request,
                        performedBy: $servedBy,
                        notes:       "Permintaan {$request->request_number}",
                    );

                    $firstStockId ??= $stock->id;
                    $qtyRemaining -= $taken;
                }

                // Simpan batch utama yang dipakai (untuk audit)
                $reqItem->update([
                    'item_stock_id' => $firstStockId,
                    'quantity_given' => $qtyNeeded - $qtyRemaining,
                ]);
            }

            $request->update([
                'status'    => RequestStatus::Ready,
                'served_by' => $servedBy,
                'served_at' => now(),
            ]);
        });
    }

    // =========================================================================
    // STOK OPNAME
    // =========================================================================

    /**
     * Tutup opname: buat adjustment stock_movement untuk setiap item yang berbeda.
     */
    public function closeStockOpname(StockOpname $opname, int $closedBy): void
    {
        DB::transaction(function () use ($opname, $closedBy) {
            foreach ($opname->items()->where('is_adjusted', false)->get() as $opnameItem) {
                if ($opnameItem->difference === null || $opnameItem->difference === 0) {
                    $opnameItem->update(['is_adjusted' => true]);
                    continue;
                }

                $stock       = $opnameItem->itemStock;
                $stockBefore = $stock->quantity;
                $stockAfter  = $opnameItem->physical_quantity;

                $stock->update(['quantity' => $stockAfter]);

                $this->recordMovement(
                    itemStock:   $stock,
                    type:        StockMovementType::Adjustment,
                    qty:         $opnameItem->difference,
                    stockBefore: $stockBefore,
                    stockAfter:  $stockAfter,
                    reference:   $opname,
                    performedBy: $closedBy,
                    notes:       "Penyesuaian opname {$opname->opname_number}",
                );

                $opnameItem->update(['is_adjusted' => true]);
            }

            $opname->update([
                'status'      => 'closed',
                'closed_by'   => $closedBy,
                'closed_date' => now()->toDateString(),
            ]);
        });
    }

    // =========================================================================
    // HELPER PRIVATE
    // =========================================================================

    // =========================================================================
    // PENYESUAIAN STOK MANUAL
    // =========================================================================

    /**
     * Sesuaikan stok item secara manual: update batch, catat movement & adjustment.
     */
    public function adjustStock(
        \App\Models\Item $item,
        int $adjustmentQty,
        string $reason,
        int $adjustedBy,
        ?string $notes = null,
    ): \App\Models\StockAdjustment {
        return DB::transaction(function () use ($item, $adjustmentQty, $reason, $adjustedBy, $notes) {
            // Cari batch dengan stok terbanyak (prioritas utama untuk adjustment)
            $stock = ItemStock::where('item_id', $item->id)
                ->where('quantity', '>', 0)
                ->orderBy('quantity', 'desc')
                ->orderBy('created_at', 'asc')
                ->first();

            if (!$stock) {
                throw new \Exception("Tidak ada stok tersedia untuk item {$item->name}");
            }

            $stockBefore = $stock->quantity;
            $stockAfter = $stockBefore + $adjustmentQty;

            if ($stockAfter < 0) {
                throw new \Exception("Penyesuaian tidak valid: stok tidak boleh negatif. Stok saat ini: {$stockBefore}, adjustment: {$adjustmentQty}");
            }

            // Update batch stok
            $stock->update(['quantity' => $stockAfter]);

            // Catat pergerakan stok
            $this->recordMovement(
                itemStock:   $stock,
                type:        StockMovementType::ManualAdjustment,
                qty:         $adjustmentQty,
                stockBefore: $stockBefore,
                stockAfter:  $stockAfter,
                reference:   null,
                performedBy: $adjustedBy,
                notes:       $notes ?: "Penyesuaian manual: {$reason}",
            );

            // Buat record adjustment
            return \App\Models\StockAdjustment::create([
                'item_id'              => $item->id,
                'item_stock_id'        => $stock->id,
                'quantity_adjustment'  => $adjustmentQty,
                'stock_before'         => $stockBefore,
                'stock_after'          => $stockAfter,
                'reason'               => $reason,
                'notes'                => $notes,
                'adjusted_by'          => $adjustedBy,
            ]);
        });
    }

    private function recordMovement(
        ItemStock          $itemStock,
        StockMovementType  $type,
        int                $qty,
        int                $stockBefore,
        int                $stockAfter,
        mixed              $reference,
        int                $performedBy,
        string             $notes = '',
    ): StockMovement {
        return StockMovement::create([
            'item_stock_id'  => $itemStock->id,
            'item_id'        => $itemStock->item_id,
            'movement_type'  => $type->value,
            'quantity'       => $qty,
            'stock_before'   => $stockBefore,
            'stock_after'    => $stockAfter,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id'   => $reference?->id,
            'performed_by'   => $performedBy,
            'notes'          => $notes,
        ]);
    }
}
