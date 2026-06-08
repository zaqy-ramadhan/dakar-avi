<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class EmployeeExport implements FromCollection, WithHeadings, WithColumnFormatting
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
                'gender' => $item['gender'],
                'age' => $item['age'],
                'email' => $item['email'],
                'education' => $item['education'],
                'blood_type' => $item['blood_type'],
                'join_date' => $item['join_date_display'] !== 'N/A' ? Carbon::createFromFormat('d/m/Y', $item['join_date_display']) : 'N/A',
                'start_date' => $item['start_date_display'] !== 'N/A' ? Carbon::createFromFormat('d/m/Y', $item['start_date_display']) : 'N/A',
                'end_date' => $item['end_date_display'] !== 'N/A' ? Carbon::createFromFormat('d/m/Y', $item['end_date_display']) : 'N/A',
                'duration' => $item['duration'],
                'LOS' => $item['LOS'],
                'department' => $item['department'],
                'employment_status' => $item['employment_status'],
                'job_status' => $item['job_status'],
                'job_type' => $item['job_type'],
                'gol' => $item['gol'],
                'status' => $item['status'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NPK', 'Fullname', 'Gender', 'Age', 'Email', 'Education', 'Blood Type',
            'Join Date', 'Start Date', 'End Date', 'Duration', 'LOS',
            'Department', 'Employment Status', 'Job Status', 'Job Type', 'Golongan', 'Status'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => 'dd/mm/yyyy',  // Join Date
            'I' => 'dd/mm/yyyy',  // Start Date
            'J' => 'dd/mm/yyyy',  // End Date
        ];
    }
}
