<?php

namespace App\Exports;

use App\Models\StockOpname;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class StockOpnameExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    public function __construct(private readonly StockOpname $opname) {}

    public function collection(): Collection
    {
        return $this->opname->items()
            ->with(['item.category', 'counter'])
            ->orderBy('difference')
            ->get();
    }

    public function title(): string
    {
        return 'Opname ' . $this->opname->opname_number;
    }

    public function headings(): array
    {
        return ['No.', 'Kode Barang', 'Nama Barang', 'Kategori', 'Satuan', 'Stok Sistem', 'Stok Fisik', 'Selisih', 'Keterangan Selisih', 'Dihitung Oleh', 'Catatan'];
    }

    public function map($oi): array
    {
        static $no = 0;
        $no++;

        $diff  = $oi->difference;
        $keterangan = match (true) {
            $diff === null => '—',
            $diff === 0    => 'Sesuai',
            $diff > 0      => 'Surplus (+' . $diff . ')',
            default        => 'Defisit (' . $diff . ')',
        };

        return [
            $no,
            $oi->item->code ?? '—',
            $oi->item->name ?? '—',
            $oi->item->category->name ?? '—',
            $oi->item->unit_of_measure ?? '—',
            $oi->system_quantity,
            $oi->physical_quantity ?? '—',
            $diff ?? '—',
            $keterangan,
            $oi->counter?->name ?? '—',
            $oi->notes ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = $sheet->getHighestColumn();

                $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
                ]);

                // Warna baris berdasarkan selisih (kolom H)
                for ($row = 2; $row <= $lastRow; $row++) {
                    $diff = $sheet->getCell("H{$row}")->getValue();
                    if (is_numeric($diff)) {
                        $color = match (true) {
                            $diff < 0 => 'FFFEE2E2', // merah muda - defisit
                            $diff > 0 => 'FFECFDF5', // hijau muda - surplus
                            default   => null,
                        };
                        if ($color) {
                            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
                            ]);
                        }
                    }
                }

                // Header info di atas tabel
                $sheet->insertNewRowBefore(1, 5);
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', 'LAPORAN HASIL STOK OPNAME');
                $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

                $sheet->setCellValue('A2', 'No. Opname : ' . $this->opname->opname_number);
                $sheet->setCellValue('A3', 'Tgl. Opname : ' . $this->opname->opname_date->format('d/m/Y'));
                $sheet->setCellValue('D2', 'Dibuat Oleh : ' . ($this->opname->creator->name ?? '—'));
                $sheet->setCellValue('D3', 'Ditutup Oleh : ' . ($this->opname->closer?->name ?? '—'));
                $sheet->setCellValue('A4', 'Dicetak : ' . now()->translatedFormat('d F Y, H:i'));
                $sheet->getRowDimension(5)->setRowHeight(5);
            },
        ];
    }
}
