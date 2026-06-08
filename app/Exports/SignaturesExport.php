<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SignaturesExport implements FromCollection, WithHeadings, WithColumnFormatting, WithStyles
{
    protected $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection->map(function ($item, $index) {
            return [
                'no' => $index + 1,
                'npk' => $item->employeeJob->user->npk ?? 'N/A',
                'nama_karyawan' => $item->employeeJob->user->fullname ?? 'N/A',
                'departemen' => $item->employeeJob->department->department_name ?? 'N/A',
                'posisi' => $item->employeeJob->position->position_name ?? 'N/A',
                'start_date' => $item->employeeJob->start_date ? $item->employeeJob->start_date->format('d/m/Y') : 'N/A',
                'end_date' => $item->employeeJob->end_date ? $item->employeeJob->end_date->format('d/m/Y') : 'N/A',
                'tipe_dokumen' => ucfirst($item->type),
                'status_signature' => $item->first_party_signature ? 'Sudah Ditandatangani' : 'Belum Ditandatangani',
                'tanggal_upload' => $item->created_at ? $item->created_at->format('d/m/Y H:i') : 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'NPK',
            'Nama Karyawan',
            'Departemen',
            'Posisi',
            'Tanggal Mulai',
            'Tanggal Berakhir',
            'Tipe Dokumen',
            'Status Signature',
            'Tanggal Upload'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => 'dd/mm/yyyy',
            'G' => 'dd/mm/yyyy',
            'J' => 'dd/mm/yyyy hh:mm',
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);
        $sheet->getStyle('1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        return [];
    }
}
