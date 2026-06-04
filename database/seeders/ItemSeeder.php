<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemStock;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua kategori sekali, di-index by code
        $cats = Category::all()->keyBy('code');

        $items = [

            // =================================================================
            // BHP-ALKES — Alat Kesehatan Habis Pakai
            // =================================================================
            ['code' => 'ALKES-001', 'category' => 'BHP-ALKES', 'name' => 'Spuit 1 ml',                   'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-002', 'category' => 'BHP-ALKES', 'name' => 'Spuit 3 ml',                   'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-003', 'category' => 'BHP-ALKES', 'name' => 'Spuit 5 ml',                   'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-004', 'category' => 'BHP-ALKES', 'name' => 'Spuit 10 ml',                  'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-005', 'category' => 'BHP-ALKES', 'name' => 'Spuit 20 ml',                  'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-006', 'category' => 'BHP-ALKES', 'name' => 'Spuit 50 ml',                  'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-007', 'category' => 'BHP-ALKES', 'name' => 'Infus Set Dewasa',             'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-008', 'category' => 'BHP-ALKES', 'name' => 'Infus Set Anak (Micro Drip)',  'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-009', 'category' => 'BHP-ALKES', 'name' => 'IV Catheter / Abocath No. 18', 'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-010', 'category' => 'BHP-ALKES', 'name' => 'IV Catheter / Abocath No. 20', 'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-011', 'category' => 'BHP-ALKES', 'name' => 'IV Catheter / Abocath No. 22', 'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-012', 'category' => 'BHP-ALKES', 'name' => 'IV Catheter / Abocath No. 24', 'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-013', 'category' => 'BHP-ALKES', 'name' => 'Folley Catheter No. 14',       'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-014', 'category' => 'BHP-ALKES', 'name' => 'Folley Catheter No. 16',       'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-015', 'category' => 'BHP-ALKES', 'name' => 'Folley Catheter No. 18',       'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-016', 'category' => 'BHP-ALKES', 'name' => 'NGT No. 14',                   'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-017', 'category' => 'BHP-ALKES', 'name' => 'NGT No. 16',                   'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-018', 'category' => 'BHP-ALKES', 'name' => 'Urine Bag',                    'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-019', 'category' => 'BHP-ALKES', 'name' => 'Blood Transfusion Set',        'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-020', 'category' => 'BHP-ALKES', 'name' => 'Needle / Jarum Suntik No. 23', 'uom' => 'box',  'type' => 'consumable', 'desc' => 'isi 100 pcs/box'],
            ['code' => 'ALKES-021', 'category' => 'BHP-ALKES', 'name' => 'Needle / Jarum Suntik No. 25', 'uom' => 'box',  'type' => 'consumable', 'desc' => 'isi 100 pcs/box'],
            ['code' => 'ALKES-022', 'category' => 'BHP-ALKES', 'name' => 'Scalp Vein No. 23',            'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-023', 'category' => 'BHP-ALKES', 'name' => 'Scalp Vein No. 25',            'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-024', 'category' => 'BHP-ALKES', 'name' => 'Oxygen Mask Dewasa',           'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-025', 'category' => 'BHP-ALKES', 'name' => 'Oxygen Mask Anak',             'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-026', 'category' => 'BHP-ALKES', 'name' => 'Nasal Cannula Dewasa',         'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-027', 'category' => 'BHP-ALKES', 'name' => 'Nasal Cannula Anak',           'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-028', 'category' => 'BHP-ALKES', 'name' => 'Suction Catheter No. 10',      'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-029', 'category' => 'BHP-ALKES', 'name' => 'Suction Catheter No. 12',      'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-030', 'category' => 'BHP-ALKES', 'name' => 'Tongue Depressor / Spatula',   'uom' => 'box',  'type' => 'consumable', 'desc' => 'isi 100 pcs/box'],
            ['code' => 'ALKES-031', 'category' => 'BHP-ALKES', 'name' => 'Tourniquet',                   'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-032', 'category' => 'BHP-ALKES', 'name' => 'Endotracheal Tube No. 7.0',    'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-033', 'category' => 'BHP-ALKES', 'name' => 'Endotracheal Tube No. 7.5',    'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-034', 'category' => 'BHP-ALKES', 'name' => 'Endotracheal Tube No. 8.0',    'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-035', 'category' => 'BHP-ALKES', 'name' => 'Three Way Stopcock',           'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ALKES-036', 'category' => 'BHP-ALKES', 'name' => 'Extension Tube / Selang Infus Tambahan', 'uom' => 'pcs', 'type' => 'consumable'],

            // =================================================================
            // BHP-ALKES-STERIL — Alat Kesehatan Steril
            // =================================================================
            ['code' => 'STERIL-001', 'category' => 'BHP-ALKES-STERIL', 'name' => 'Sarung Tangan Steril No. 6.5',  'uom' => 'pasang', 'type' => 'consumable'],
            ['code' => 'STERIL-002', 'category' => 'BHP-ALKES-STERIL', 'name' => 'Sarung Tangan Steril No. 7.0',  'uom' => 'pasang', 'type' => 'consumable'],
            ['code' => 'STERIL-003', 'category' => 'BHP-ALKES-STERIL', 'name' => 'Sarung Tangan Steril No. 7.5',  'uom' => 'pasang', 'type' => 'consumable'],
            ['code' => 'STERIL-004', 'category' => 'BHP-ALKES-STERIL', 'name' => 'Sarung Tangan Steril No. 8.0',  'uom' => 'pasang', 'type' => 'consumable'],
            ['code' => 'STERIL-005', 'category' => 'BHP-ALKES-STERIL', 'name' => 'Kasa Steril 16x16 cm',          'uom' => 'pcs',    'type' => 'consumable'],
            ['code' => 'STERIL-006', 'category' => 'BHP-ALKES-STERIL', 'name' => 'Kasa Steril 7.5x7.5 cm',        'uom' => 'pcs',    'type' => 'consumable'],
            ['code' => 'STERIL-007', 'category' => 'BHP-ALKES-STERIL', 'name' => 'Dressing Pack / Set Ganti Balut','uom' => 'set',    'type' => 'consumable'],

            // =================================================================
            // BHP-LINEN — Linen & Tekstil Medis
            // =================================================================
            ['code' => 'LINEN-001', 'category' => 'BHP-LINEN', 'name' => 'Kasa Non-Steril 16x16 cm (roll)',   'uom' => 'roll',  'type' => 'consumable'],
            ['code' => 'LINEN-002', 'category' => 'BHP-LINEN', 'name' => 'Kapas Putih 250 gram',              'uom' => 'pack',  'type' => 'consumable'],
            ['code' => 'LINEN-003', 'category' => 'BHP-LINEN', 'name' => 'Plester Luka / Hansaplast',         'uom' => 'box',   'type' => 'consumable'],
            ['code' => 'LINEN-004', 'category' => 'BHP-LINEN', 'name' => 'Plester Coklat 5 cm x 5 m',        'uom' => 'roll',  'type' => 'consumable'],
            ['code' => 'LINEN-005', 'category' => 'BHP-LINEN', 'name' => 'Plester Coklat 10 cm x 5 m',       'uom' => 'roll',  'type' => 'consumable'],
            ['code' => 'LINEN-006', 'category' => 'BHP-LINEN', 'name' => 'Perban Elastis 5 cm',               'uom' => 'roll',  'type' => 'consumable'],
            ['code' => 'LINEN-007', 'category' => 'BHP-LINEN', 'name' => 'Perban Elastis 10 cm',              'uom' => 'roll',  'type' => 'consumable'],
            ['code' => 'LINEN-008', 'category' => 'BHP-LINEN', 'name' => 'Verban Kasa Gulung 5 cm',           'uom' => 'roll',  'type' => 'consumable'],
            ['code' => 'LINEN-009', 'category' => 'BHP-LINEN', 'name' => 'Verban Kasa Gulung 10 cm',          'uom' => 'roll',  'type' => 'consumable'],
            ['code' => 'LINEN-010', 'category' => 'BHP-LINEN', 'name' => 'Underpad / Perlak Sekali Pakai',    'uom' => 'pcs',   'type' => 'consumable'],

            // =================================================================
            // BHP-APD — Alat Pelindung Diri
            // =================================================================
            ['code' => 'APD-001', 'category' => 'BHP-APD', 'name' => 'Masker Bedah 3 Ply',              'uom' => 'box',  'type' => 'consumable', 'desc' => 'isi 50 pcs/box'],
            ['code' => 'APD-002', 'category' => 'BHP-APD', 'name' => 'Masker N95 / KN95',               'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'APD-003', 'category' => 'BHP-APD', 'name' => 'Sarung Tangan Latex Non-Steril S', 'uom' => 'box',  'type' => 'consumable', 'desc' => 'isi 100 pcs/box'],
            ['code' => 'APD-004', 'category' => 'BHP-APD', 'name' => 'Sarung Tangan Latex Non-Steril M', 'uom' => 'box',  'type' => 'consumable', 'desc' => 'isi 100 pcs/box'],
            ['code' => 'APD-005', 'category' => 'BHP-APD', 'name' => 'Sarung Tangan Latex Non-Steril L', 'uom' => 'box',  'type' => 'consumable', 'desc' => 'isi 100 pcs/box'],
            ['code' => 'APD-006', 'category' => 'BHP-APD', 'name' => 'Sarung Tangan Vinyl Non-Steril M', 'uom' => 'box',  'type' => 'consumable', 'desc' => 'isi 100 pcs/box'],
            ['code' => 'APD-007', 'category' => 'BHP-APD', 'name' => 'Hazmat / Baju Pelindung',          'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'APD-008', 'category' => 'BHP-APD', 'name' => 'Face Shield',                      'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'APD-009', 'category' => 'BHP-APD', 'name' => 'Google / Kacamata Pelindung',      'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'APD-010', 'category' => 'BHP-APD', 'name' => 'Apron Plastik',                    'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'APD-011', 'category' => 'BHP-APD', 'name' => 'Shoe Cover / Penutup Sepatu',      'uom' => 'pasang', 'type' => 'consumable'],
            ['code' => 'APD-012', 'category' => 'BHP-APD', 'name' => 'Cap / Penutup Kepala',             'uom' => 'pcs',  'type' => 'consumable'],

            // =================================================================
            // BHP-DISINFEKTAN — Desinfektan & Antiseptik
            // =================================================================
            ['code' => 'DISIN-001', 'category' => 'BHP-DISINFEKTAN', 'name' => 'Alkohol 70% (botol 100 ml)',       'uom' => 'botol', 'type' => 'consumable'],
            ['code' => 'DISIN-002', 'category' => 'BHP-DISINFEKTAN', 'name' => 'Alkohol 70% (jerigen 5 liter)',     'uom' => 'jerigen','type' => 'consumable'],
            ['code' => 'DISIN-003', 'category' => 'BHP-DISINFEKTAN', 'name' => 'Povidone Iodine / Betadine 30 ml', 'uom' => 'botol', 'type' => 'consumable'],
            ['code' => 'DISIN-004', 'category' => 'BHP-DISINFEKTAN', 'name' => 'Povidone Iodine / Betadine 300 ml','uom' => 'botol', 'type' => 'consumable'],
            ['code' => 'DISIN-005', 'category' => 'BHP-DISINFEKTAN', 'name' => 'Chlorhexidine Gluconate 2% 500 ml','uom' => 'botol', 'type' => 'consumable'],
            ['code' => 'DISIN-006', 'category' => 'BHP-DISINFEKTAN', 'name' => 'Hand Sanitizer Gel 500 ml',        'uom' => 'botol', 'type' => 'consumable'],
            ['code' => 'DISIN-007', 'category' => 'BHP-DISINFEKTAN', 'name' => 'Cairan Desinfektan Permukaan 1 L', 'uom' => 'botol', 'type' => 'consumable'],
            ['code' => 'DISIN-008', 'category' => 'BHP-DISINFEKTAN', 'name' => 'NaCl 0.9% Flushing 100 ml',        'uom' => 'botol', 'type' => 'consumable'],
            ['code' => 'DISIN-009', 'category' => 'BHP-DISINFEKTAN', 'name' => 'Hydrogen Peroxide 3% 100 ml',      'uom' => 'botol', 'type' => 'consumable'],

            // =================================================================
            // BHP-KEBERSIHAN — Alat & Bahan Kebersihan
            // =================================================================
            ['code' => 'BERSIH-001', 'category' => 'BHP-KEBERSIHAN', 'name' => 'Sabun Cuci Tangan Cair 500 ml',    'uom' => 'botol',  'type' => 'consumable'],
            ['code' => 'BERSIH-002', 'category' => 'BHP-KEBERSIHAN', 'name' => 'Sabun Cuci Piring 800 ml',         'uom' => 'botol',  'type' => 'consumable'],
            ['code' => 'BERSIH-003', 'category' => 'BHP-KEBERSIHAN', 'name' => 'Deterjen Bubuk 1 kg',              'uom' => 'kg',     'type' => 'consumable'],
            ['code' => 'BERSIH-004', 'category' => 'BHP-KEBERSIHAN', 'name' => 'Karbol / Pembersih Lantai 1 L',    'uom' => 'botol',  'type' => 'consumable'],
            ['code' => 'BERSIH-005', 'category' => 'BHP-KEBERSIHAN', 'name' => 'Kantong Sampah Hitam Besar',        'uom' => 'roll',   'type' => 'consumable'],
            ['code' => 'BERSIH-006', 'category' => 'BHP-KEBERSIHAN', 'name' => 'Kantong Sampah Kuning (Medis) Besar','uom' => 'roll',  'type' => 'consumable'],
            ['code' => 'BERSIH-007', 'category' => 'BHP-KEBERSIHAN', 'name' => 'Kantong Sampah Merah (Infeksius)',  'uom' => 'roll',   'type' => 'consumable'],
            ['code' => 'BERSIH-008', 'category' => 'BHP-KEBERSIHAN', 'name' => 'Tisu Gulung (1 roll)',              'uom' => 'roll',   'type' => 'consumable'],
            ['code' => 'BERSIH-009', 'category' => 'BHP-KEBERSIHAN', 'name' => 'Tisu Tangan / Facial Tissue (box)', 'uom' => 'box',    'type' => 'consumable'],
            ['code' => 'BERSIH-010', 'category' => 'BHP-KEBERSIHAN', 'name' => 'Sabun Mandi Bar',                   'uom' => 'pcs',    'type' => 'consumable'],
            ['code' => 'BERSIH-011', 'category' => 'BHP-KEBERSIHAN', 'name' => 'Sikat WC',                          'uom' => 'pcs',    'type' => 'consumable'],
            ['code' => 'BERSIH-012', 'category' => 'BHP-KEBERSIHAN', 'name' => 'Kain Lap / Majun',                  'uom' => 'kg',     'type' => 'consumable'],

            // =================================================================
            // BHP-ATK — Alat Tulis Kantor
            // =================================================================
            ['code' => 'ATK-001', 'category' => 'BHP-ATK', 'name' => 'Kertas HVS A4 70 gram',         'uom' => 'rim',  'type' => 'consumable'],
            ['code' => 'ATK-002', 'category' => 'BHP-ATK', 'name' => 'Kertas HVS F4 70 gram',         'uom' => 'rim',  'type' => 'consumable'],
            ['code' => 'ATK-003', 'category' => 'BHP-ATK', 'name' => 'Pulpen Ballpoint (hitam)',       'uom' => 'box',  'type' => 'consumable', 'desc' => 'isi 12 pcs/box'],
            ['code' => 'ATK-004', 'category' => 'BHP-ATK', 'name' => 'Pulpen Ballpoint (merah)',       'uom' => 'box',  'type' => 'consumable', 'desc' => 'isi 12 pcs/box'],
            ['code' => 'ATK-005', 'category' => 'BHP-ATK', 'name' => 'Spidol Permanent (hitam)',       'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ATK-006', 'category' => 'BHP-ATK', 'name' => 'Spidol Whiteboard (hitam)',      'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ATK-007', 'category' => 'BHP-ATK', 'name' => 'Staples No. 10',                'uom' => 'box',  'type' => 'consumable'],
            ['code' => 'ATK-008', 'category' => 'BHP-ATK', 'name' => 'Isi Staples No. 10',            'uom' => 'box',  'type' => 'consumable'],
            ['code' => 'ATK-009', 'category' => 'BHP-ATK', 'name' => 'Klip Kertas Kecil',             'uom' => 'box',  'type' => 'consumable'],
            ['code' => 'ATK-010', 'category' => 'BHP-ATK', 'name' => 'Klip Kertas Besar',             'uom' => 'box',  'type' => 'consumable'],
            ['code' => 'ATK-011', 'category' => 'BHP-ATK', 'name' => 'Map Plastik Transparan',        'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ATK-012', 'category' => 'BHP-ATK', 'name' => 'Map Snelhecter',                'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ATK-013', 'category' => 'BHP-ATK', 'name' => 'Amplop Coklat Besar',           'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ATK-014', 'category' => 'BHP-ATK', 'name' => 'Amplop Putih Kecil',            'uom' => 'box',  'type' => 'consumable', 'desc' => 'isi 50 pcs/box'],
            ['code' => 'ATK-015', 'category' => 'BHP-ATK', 'name' => 'Isolasi Bening',               'uom' => 'roll', 'type' => 'consumable'],
            ['code' => 'ATK-016', 'category' => 'BHP-ATK', 'name' => 'Double Tape',                   'uom' => 'roll', 'type' => 'consumable'],
            ['code' => 'ATK-017', 'category' => 'BHP-ATK', 'name' => 'Correction Fluid / Tipex',      'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ATK-018', 'category' => 'BHP-ATK', 'name' => 'Buku Tulis 58 lembar',          'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ATK-019', 'category' => 'BHP-ATK', 'name' => 'Buku Folio Bergaris',           'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ATK-020', 'category' => 'BHP-ATK', 'name' => 'Label Stiker Nama',             'uom' => 'pack', 'type' => 'consumable'],

            // =================================================================
            // BHP-PRINT — Bahan Cetak & Komputer
            // =================================================================
            ['code' => 'PRINT-001', 'category' => 'BHP-PRINT', 'name' => 'Tinta Printer Epson Black',      'uom' => 'botol', 'type' => 'consumable'],
            ['code' => 'PRINT-002', 'category' => 'BHP-PRINT', 'name' => 'Tinta Printer Epson Color Set',  'uom' => 'set',   'type' => 'consumable'],
            ['code' => 'PRINT-003', 'category' => 'BHP-PRINT', 'name' => 'Toner HP LaserJet',              'uom' => 'pcs',   'type' => 'consumable'],
            ['code' => 'PRINT-004', 'category' => 'BHP-PRINT', 'name' => 'Kertas Thermal 80mm (Kasir)',     'uom' => 'roll',  'type' => 'consumable'],

            // =================================================================
            // BHP-ELEKTRIK — Bahan Listrik & Teknis
            // =================================================================
            ['code' => 'ELEC-001', 'category' => 'BHP-ELEKTRIK', 'name' => 'Baterai AA',          'uom' => 'pack', 'type' => 'consumable', 'desc' => 'isi 4 pcs/pack'],
            ['code' => 'ELEC-002', 'category' => 'BHP-ELEKTRIK', 'name' => 'Baterai AAA',         'uom' => 'pack', 'type' => 'consumable', 'desc' => 'isi 4 pcs/pack'],
            ['code' => 'ELEC-003', 'category' => 'BHP-ELEKTRIK', 'name' => 'Baterai 9V (kotak)',  'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ELEC-004', 'category' => 'BHP-ELEKTRIK', 'name' => 'Lampu LED 10 Watt',   'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ELEC-005', 'category' => 'BHP-ELEKTRIK', 'name' => 'Lampu LED 15 Watt',   'uom' => 'pcs',  'type' => 'consumable'],
            ['code' => 'ELEC-006', 'category' => 'BHP-ELEKTRIK', 'name' => 'Kabel Ties Kecil',    'uom' => 'pack', 'type' => 'consumable'],
            ['code' => 'ELEC-007', 'category' => 'BHP-ELEKTRIK', 'name' => 'Lakban Hitam / Isolasi Listrik', 'uom' => 'roll', 'type' => 'consumable'],

            // =================================================================
            // INV-ALKES-UMUM — Alat Kesehatan Inventaris Umum
            // =================================================================
            ['code' => 'INV-001', 'category' => 'INV-ALKES-UMUM', 'name' => 'Tensimeter Digital',          'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-002', 'category' => 'INV-ALKES-UMUM', 'name' => 'Tensimeter Aneroid',          'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-003', 'category' => 'INV-ALKES-UMUM', 'name' => 'Stetoskop Dewasa',            'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-004', 'category' => 'INV-ALKES-UMUM', 'name' => 'Stetoskop Anak',              'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-005', 'category' => 'INV-ALKES-UMUM', 'name' => 'Termometer Digital',          'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-006', 'category' => 'INV-ALKES-UMUM', 'name' => 'Termometer Infrared',         'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-007', 'category' => 'INV-ALKES-UMUM', 'name' => 'Timbangan Badan Digital',     'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-008', 'category' => 'INV-ALKES-UMUM', 'name' => 'Timbangan Bayi',              'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-009', 'category' => 'INV-ALKES-UMUM', 'name' => 'Glucometer / Alat Cek Gula',  'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-010', 'category' => 'INV-ALKES-UMUM', 'name' => 'Pulse Oximeter',              'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-011', 'category' => 'INV-ALKES-UMUM', 'name' => 'Nebulizer',                   'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-012', 'category' => 'INV-ALKES-UMUM', 'name' => 'Otoskop',                     'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-013', 'category' => 'INV-ALKES-UMUM', 'name' => 'Oftalmoskop',                 'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-014', 'category' => 'INV-ALKES-UMUM', 'name' => 'Pen Light / Senter Medis',    'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-015', 'category' => 'INV-ALKES-UMUM', 'name' => 'Reflex Hammer',               'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-016', 'category' => 'INV-ALKES-UMUM', 'name' => 'Suction Portable',            'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-017', 'category' => 'INV-ALKES-UMUM', 'name' => 'Ambu Bag Dewasa',             'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'INV-018', 'category' => 'INV-ALKES-UMUM', 'name' => 'Ambu Bag Anak',               'uom' => 'unit', 'type' => 'inventory'],

            // =================================================================
            // INV-FURNITUR-MEDIS — Furnitur Medis
            // =================================================================
            ['code' => 'FURN-MED-001', 'category' => 'INV-FURNITUR-MEDIS', 'name' => 'Tempat Tidur Pasien Manual',   'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-MED-002', 'category' => 'INV-FURNITUR-MEDIS', 'name' => 'Tempat Tidur Pasien Elektrik', 'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-MED-003', 'category' => 'INV-FURNITUR-MEDIS', 'name' => 'Brankar / Stretcher',          'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-MED-004', 'category' => 'INV-FURNITUR-MEDIS', 'name' => 'Kursi Roda',                   'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-MED-005', 'category' => 'INV-FURNITUR-MEDIS', 'name' => 'Troli Medis / Emergency Trolley', 'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-MED-006', 'category' => 'INV-FURNITUR-MEDIS', 'name' => 'Tiang Infus Stainless',        'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-MED-007', 'category' => 'INV-FURNITUR-MEDIS', 'name' => 'Meja Overbed / Meja Pasien',   'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-MED-008', 'category' => 'INV-FURNITUR-MEDIS', 'name' => 'Lemari Pasien / Loker Pasien', 'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-MED-009', 'category' => 'INV-FURNITUR-MEDIS', 'name' => 'Troli Instrumen Stainless',    'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-MED-010', 'category' => 'INV-FURNITUR-MEDIS', 'name' => 'Kulkas Obat / Vaksin',         'uom' => 'unit', 'type' => 'inventory'],

            // =================================================================
            // INV-FURNITUR-KANTOR — Furnitur Kantor
            // =================================================================
            ['code' => 'FURN-KAN-001', 'category' => 'INV-FURNITUR-KANTOR', 'name' => 'Meja Kerja',              'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-KAN-002', 'category' => 'INV-FURNITUR-KANTOR', 'name' => 'Kursi Kerja Beroda',      'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-KAN-003', 'category' => 'INV-FURNITUR-KANTOR', 'name' => 'Lemari Arsip',            'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-KAN-004', 'category' => 'INV-FURNITUR-KANTOR', 'name' => 'Filling Cabinet 4 Laci',  'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-KAN-005', 'category' => 'INV-FURNITUR-KANTOR', 'name' => 'Rak Besi 5 Susun',        'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'FURN-KAN-006', 'category' => 'INV-FURNITUR-KANTOR', 'name' => 'Whiteboard 120x90 cm',    'uom' => 'unit', 'type' => 'inventory'],

            // =================================================================
            // INV-KOMPUTER — Perangkat Komputer & IT
            // =================================================================
            ['code' => 'IT-001', 'category' => 'INV-KOMPUTER', 'name' => 'Personal Computer (PC)',       'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'IT-002', 'category' => 'INV-KOMPUTER', 'name' => 'Laptop',                       'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'IT-003', 'category' => 'INV-KOMPUTER', 'name' => 'Monitor 21"',                  'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'IT-004', 'category' => 'INV-KOMPUTER', 'name' => 'Printer Inkjet A4',            'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'IT-005', 'category' => 'INV-KOMPUTER', 'name' => 'Printer Laser A4',             'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'IT-006', 'category' => 'INV-KOMPUTER', 'name' => 'Printer Dot Matrix',           'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'IT-007', 'category' => 'INV-KOMPUTER', 'name' => 'Scanner Dokumen',              'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'IT-008', 'category' => 'INV-KOMPUTER', 'name' => 'UPS / Battery Backup',         'uom' => 'unit', 'type' => 'inventory'],
            ['code' => 'IT-009', 'category' => 'INV-KOMPUTER', 'name' => 'Switch Hub 8 Port',            'uom' => 'unit', 'type' => 'inventory'],
        ];

        foreach ($items as $item) {
            $category = $cats->get($item['category']);
            if (!$category) continue;

            $item = Item::firstOrCreate(
                ['code' => $item['code']],
                [
                    'category_id'    => $category->id,
                    'name'           => $item['name'],
                    'unit_of_measure'=> $item['uom'],
                    'item_type'      => $item['type'],
                    'description'    => $item['desc'] ?? null,
                    'is_active'      => true,
                ]
            );

            ItemStock::create([
                'item_id' => $item->id,
                'purchase_price' => rand(10000, 1000000),
                'selling_price' => rand(10000, 1000000),
                'quantity' => rand(50, 100),
                'minimum_stock' => rand(5, 10),
                'batch_number' => 'BATCH-001',
                'expired_date' => now()->addYear(),
                'notes' => 'Initial stock',
            ]);
        }
    }
}
