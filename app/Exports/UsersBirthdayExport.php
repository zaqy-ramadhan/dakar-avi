<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\EmployeeDetail;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class UsersBirthdayExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $birth_month;

    public function __construct($birth_month)
    {
        $this->birth_month = $birth_month;
    }

    public function collection()
    {
        $birthMonth = $this->birth_month;

        $employeeDetails = EmployeeDetail::with([
            'user.employeeJob.department',
            'user.employeeJob.group',
            'user.employeeJob.jobType',
            'user.employeeJob.position'
        ])
            ->whereMonth('birth_date', (int) $birthMonth)
            ->whereHas('user.employeeJob', function ($query) {
                $query->where('employment_status', true);
            })
            ->get()
            ->map(function ($detail) {
                $user = $detail->user;
                $job = $user && $user->employeeJob ? $user->employeeJob->last() : null;

                return [
                    'npk' => $user ? $user->npk : 'N/A',
                    'name' => $user ? $user->fullname : 'N/A',
                    'status_karyawan' => $job ? $job->job_status : 'N/A',
                    'jabatan' => $job && $job->position ? $job->position->position_name : 'N/A',
                    'department' => $job && $job->department ? $job->department->department_name : 'N/A',
                    'group' => $job && $job->group ? $job->group->group_name : 'N/A',
                    'type' => $job && $job->jobType ? $job->jobType->job_type_name : 'N/A',
                    'gender' => $detail->gender == 1 ? 'P' : ($detail->gender == 0 ? 'L' : 'N/A'),
                    'birth_date' => \Carbon\Carbon::parse($detail->birth_date)->isoFormat('D MMMM Y'),
                ];
            });
        // dd($employeeDetails);
        return $employeeDetails;
    }


    public function headings(): array
    {
        return [
            'NPK',
            'Nama Lengkap',
            'Status Karyawan',
            'Jabatan',
            'Departemen',
            'Group',
            'Type',
            'Jenis Kelamin',
            'Tanggal Lahir',
        ];
    }
}
