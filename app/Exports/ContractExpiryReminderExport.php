<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ContractExpiryReminderExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function collection()
    {
        return collect($this->employees)->map(function ($employee) {
            $job = $employee->current_job ?? $employee->employeeJob?->first();
            
            return [
                'NPK' => $employee->npk ?? '-',
                'Nama' => $employee->fullname ?? '-',
                'Posisi' => $employee->position_name ?? $job?->position?->position_name ?? '-',
                'Departemen' => $employee->department_name ?? $job?->department?->department_name ?? '-',
                'Divisi' => $employee->division_name ?? $job?->division?->division_name ?? '-',
                'Tanggal Mulai Kontrak' => $job?->start_date?->format('d/m/Y') ?? '-',
                'Tanggal Akhir Kontrak' => $job?->end_date?->format('d/m/Y') ?? '-',
                'Sisa Hari' => (int)($employee->remaining_days ?? 0),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NPK',
            'Nama',
            'Posisi',
            'Departemen',
            'Divisi',
            'Tanggal Mulai Kontrak',
            'Tanggal Akhir Kontrak',
            'Sisa Hari',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 25,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 18,
            'G' => 18,
            'H' => 12,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->employees) + 1;

        // Header styling
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4788'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Data rows styling
        $sheet->getStyle("A2:H{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D3D3D3'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Alternating row colors
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:H{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F5F5'],
                    ],
                ]);
            }
        }

        // Center alignment for specific columns
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F2:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
