<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemStock;
use App\Models\StockMovement;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemService
{
    // -------------------------------------------------------------------------
    // Buat item baru
    // -------------------------------------------------------------------------
    public function create(array $data, ?UploadedFile $image = null): Item
    {
        if ($image) {
            $data['image_path'] = $image->store('items', 'public');
        }

        $initialStock = (int) ($data['initial_stock'] ?? 0);

        unset($data['image'], $data['initial_stock']);

        $item = Item::create($data);

        if ($initialStock > 0) {
            DB::transaction(function () use ($item, $initialStock) {
                $stock = ItemStock::create([
                    'item_id'  => $item->id,
                    'quantity' => $initialStock,
                    'notes'    => 'Stok Awal',
                ]);

                StockMovement::create([
                    'item_stock_id' => $stock->id,
                    'item_id'       => $item->id,
                    'movement_type' => 'in',
                    'quantity'      => $initialStock,
                    'stock_before'  => 0,
                    'stock_after'   => $initialStock,
                    'performed_by'  => auth()->id(),
                    'notes'         => 'Stok Awal',
                ]);
            });
        }

        return $item;
    }

    // -------------------------------------------------------------------------
    // Update item
    // -------------------------------------------------------------------------
    public function update(Item $item, array $data, ?UploadedFile $image = null): Item
    {
        if ($image) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $data['image_path'] = $image->store('items', 'public');
        }

        unset($data['image']);
        $item->update($data);

        return $item->fresh('category');
    }

    // -------------------------------------------------------------------------
    // Hapus item (cek stok dulu)
    // -------------------------------------------------------------------------
    public function delete(Item $item): void
    {
        if ($item->stocks()->exists()) {
            throw new \RuntimeException('Barang tidak dapat dihapus karena masih memiliki stok.');
        }
        // if ($item->stocks()->sum('quantity') > 0) {
        //     throw new \RuntimeException('Barang tidak dapat dihapus karena masih memiliki stok.');
        // }

        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();
    }
}
