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
     * Generate nomor opname: OPN-YYYY-MM
     * Contoh: OPN-2024-01
     */
    public function generateOpnameNumber(): string
    {
        $year  = now()->format('Y');
        $month = now()->format('m');
        return "OPN-{$year}-{$month}";
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
