<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromQuery;
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

class ItemStocksExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    public function __construct(private readonly array $filters) {}

    public function query()
    {
        $query = Item::with(['category', 'stocks'])
            ->withSum('stocks as total_stock', 'quantity')
            ->active()->orderBy('name');

        if (!empty($this->filters['item_type']))   $query->where('item_type', $this->filters['item_type']);
        if (!empty($this->filters['category_id'])) $query->where('category_id', $this->filters['category_id']);

        if (!empty($this->filters['stock_status'])) {
            match ($this->filters['stock_status']) {
                'low'   => $query->whereHas('stocks', fn($q) => $q->whereColumn('quantity', '<=', 'minimum_stock')->where('minimum_stock', '>', 0)),
                'empty' => $query->whereDoesntHave('stocks', fn($q) => $q->where('quantity', '>', 0)),
                default => null,
            };
        }

        return $query;
    }

    public function title(): string
    {
        return 'Laporan Stok Barang';
    }

    public function headings(): array
    {
        return ['No.', 'Kode', 'Nama Barang', 'Kategori', 'Tipe', 'Satuan', 'Total Stok', 'Jumlah Batch', 'Status Stok'];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        $total       = (int)($item->total_stock ?? 0);
        $batchCount  = $item->stocks->count();
        $isLow       = $item->stocks->filter(fn($s) => $s->minimum_stock > 0 && $s->quantity <= $s->minimum_stock)->isNotEmpty();
        $stockStatus = $total === 0 ? 'Habis' : ($isLow ? 'Rendah' : 'Normal');

        return [
            $no,
            $item->code,
            $item->name,
            $item->category->name ?? '—',
            $item->item_type->label(),
            $item->unit_of_measure,
            $total,
            $batchCount,
            $stockStatus,
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

                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF9FAFB']],
                        ]);
                    }
                }

                // Warna sel status stok
                for ($row = 2; $row <= $lastRow; $row++) {
                    $status = $sheet->getCell("I{$row}")->getValue();
                    $color  = match ($status) {
                        'Habis'  => 'FFFEE2E2',
                        'Rendah' => 'FFFEF9C3',
                        default  => null,
                    };
                    if ($color) {
                        $sheet->getStyle("I{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
                        ]);
                    }
                }

                $sheet->insertNewRowBefore(1, 3);
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', 'LAPORAN KONDISI STOK BARANG');
                $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->setCellValue('A2', 'Dicetak: ' . now()->translatedFormat('d F Y, H:i'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension(3)->setRowHeight(5);
            },
        ];
    }
}
