<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeExportMin implements FromCollection, WithHeadings
{
    protected $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection->map(function ($item) {
            return [
                'npk' => $item['npk'],
                'fullname' => $item['fullname'],
                'nik' => $item['nik'],
                'no_phone' => $item['no_phone'],
                'status' => $item['status'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NPK', 'Fullname', 'NIK', 'No. Phone', 'Status'
        ];
    }
}
