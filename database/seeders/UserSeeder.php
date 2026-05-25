<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $units = Unit::all()->keyBy('code');

        // ---------------------------------------------------------------
        // 1. Admin Sistem
        // ---------------------------------------------------------------
        User::firstOrCreate(
            ['email' => 'admin@rsi.com'],
            [
                'name'        => 'Administrator',
                'password'    => Hash::make('password'),
                'employee_id' => 'ADM-001',
                'role'        => 'admin',
                'unit_id'     => null,
                'is_active'   => true,
            ]
        );

        // ---------------------------------------------------------------
        // 2. Petugas Unit Sarpras
        // ---------------------------------------------------------------
        $unitSarpras = $units->get('SARPRAS');

        $sarprasUsers = [
            ['name' => 'Budi Santoso',    'email' => 'budi.sarpras@rsi.com',    'employee_id' => 'SRP-001'],
            ['name' => 'Dewi Rahayu',     'email' => 'dewi.sarpras@rsi.com',    'employee_id' => 'SRP-002'],
            ['name' => 'Agus Priyanto',   'email' => 'agus.sarpras@rsi.com',    'employee_id' => 'SRP-003'],
        ];

        foreach ($sarprasUsers as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name'        => $user['name'],
                    'password'    => Hash::make('password'),
                    'employee_id' => $user['employee_id'],
                    'role'        => 'sarpras',
                    'unit_id'     => $unitSarpras?->id,
                    'is_active'   => true,
                ]
            );
        }

        // ---------------------------------------------------------------
        // 3. Perwakilan User dari Setiap Unit
        // ---------------------------------------------------------------
        $unitUsers = [
            ['unit' => 'IGD',        'name' => 'Siti Nurhaliza',   'email' => 'petugas.igd@rsi.com',       'employee_id' => 'USR-001'],
            ['unit' => 'ICU',        'name' => 'Rina Marlina',      'email' => 'petugas.icu@rsi.com',       'employee_id' => 'USR-002'],
            ['unit' => 'OK',         'name' => 'Hendra Wijaya',     'email' => 'petugas.ok@rsi.com',        'employee_id' => 'USR-003'],
            ['unit' => 'RI-UMUM',    'name' => 'Fitriani Kusuma',   'email' => 'petugas.ri.umum@rsi.com',   'employee_id' => 'USR-004'],
            ['unit' => 'RI-ANAK',    'name' => 'Yuli Astuti',       'email' => 'petugas.ri.anak@rsi.com',   'employee_id' => 'USR-005'],
            ['unit' => 'RI-KEBID',   'name' => 'Nining Suryani',    'email' => 'petugas.ri.kebid@rsi.com',  'employee_id' => 'USR-006'],
            ['unit' => 'RI-BEDAH',   'name' => 'Wahyu Setiawan',    'email' => 'petugas.ri.bedah@rsi.com',  'employee_id' => 'USR-007'],
            ['unit' => 'POLI-UMUM',  'name' => 'Anita Permata',     'email' => 'petugas.poli.umum@rsi.com', 'employee_id' => 'USR-008'],
            ['unit' => 'POLI-ANAK',  'name' => 'Lestari Wulandari', 'email' => 'petugas.poli.anak@rsi.com', 'employee_id' => 'USR-009'],
            ['unit' => 'LAB',        'name' => 'Bambang Sugiarto',  'email' => 'petugas.lab@rsi.com',       'employee_id' => 'USR-010'],
            ['unit' => 'RADIOLOGI',  'name' => 'Dian Pratiwi',      'email' => 'petugas.rad@rsi.com',       'employee_id' => 'USR-011'],
            ['unit' => 'FARMASI',    'name' => 'Rizky Aditya',      'email' => 'petugas.farmasi@rsi.com',   'employee_id' => 'USR-012'],
            ['unit' => 'GIZI',       'name' => 'Mira Sari',         'email' => 'petugas.gizi@rsi.com',      'employee_id' => 'USR-013'],
            ['unit' => 'KEUANGAN',   'name' => 'Hendri Kurniawan',  'email' => 'petugas.keuangan@rsi.com',  'employee_id' => 'USR-014'],
            ['unit' => 'SDM',        'name' => 'Tika Anggraini',    'email' => 'petugas.sdm@rsi.com',       'employee_id' => 'USR-015'],
            ['unit' => 'IT',         'name' => 'Fajar Nugroho',     'email' => 'petugas.it@rsi.com',        'employee_id' => 'USR-016'],
        ];

        foreach ($unitUsers as $user) {
            $unit = $units->get($user['unit']);
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name'        => $user['name'],
                    'password'    => Hash::make('password'),
                    'employee_id' => $user['employee_id'],
                    'role'        => 'unit',
                    'unit_id'     => $unit?->id,
                    'is_active'   => true,
                ]
            );
        }
    }
}
