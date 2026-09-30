<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NumberGeneratorService
{
    /**
     * Generate nomor permintaan: REQ-YYYY-XXXX
     * Contoh: REQ-2024-0001
     */
    public function generateRequestNumber(): string
    {
        return $this->generate('requests', 'request_number', 'REQ');
    }

    /**
     * Generate nomor PO: PO-YYYY-XXXX
     * Contoh: PO-2024-0001
     */
    public function generatePONumber(): string
    {
        return $this->generate('purchase_orders', 'po_number', 'PO');
    }

    /**
     * Generate nomor opname: OPN-YYYY-XXXX (sekuens per tahun agar unik
     * walaupun beberapa opname dibuat dalam bulan yang sama).
     * Contoh: OPN-2024-0001
     *
     * Sengaja tidak memakai helper generate(): nomor lama berformat
     * OPN-YYYY-MM selalu menang pada perbandingan string max()
     * sehingga sekuens akan macet. Di sini sekuens dihitung dari
     * segmen numerik terakhir secara eksplisit.
     */
    public function generateOpnameNumber(): string
    {
        $year = now()->format('Y');

        // Lock untuk mencegah duplicate di concurrent request
        $numbers = DB::table('stock_opname')
            ->where('opname_number', 'like', "OPN-{$year}-%")
            ->lockForUpdate()
            ->pluck('opname_number');

        $seq = $numbers
            ->map(fn($n) => (int) substr(strrchr($n, '-'), 1))
            ->max() ?? 0;

        return sprintf('OPN-%s-%04d', $year, $seq + 1);
    }

    private function generate(string $table, string $column, string $prefix): string
    {
        $year = now()->format('Y');

        // Lock untuk mencegah duplicate di concurrent request
        $lastNumber = DB::table($table)
            ->where($column, 'like', "{$prefix}-{$year}-%")
            ->lockForUpdate()
            ->max($column);

        if ($lastNumber) {
            $seq = (int) substr($lastNumber, -4);
        } else {
            $seq = 0;
        }

        return sprintf('%s-%s-%04d', $prefix, $year, $seq + 1);
    }
}
