<?php

namespace App\Exports;

use App\Models\Request as RequestModel;
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

class RequestsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    public function __construct(private readonly array $filters) {}

    public function query()
    {
        $query = RequestModel::with(['requester', 'unit', 'reviewer', 'server', 'items.item'])
            ->latest('request_date');

        if (!empty($this->filters['unit_id']))   $query->where('unit_id', $this->filters['unit_id']);
        if (!empty($this->filters['status']))    $query->where('status', $this->filters['status']);
        if (!empty($this->filters['priority']))  $query->where('priority', $this->filters['priority']);
        if (!empty($this->filters['date_from'])) $query->whereDate('request_date', '>=', $this->filters['date_from']);
        if (!empty($this->filters['date_to']))   $query->whereDate('request_date', '<=', $this->filters['date_to']);

        return $query;
    }

    public function title(): string
    {
        return 'Laporan Permintaan';
    }

    public function headings(): array
    {
        return [
            'No.',
            'No. Permintaan',
            'Unit',
            'Pengaju',
            'Prioritas',
            'Status',
            'Tgl. Ajuan',
            'Tgl. Butuh',
            'Jumlah Item',
            'Direview Oleh',
            'Disiapkan Oleh',
            'Diambil Oleh',
            'Catatan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $statusLabels = [
            'pending'   => 'Menunggu Review',
            'approved'  => 'Disetujui',
            'partial'   => 'Disetujui Sebagian',
            'rejected'  => 'Ditolak',
            'ready'     => 'Siap Diambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $priorityLabels = [
            'low'    => 'Rendah',
            'normal' => 'Normal',
            'high'   => 'Tinggi',
            'urgent' => 'Mendesak',
        ];

        return [
            $no,
            $row->request_number,
            $row->unit->name ?? '—',
            $row->requester->name ?? '—',
            $priorityLabels[$row->priority] ?? $row->priority,
            $statusLabels[$row->status->value] ?? $row->status->value,
            $row->request_date->format('d/m/Y'),
            $row->needed_date?->format('d/m/Y') ?? '—',
            $row->items->count(),
            $row->reviewer->name ?? '—',
            $row->server->name ?? '—',
            $row->received_by_name ?? '—',
            $row->notes ?? '—',
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
                $sheet      = $event->sheet->getDelegate();
                $lastRow    = $sheet->getHighestRow();
                $lastCol    = $sheet->getHighestColumn();

                // Border seluruh tabel
                $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFD1D5DB'],
                        ],
                    ],
                ]);

                // Zebra stripe
                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF9FAFB']],
                        ]);
                    }
                }

                // Tambah header info laporan di baris pertama
                $sheet->insertNewRowBefore(1, 3);
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', 'LAPORAN PERMINTAAN BARANG');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->setCellValue('A2', 'Dicetak: ' . now()->translatedFormat('d F Y, H:i'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->getRowDimension(3)->setRowHeight(5);
            },
        ];
    }
}
