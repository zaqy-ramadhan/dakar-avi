<?php

namespace App\Exports;

use App\Models\EmployeeJob;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Carbon;

class JoinedThisMonthExport implements FromCollection, WithHeadings
{
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
            ->whereHas('user')
            ->whereBetween('start_date', [
                Carbon::createFromDate($year, $month, 1)->startOfMonth(),
                Carbon::createFromDate($year, $month, 1)->endOfMonth()
            ])
            ->where('job_status', 'kontrak')
            ->where('user_dakar_role', 'karyawan')
            ->get()
            ->map(function ($job) {
                return [
                    'npk' => $job->user?->npk ?? 'N/A',
                    'name' => $job->user?->fullname ?? 'N/A',
                    'department' => $job->department?->department_name ?? 'N/A',
                    'join_date' => $job->user?->join_date
                        ? Carbon::parse($job->user->join_date)->isoFormat('D MMMM Y')
                        : ($job->user?->firstEmployeeJob?->start_date
                            ? Carbon::parse($job->user->firstEmployeeJob->start_date)->isoFormat('D MMMM Y')
                            : 'N/A'),
                    'start_date' => $job->start_date
                        ? Carbon::parse($job->start_date)->isoFormat('D MMMM Y')
                        : 'N/A',
                    'end_date' => $job->end_date
                        ? Carbon::parse($job->end_date)->isoFormat('D MMMM Y')
                        : 'N/A',
                    'status' => $job->contract,
                ];
            });

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
