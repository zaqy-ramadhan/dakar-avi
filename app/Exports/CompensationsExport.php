<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CompensationsExport implements FromCollection, WithHeadings, WithColumnFormatting, WithStyles
{
    protected $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection->map(function ($item, $index) {
            $contractDoc = $item->jobDoc->firstWhere('type', 'contract');
            
            return [
                'no' => $index + 1,
                'npk' => $item->user->npk ?? 'N/A',
                'nama_karyawan' => $item->user->fullname ?? 'N/A',
                'departemen' => $item->department->department_name ?? 'N/A',
                'posisi' => $item->position->position_name ?? 'N/A',
                'job_type' => $item->jobType->job_type_name ?? 'N/A',
                'start_date' => $item->start_date ? $item->start_date->format('d/m/Y') : 'N/A',
                'end_date' => $item->end_date ? $item->end_date->format('d/m/Y') : 'N/A',
                'durasi_kontrak' => $item->duration() ?? 'N/A',
                'status_kontrak' => $contractDoc && $contractDoc->first_party_signature ? 'Sudah Ditandatangani' : 'Belum Ditandatangani',
                'status_employment' => $item->employment_status ? 'Aktif' : 'Non-Aktif',
                'tanggal_dibuat' => $item->created_at ? $item->created_at->format('d/m/Y H:i') : 'N/A',
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
            'Tipe Pekerjaan',
            'Tanggal Mulai',
            'Tanggal Berakhir',
            'Durasi Kontrak',
            'Status Kontrak',
            'Status Employment',
            'Tanggal Dibuat'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => 'dd/mm/yyyy',
            'H' => 'dd/mm/yyyy',
            'L' => 'dd/mm/yyyy hh:mm',
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);
        $sheet->getStyle('1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        return [];
    }
}
