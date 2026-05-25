<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            // Rawat Inap
            ['code' => 'RI-UMUM',   'name' => 'Rawat Inap Umum'],
            ['code' => 'RI-ANAK',   'name' => 'Rawat Inap Anak'],
            ['code' => 'RI-KEBID',  'name' => 'Rawat Inap Kebidanan'],
            ['code' => 'RI-BEDAH',  'name' => 'Rawat Inap Bedah'],
            ['code' => 'RI-SARAF',  'name' => 'Rawat Inap Saraf'],
            ['code' => 'RI-JANTUNG','name' => 'Rawat Inap Jantung'],

            // Rawat Jalan / Poliklinik
            ['code' => 'POLI-UMUM', 'name' => 'Poliklinik Umum'],
            ['code' => 'POLI-ANAK', 'name' => 'Poliklinik Anak'],
            ['code' => 'POLI-OBGYN','name' => 'Poliklinik Kebidanan & Kandungan'],
            ['code' => 'POLI-BEDAH','name' => 'Poliklinik Bedah'],
            ['code' => 'POLI-DALAM','name' => 'Poliklinik Penyakit Dalam'],
            ['code' => 'POLI-MATA', 'name' => 'Poliklinik Mata'],
            ['code' => 'POLI-THT',  'name' => 'Poliklinik THT'],
            ['code' => 'POLI-KULIT','name' => 'Poliklinik Kulit & Kelamin'],
            ['code' => 'POLI-SARAF','name' => 'Poliklinik Saraf'],
            ['code' => 'POLI-GIGI', 'name' => 'Poliklinik Gigi & Mulut'],

            // Unit Kritis & Penunjang Medis
            ['code' => 'IGD',       'name' => 'Instalasi Gawat Darurat'],
            ['code' => 'ICU',       'name' => 'Intensive Care Unit'],
            ['code' => 'ICCU',      'name' => 'Intensive Coronary Care Unit'],
            ['code' => 'NICU',      'name' => 'Neonatal Intensive Care Unit'],
            ['code' => 'PICU',      'name' => 'Pediatric Intensive Care Unit'],
            ['code' => 'OK',        'name' => 'Kamar Operasi'],
            ['code' => 'VK',        'name' => 'Kamar Bersalin'],
            ['code' => 'HD',        'name' => 'Hemodialisa'],

            // Penunjang Diagnostik
            ['code' => 'LAB',       'name' => 'Laboratorium'],
            ['code' => 'RADIOLOGI', 'name' => 'Radiologi'],
            ['code' => 'FISIOTERAPI','name' => 'Fisioterapi'],
            ['code' => 'FARMASI',   'name' => 'Farmasi / Apotek'],
            ['code' => 'GIZI',      'name' => 'Instalasi Gizi'],
            ['code' => 'CSSD',      'name' => 'Central Sterile Supply Department'],

            // Administrasi & Pendukung
            ['code' => 'SARPRAS',   'name' => 'Sarana dan Prasarana'],
            ['code' => 'KEUANGAN',  'name' => 'Keuangan & Akuntansi'],
            ['code' => 'SDM',       'name' => 'Sumber Daya Manusia'],
            ['code' => 'IT',        'name' => 'Teknologi Informasi'],
            ['code' => 'HUMAS',     'name' => 'Hubungan Masyarakat'],
            ['code' => 'SECURITY',  'name' => 'Keamanan'],
            ['code' => 'LAUNDRY',   'name' => 'Laundry & Linen'],
            ['code' => 'SANITASI',  'name' => 'Sanitasi & Kesehatan Lingkungan'],
            ['code' => 'AMBULANCE', 'name' => 'Ambulance & Transportasi'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['code' => $unit['code']],
                ['name' => $unit['name'], 'is_active' => true]
            );
        }
    }
}
