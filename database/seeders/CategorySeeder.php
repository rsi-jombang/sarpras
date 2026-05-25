<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // ---------------------------------------------------------------
            // BARANG HABIS PAKAI (consumable)
            // ---------------------------------------------------------------
            [
                'code'      => 'BHP-ALKES',
                'name'      => 'Alat Kesehatan Habis Pakai',
                'item_type' => 'consumable',
                'description' => 'Alat kesehatan sekali pakai: spuit, infus set, kateter, dll.',
            ],
            [
                'code'      => 'BHP-ALKES-STERIL',
                'name'      => 'Alat Kesehatan Steril',
                'item_type' => 'consumable',
                'description' => 'Kasa steril, kapas steril, sarung tangan steril, dll.',
            ],
            [
                'code'      => 'BHP-LINEN',
                'name'      => 'Linen & Tekstil Medis',
                'item_type' => 'consumable',
                'description' => 'Verban, kasa gulung, plester, perban elastis, dll.',
            ],
            [
                'code'      => 'BHP-LAB',
                'name'      => 'Bahan & Reagen Laboratorium',
                'item_type' => 'consumable',
                'description' => 'Reagen, tabung sampel, strip test, dll.',
            ],
            [
                'code'      => 'BHP-RADIOLOGI',
                'name'      => 'Bahan Radiologi',
                'item_type' => 'consumable',
                'description' => 'Film rontgen, kertas USG, kontras media, dll.',
            ],
            [
                'code'      => 'BHP-OK',
                'name'      => 'Bahan Kamar Operasi',
                'item_type' => 'consumable',
                'description' => 'Benang operasi, drain, stapler, dll.',
            ],
            [
                'code'      => 'BHP-GIGI',
                'name'      => 'Bahan Habis Pakai Gigi',
                'item_type' => 'consumable',
                'description' => 'Tambal gigi, bahan cetak gigi, dll.',
            ],
            [
                'code'      => 'BHP-APD',
                'name'      => 'Alat Pelindung Diri (APD)',
                'item_type' => 'consumable',
                'description' => 'Masker, hazmat, face shield, goggle, apron, dll.',
            ],
            [
                'code'      => 'BHP-DISINFEKTAN',
                'name'      => 'Desinfektan & Antiseptik',
                'item_type' => 'consumable',
                'description' => 'Alkohol, betadine, chlorhexidine, cairan pembersih luka, dll.',
            ],
            [
                'code'      => 'BHP-CSSD',
                'name'      => 'Bahan CSSD',
                'item_type' => 'consumable',
                'description' => 'Pouches sterilisasi, indikator steril, dll.',
            ],
            [
                'code'      => 'BHP-KEBERSIHAN',
                'name'      => 'Alat & Bahan Kebersihan',
                'item_type' => 'consumable',
                'description' => 'Sabun, deterjen, pel, sapu, kantong sampah, tisu, dll.',
            ],
            [
                'code'      => 'BHP-ATK',
                'name'      => 'Alat Tulis Kantor (ATK)',
                'item_type' => 'consumable',
                'description' => 'Kertas, pulpen, map, buku, staples, dll.',
            ],
            [
                'code'      => 'BHP-PRINT',
                'name'      => 'Bahan Cetak & Komputer',
                'item_type' => 'consumable',
                'description' => 'Tinta printer, toner, kertas foto, label, dll.',
            ],
            [
                'code'      => 'BHP-FORM',
                'name'      => 'Formulir & Dokumen',
                'item_type' => 'consumable',
                'description' => 'Formulir rekam medis, resep, surat keterangan, dll.',
            ],
            [
                'code'      => 'BHP-DAPUR',
                'name'      => 'Bahan Habis Pakai Dapur & Gizi',
                'item_type' => 'consumable',
                'description' => 'Plastik wrap, aluminium foil, wadah makan sekali pakai, dll.',
            ],
            [
                'code'      => 'BHP-ELEKTRIK',
                'name'      => 'Bahan Listrik & Teknis',
                'item_type' => 'consumable',
                'description' => 'Baterai, lampu, kabel ties, fuse, dll.',
            ],

            // ---------------------------------------------------------------
            // INVENTARIS
            // ---------------------------------------------------------------
            [
                'code'      => 'INV-ALKES-UMUM',
                'name'      => 'Alat Kesehatan Umum',
                'item_type' => 'inventory',
                'description' => 'Tensimeter, stetoskop, timbangan, termometer, dll.',
            ],
            [
                'code'      => 'INV-ALKES-KHUSUS',
                'name'      => 'Alat Kesehatan Khusus',
                'item_type' => 'inventory',
                'description' => 'Nebulizer, pulse oximeter, glucometer, EKG portable, dll.',
            ],
            [
                'code'      => 'INV-ELEKTROMEDIK',
                'name'      => 'Peralatan Elektromedik',
                'item_type' => 'inventory',
                'description' => 'Ventilator, infus pump, syringe pump, monitor pasien, dll.',
            ],
            [
                'code'      => 'INV-BEDAH',
                'name'      => 'Instrumen & Peralatan Bedah',
                'item_type' => 'inventory',
                'description' => 'Set instrumen bedah, klem, forcep, gunting medis, dll.',
            ],
            [
                'code'      => 'INV-FURNITUR-MEDIS',
                'name'      => 'Furnitur Medis',
                'item_type' => 'inventory',
                'description' => 'Tempat tidur pasien, troli medis, brankar, kursi roda, dll.',
            ],
            [
                'code'      => 'INV-FURNITUR-KANTOR',
                'name'      => 'Furnitur Kantor & Umum',
                'item_type' => 'inventory',
                'description' => 'Meja, kursi, lemari, rak, filling cabinet, dll.',
            ],
            [
                'code'      => 'INV-KOMPUTER',
                'name'      => 'Perangkat Komputer & IT',
                'item_type' => 'inventory',
                'description' => 'PC, laptop, printer, scanner, router, UPS, dll.',
            ],
            [
                'code'      => 'INV-KOMUNIKASI',
                'name'      => 'Peralatan Komunikasi',
                'item_type' => 'inventory',
                'description' => 'Telepon, HT, intercom, dll.',
            ],
            [
                'code'      => 'INV-DAPUR',
                'name'      => 'Peralatan Dapur & Gizi',
                'item_type' => 'inventory',
                'description' => 'Troli makan, wadah makanan, peralatan masak, dll.',
            ],
            [
                'code'      => 'INV-LAUNDRY',
                'name'      => 'Peralatan Laundry',
                'item_type' => 'inventory',
                'description' => 'Mesin cuci, mesin pengering, setrika, dll.',
            ],
            [
                'code'      => 'INV-KEBERSIHAN',
                'name'      => 'Peralatan Kebersihan',
                'item_type' => 'inventory',
                'description' => 'Vacuum cleaner, mesin poles lantai, troli kebersihan, dll.',
            ],
            [
                'code'      => 'INV-KEAMANAN',
                'name'      => 'Peralatan Keamanan',
                'item_type' => 'inventory',
                'description' => 'CCTV, metal detector, tabung pemadam, dll.',
            ],
            [
                'code'      => 'INV-LISTRIK',
                'name'      => 'Peralatan Listrik & Mekanikal',
                'item_type' => 'inventory',
                'description' => 'Genset, stabilizer, panel listrik, pompa air, dll.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['code' => $cat['code']],
                [
                    'name'        => $cat['name'],
                    'item_type'   => $cat['item_type'],
                    'description' => $cat['description'],
                    'is_active'   => true,
                ]
            );
        }
    }
}
