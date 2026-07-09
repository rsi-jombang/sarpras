<?php

namespace App\Services;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    public function __construct(
        private readonly NumberGeneratorService $numberGenerator,
        private readonly StockService           $stockService,
    ) {}

    // -------------------------------------------------------------------------
    // Buat PO baru (status: draft)
    // -------------------------------------------------------------------------
    public function create(User $creator, array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($creator, $data) {
            $po = PurchaseOrder::create([
                'po_number'        => $this->numberGenerator->generatePONumber(),
                'created_by'       => $creator->id,
                'supplier_name'    => $data['supplier_name'] ?? null,
                'supplier_contact' => $data['supplier_contact'] ?? null,
                'status'           => PurchaseOrderStatus::Draft,
                'order_date'       => $data['order_date'] ?? null,
                'expected_date'    => $data['expected_date'] ?? null,
                'notes'            => $data['notes'] ?? null,
            ]);

            $total = $this->syncItems($po, $data['items']);
            $po->update(['total_amount' => $total]);

            return $po;
        });
    }

    // -------------------------------------------------------------------------
    // Update PO yang masih draft
    // -------------------------------------------------------------------------
    public function update(PurchaseOrder $po, array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($po, $data) {
            $po->update([
                'supplier_name'    => $data['supplier_name'] ?? null,
                'supplier_contact' => $data['supplier_contact'] ?? null,
                'order_date'       => $data['order_date'] ?? null,
                'expected_date'    => $data['expected_date'] ?? null,
                'notes'            => $data['notes'] ?? null,
            ]);

            $po->items()->delete();
            $total = $this->syncItems($po, $data['items']);
            $po->update(['total_amount' => $total]);

            return $po->fresh('items');
        });
    }

    // -------------------------------------------------------------------------
    // Admin approve PO: draft → approved
    // -------------------------------------------------------------------------
    public function approve(PurchaseOrder $po, User $approver): PurchaseOrder
    {
        $po->update([
            'status'      => PurchaseOrderStatus::Approved,
            'approved_by' => $approver->id,
        ]);

        return $po->fresh();
    }

    // -------------------------------------------------------------------------
    // Sarpras terima barang: update qty aktual lalu serahkan ke StockService
    // -------------------------------------------------------------------------
    public function receive(PurchaseOrder $po, User $receiver, array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($po, $receiver, $data) {
            // Update detail penerimaan per item
            foreach ($data['items'] as $itemData) {
                $po->items()
                    ->where('id', $itemData['po_item_id'])
                    ->update([
                        'quantity_received' => $itemData['quantity_received'],
                        'unit_price'        => $itemData['unit_price'],
                        'batch_number'      => $itemData['batch_number'] ?? null,
                        'expired_date'      => $itemData['expired_date'] ?? null,
                    ]);
            }

            $po->update(['received_date' => $data['received_date']]);

            // Jalankan logika stok masuk
            $this->stockService->receivePurchaseOrder(
                $po->fresh('items'),
                $receiver->id
            );

            return $po->fresh();
        });
    }

    // -------------------------------------------------------------------------
    // Batalkan PO
    // -------------------------------------------------------------------------
    public function cancel(PurchaseOrder $po): PurchaseOrder
    {
        $po->update(['status' => PurchaseOrderStatus::Cancelled]);

        return $po->fresh();
    }

    // -------------------------------------------------------------------------
    // Hapus PO beserta item-nya
    // -------------------------------------------------------------------------
    // public function delete(PurchaseOrder $po): void
    // {
    //     DB::transaction(function () use ($po) {
    //         $po->items()->delete();
    //         $po->delete();
    //     });
    // }

    // -------------------------------------------------------------------------
    // Soft delete PO (items ikut terhapus via cascade saat force delete,
    // tapi soft delete hanya menandai deleted_at di tabel purchase_orders)
    // -------------------------------------------------------------------------
    public function delete(PurchaseOrder $po): void
    {
        // Soft delete — items TIDAK dihapus, PO hanya ditandai deleted_at
        // Ini memastikan data items tetap ada jika PO di-restore
        $po->delete();
    }

    // -------------------------------------------------------------------------
    // Restore PO dari trash
    // -------------------------------------------------------------------------
    public function restore(PurchaseOrder $po): PurchaseOrder
    {
        $po->restore();

        return $po->fresh();
    }

    // -------------------------------------------------------------------------
    // Force delete PO — hapus permanen beserta items (cascade)
    // -------------------------------------------------------------------------
    public function forceDelete(PurchaseOrder $po): void
    {
        // items akan terhapus otomatis via cascadeOnDelete di migration
        $po->forceDelete();
    }

    // -------------------------------------------------------------------------
    // Helper: insert items dan kembalikan total
    // -------------------------------------------------------------------------
    private function syncItems(PurchaseOrder $po, array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $subtotal = isset($item['subtotal']) ? (float) $item['subtotal'] : (int) $item['quantity_ordered'] * (float) $item['unit_price'];
            $total   += $subtotal;

            $po->items()->create([
                'item_id'          => $item['item_id'],
                'quantity_ordered'  => $item['quantity_ordered'],
                'unit_price'       => $item['unit_price'],
                'subtotal'         => $subtotal,
                'batch_number'     => $item['batch_number'] ?? null,
                'expired_date'     => $item['expired_date'] ?? null,
                'notes'            => $item['notes'] ?? null,
            ]);
        }

        return $total;
    }
}
