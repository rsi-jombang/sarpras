<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan seeder ini PENTING — jangan diubah.
     * Unit harus ada sebelum User, Category sebelum Item.
     */
    public function run(): void
    {
        $this->call([
            UnitSeeder::class,      // 1. Unit/departemen
            UserSeeder::class,      // 2. User (butuh unit_id)
            CategorySeeder::class,  // 3. Kategori barang
            ItemSeeder::class,      // 4. Master barang (butuh category_id)
        ]);
    }
}
