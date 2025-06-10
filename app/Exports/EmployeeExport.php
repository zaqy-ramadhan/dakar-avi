<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $query = User::whereHas('employeeDetail')
            ->whereHas('latestEmployeeJob')
            ->whereHas('firstEmployeeJob')
            ->with(['employeeDetail', 'latestEmployeeJob', 'firstEmployeeJob']);

        // Apply filters
        if ($department = request('department')) {
            $query->whereHas('latestEmployeeJob.department', function ($q) use ($department) {
                $q->where('department_name', $department);
            });
        }

        if ($position = request('position')) {
            $query->whereHas('latestEmployeeJob.position', function ($q) use ($position) {
                $q->where('position_name', $position);
            });
        }

        if (request('gender') !== null) {
            $query->whereHas('employeeDetail', function ($q) {
                $q->where('gender', request('gender'));
            });
        }

        if ($employmentStatus = request('employment_status')) {
            $query->whereHas('latestEmployeeJob', function ($q) use ($employmentStatus) {
                $q->where('user_dakar_role', $employmentStatus);
            });
        }

        if ($subGolongan = request('sub_golongan')) {
            $query->whereHas('latestEmployeeJob.subGolongan', function ($q) use ($subGolongan) {
                $q->where('sub_golongan_name', $subGolongan);
            });
        }

        if ($jobStatus = request('job_status')) {
            $query->whereHas('latestEmployeeJob', function ($q) use ($jobStatus) {
                $q->where('job_status', $jobStatus);
            });
        }

        if ($jobType = request('job_type')) {
            $query->whereHas('latestEmployeeJob.jobType', function ($q) use ($jobType) {
                $q->where('job_type_name', $jobType);
            });
        }

        if (request('status') !== null) {
            $query->whereHas('latestEmployeeJob', function ($q) {
                $q->where('employment_status', request('status'));
            });
        }

        return $query->get()->map(function ($employee) {
            $detail = $employee->employeeDetail;
            $job = $employee->latestEmployeeJob;
            $firstJob = $employee->firstEmployeeJob;

            return [
                'npk' => $employee->npk,
                'fullname' => $employee->fullname,
                'email' => $employee->email,
                'gender' => $detail->gender == 1 ? 'P' : ($detail->gender == 0 ? 'L' : 'N/A'),
                'join_date' => $employee->join_date
                    ? Carbon::parse($employee->join_date)->isoFormat('D MMMM Y')
                    : ($firstJob ? Carbon::parse($firstJob->start_date)->isoFormat('D MMMM Y') : 'N/A'),
                'department' => $job->department->department_name ?? 'N/A',
                'position' => $job->position->position_name ?? 'N/A',
                'employment_status' => $job->user_dakar_role ? Str::ucfirst($job->user_dakar_role) : 'N/A',
                'job_status' => $job->contract ?? 'N/A',
                'job_type' => $job->jobType->job_type_name ?? 'N/A',
                'gol'      => $job->golongan->golongan_name ?? 'N/A',
                'sub_gol'  => $job->subGolongan->sub_golongan_name ?? 'N/A',
                'status'   => $job->employment_status ?? 'N/A'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NPK',
            'Full Name',
            'Email',
            'Gender',
            'Join Date',
            'Department',
            'Position',
            'Employment Status',
            'Job Status',
            'Job Type',
            'Golongan',
            'Sub Golongan',
            'Status'
        ];
    }
}