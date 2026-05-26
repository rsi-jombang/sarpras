<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Http\UploadedFile;
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

        unset($data['image']);

        return Item::create($data);
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
