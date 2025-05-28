<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\EmployeeDetail;
use App\Models\EmployeeJob;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ContractExpiredExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        $month = $this->month;
        $year = $this->year;

        $employeeJob = EmployeeJob::with(['department', 'user'])
                        ->where('job_status', 'kontrak')
                        ->where('user_dakar_role', 'karyawan')
                        ->whereMonth('end_date', $month)
                        ->whereYear('end_date', $year)
                        ->get()
                        ->map(function ($job) {
                            return [
                                'npk' => $job->user ? $job->user->npk : 'N/A',
                                'name' => $job->user ? $job->user->fullname : 'N/A',
                                'department' => $job->department ? $job->department->department_name : 'N/A',
                                'join_date' => $job->user->join_date ?? 'N/A',
                                'start_date' => $job->start_date ? \Carbon\Carbon::parse($job->start_date)->isoFormat('D MMMM Y') : 'N/A',
                                'end_date' => $job->end_date ? \Carbon\Carbon::parse($job->end_date)->isoFormat('D MMMM Y') : 'N/A',
                                'status' => $job->contract,
                            ];
                        });
        // dd($employeeJob);
        return $employeeJob;
    }


    public function headings(): array
    {
        return [
            'NPK',
            'Nama Lengkap',
            'Departemen',
            'Join Date',
            'Start Date',
            'End Date',
            'Status',
        ];
    }
}
