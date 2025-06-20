<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeExport implements FromCollection, WithHeadings
{
    protected $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        // return [
        //     'NPK', 'Fullname', 'Gender', 'Age', 'Education', 'Blood Type',
        //     'Join Date', 'Start Date', 'End Date', 'Duration', 'LOS',
        //     'Department', 'Employment Status', 'Job Status', 'Job Type', 'Golongan', 'Status'
        // ];
          return $this->collection->first()
            ? array_keys($this->collection->first())
            : [];
    }
}
