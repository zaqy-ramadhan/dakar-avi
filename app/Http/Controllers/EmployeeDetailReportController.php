<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Models\DakarRole;
use App\Models\Department;
use App\Models\Golongan;
use App\Models\JobStatus;
use App\Models\JobType;
use App\Models\Position;
use App\Models\SubGolongan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;


class EmployeeDetailReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $date_from = request('date_from') ? Carbon::parse(request('date_from')) : Carbon::now();
        $date_to = request('date_to') ? Carbon::parse(request('date_to')) : Carbon::now();
        $startOfMonth = $date_from;
        $endOfMonth = $date_to;
        //$date = request('date') ? Carbon::parse(request('date')) : Carbon::now();
        // $startOfMonth = $date->copy()->startOfMonth()->startOfDay();
        // $endOfMonth = $date->copy()->endOfMonth()->endOfDay();

        $query = User::with([
            'employeeJob.department',
            'employeeJob.position',
            'employeeJob.subGolongan',
            'employeeJob.jobType',
            'employeeJob.golongan',
            'employeeDetail',
            'employeeEducations',
        ])->whereHas('employeeJob', function ($q) use ($endOfMonth) {
            $q->whereDate('start_date', '<=', $endOfMonth);
        })

            ->when(
                request('department'),
                fn($q, $val) =>
                $q->whereHas('employeeJob.department', fn($qq) => $qq->where('department_name', $val))
            )
            ->when(
                request('position'),
                fn($q, $val) =>
                $q->whereHas('employeeJob.position', fn($qq) => $qq->where('position_name', $val))
            )
            ->when(
                !is_null(request('gender')),
                fn($q) =>
                $q->whereHas('employeeDetail', fn($qq) => $qq->where('gender', request('gender')))
            )
            ->when(
                request('employment_status'),
                fn($q, $val) =>
                $q->whereHas('employeeJob', fn($qq) => $qq->where('user_dakar_role', $val))
            )
            ->when(
                request('sub_golongan'),
                fn($q, $val) =>
                $q->whereHas('employeeJob.subGolongan', fn($qq) => $qq->where('sub_golongan_name', $val))
            )
            // ->when(
            //     request('job_status'),
            //     fn($q, $val) =>
            //     $q->whereHas('employeeJob', fn($qq) => $qq->where('job_status', $val))
            // )
            ->when(
                request('job_type'),
                fn($q, $val) =>
                $q->whereHas('employeeJob.jobType', fn($qq) => $qq->where('job_type_name', $val))
            )
            ->when(
                request('latestEducation'),
                fn($q, $val) =>
                $q->whereHas('latestEducation', fn($qq) => $qq->where('education_level', $val))
            )
            ->when(!is_null(request('status')), function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereHas('employeeJob', function ($qq) use ($startOfMonth, $endOfMonth) {
                    if (request('status') === 'active') {
                        $qq->whereDate('start_date', '<=', $endOfMonth)
                            ->where(function ($q2) use ($startOfMonth) {
                                $q2->whereNull('resign_date')
                                    ->whereNull('end_date')
                                    ->orWhereDate('resign_date', '>=', $startOfMonth)
                                    ->orWhereDate('end_date', '>=', $startOfMonth);
                            });
                    } elseif (request('status') === 'inactive') {
                        $qq->whereDate('start_date', '>', $endOfMonth)
                            ->orWhere(function ($q2) use ($startOfMonth) {
                                $q2->whereNotNull('resign_date')->whereDate('resign_date', '<', $startOfMonth);
                            })
                            ->orWhere(function ($q2) use ($startOfMonth) {
                                $q2->whereNotNull('end_date')->whereDate('end_date', '<', $startOfMonth);
                            });
                    }
                });
            });

        $employees = $query->get();

        $employees = $employees->map(function ($employee) use ($startOfMonth, $endOfMonth) {
            $detail = $employee->employeeDetail;
            $firstJob = $employee->firstEmployeeJob;
            // $job = $employee->currentEmployeeJob($date) ?? $employee->latestEmployeeJob;
            $job = $employee->rangeEmployeeJob($startOfMonth, $endOfMonth) 
            ?? $employee->latestEmployeeJob
            ;
            $latestEducation = $employee->latestEducation();

            $joinDateParsed = $employee->join_date
                ? Carbon::parse($employee->join_date)->startOfDay()
                : Carbon::parse($firstJob->start_date)->startOfDay();
            
            return [
                'npk' => $employee->npk,
                'fullname' => $employee->fullname,
                'gender' => in_array($detail?->gender, [1, '1'], true) ? 'P' : (in_array($detail?->gender, [0, '0'], true) ? 'L' : 'N/A'),
                'age' => $detail?->age($endOfMonth) ?? 'N/A',
                'email' => $employee->email ?? 'N/A',
                'education' => $latestEducation?->education_level ?? 'N/A',
                'blood_type' => $detail?->blood_type ?? 'N/A',
                'join_date' => $joinDateParsed->getTimestamp(),
                'join_date_display' => $joinDateParsed->format('d/m/Y'),
                'start_date' => $job?->start_date ? $job->start_date->startOfDay()->getTimestamp() : 0,
                'start_date_display' => $job?->start_date?->format('d/m/Y') ?? 'N/A',
                'end_date' => $job?->end_date ? $job->end_date->startOfDay()->getTimestamp() : 0,
                'end_date_display' => $job?->end_date?->format('d/m/Y') ?? 'N/A',
                'duration' => $job?->duration() ?? 'N/A',
                'LOS' => $employee->LOS($endOfMonth),
                'department' => $job?->department?->department_name ?? 'N/A',
                'employment_status' => Str::ucfirst($job?->user_dakar_role ?? 'N/A'),
                'job_status' => $job?->contract ?? 'N/A',
                'job_type' => $job?->jobType?->job_type_name ?? 'N/A',
                'gol' => $job?->golongan?->golongan_name ?? 'N/A',
                'status' => $job?->is_active_range($startOfMonth, $endOfMonth) ?? 'inactive',
            ];
        })
    //    ->filter(function ($item) {
    //         if (request()->filled('status')) {
    //             return $item['status'] === request('status');
    //         }
    //         return true;
    //     })
    //     ->filter(function ($item) {
    //         if (request()->filled('employment_status')) {
    //             return strtolower($item['employment_status']) === strtolower(request('employment_status'));
    //         }
    //         return true;
    //     })
    //     ->values();
        ->filter(function ($item) {
            // 1. Filter Status (Active/Inactive)
            if (request()->filled('status')) {
                if ($item['status'] !== request('status')) return false;
            }

            // 2. Filter Department
            if (request()->filled('department')) {
                if ($item['department'] !== request('department')) return false;
            }

            // 3. Filter Employment Status (misal: Pemagangan)
            if (request()->filled('employment_status')) {
                if (strtolower($item['employment_status']) !== strtolower(request('employment_status'))) return false;
            }

            // 4. Filter Gender
            if (!is_null(request('gender'))) {
                $genderReq = request('gender') == 1 ? 'P' : 'L';
                if ($item['gender'] !== $genderReq) return false;
            }

            // 5. Filter Job Type
            if (request()->filled('job_type')) {
                if ($item['job_type'] !== request('job_type')) return false;
            }

            // 6. Filter Pendidikan Terakhir
            if (request()->filled('latestEducation')) {
                if ($item['education'] !== request('latestEducation')) return false;
            }

            return true;
        })
        ->values()
        ;

        if (request()->has('export') && request('export') == 'excel') {
            return Excel::download(new EmployeeExport($employees), 'employee-report-' . $startOfMonth->isoFormat('MMMM Y') . '-' . $endOfMonth->isoFormat('MMMM Y') . '.xlsx');
        }

        if (request()->ajax()) {
            return DataTables::of($employees)
                ->addIndexColumn()
                ->addColumn('is_active', function ($employee) {
                    return match ($employee['status']) {
                        'active' => '<span class="badge text-bg-success">Active</span>',
                        'inactive' => '<span class="badge text-bg-danger">Inactive</span>',
                        default => '<span class="badge text-bg-light">N/A</span>',
                    };
                })
                ->rawColumns(['is_active'])
                ->make(true);
        }

        $departments = Department::all();
        $roles = DakarRole::whereNotIn('role_name', ['admin', 'admin 2', 'admin 3', 'admin 4'])->get();
        $jobStatus = JobStatus::all();
        $jobType = JobType::all();
        $golongan = Golongan::all();

        return view('admin.reporting.employee', compact('departments', 'roles', 'jobStatus', 'jobType', 'golongan'));
    }


    // public function index()
    // {
    //     $date = request('date') ? Carbon::parse(request('date')) : Carbon::now();
    //     $startOfMonth = $date->copy()->startOfMonth()->startOfDay();
    //     $endOfMonth = $date->copy()->endOfMonth()->endOfDay();

    //     $query = User::whereHas('employeeJob', function ($q) use ($endOfMonth) {
    //         $q->whereDate('start_date', '<=', $endOfMonth)
    //             ->where(function ($q2) use ($endOfMonth) {
    //                 $q2->whereNull('resign_date')
    //                     ->orWhereDate('resign_date', '>=', $endOfMonth);
    //             });
    //     })
    //         ->when(request('department'), function ($q, $department) {
    //             $q->whereHas('employeeJob.department', fn($qq) => $qq->where('department_name', $department));
    //         })
    //         ->when(request('position'), function ($q, $position) {
    //             $q->whereHas('employeeJob.position', fn($qq) => $qq->where('position_name', $position));
    //         })
    //         ->when(!is_null(request('gender')), function ($q) {
    //             $q->whereHas('employeeDetail', fn($qq) => $qq->where('gender', request('gender')));
    //         })
    //         ->when(request('employment_status'), function ($q, $status) {
    //             $q->whereHas('employeeJob', fn($qq) => $qq->where('user_dakar_role', $status));
    //         })
    //         ->when(request('sub_golongan'), function ($q, $sg) {
    //             $q->whereHas('employeeJob.subGolongan', fn($qq) => $qq->where('sub_golongan_name', $sg));
    //         })
    //         ->when(request('job_status'), function ($q, $js) {
    //             $q->whereHas('employeeJob', fn($qq) => $qq->where('job_status', $js));
    //         })
    //         ->when(request('job_type'), function ($q, $jt) {
    //             $q->whereHas('employeeJob.jobType', fn($qq) => $qq->where('job_type_name', $jt));
    //         })
    //         ->when(!is_null(request('status')), function ($q) use ($startOfMonth, $endOfMonth) {
    //             $q->whereHas('employeeJob', function ($qq) use ($startOfMonth, $endOfMonth) {
    //                 if (request('status') === 'active') {
    //                     $qq->whereDate('start_date', '<=', $endOfMonth)
    //                         ->where(function ($q2) use ($startOfMonth) {
    //                             $q2->whereNull('resign_date')
    //                                 ->whereNull('end_date')
    //                                 ->orWhere('resign_date', '>=', $startOfMonth)
    //                                 ->orWhere('end_date', '>=', $startOfMonth);
    //                         });
    //                 } elseif (request('status') === 'inactive') {
    //                     $qq->whereDate('start_date', '>', $endOfMonth)
    //                         ->orWhere(function ($q2) use ($startOfMonth) {
    //                             $q2->whereNotNull('resign_date')->where('resign_date', '<', $startOfMonth);
    //                         })
    //                         ->orWhere(function ($q2) use ($startOfMonth) {
    //                             $q2->whereNotNull('end_date')->where('end_date', '<', $startOfMonth);
    //                         });
    //                 }
    //             });
    //         });

    //     $employees = $query->get();

    //     if ($latestEducation = request('education')) {
    //         $employees = $employees->filter(
    //             fn($emp) =>
    //             $emp->latestEducation()?->education_level === $latestEducation
    //         )->values();
    //     }

    //     $employees = $employees->map(function ($employee) use ($date) {
    //         $detail = $employee->employeeDetail;
    //         $firstJob = $employee->firstEmployeeJob;
    //         $job = $employee->currentEmployeeJob($date) ?? $employee->latestEmployeeJob;
    //         $latestEducation = $employee->latestEducation();

    //         return [
    //             'npk' => $employee->npk,
    //             'fullname' => $employee->fullname,
    //             'gender' => in_array($detail?->gender, [1, '1'], true) ? 'P' : (in_array($detail?->gender, [0, '0'], true) ? 'L' : 'N/A'),
    //             'age' => $detail?->age($date) ?? 'N/A',
    //             'education' => $latestEducation?->education_level ?? 'N/A',
    //             'blood_type' => $detail?->blood_type ?? 'N/A',
    //             'join_date' => $employee->join_date
    //                 ? Carbon::parse($employee->join_date)->isoFormat('D MMMM Y')
    //                 : Carbon::parse($firstJob->start_date)->isoFormat('D MMMM Y'),
    //             'start_date' => $job?->start_date?->isoFormat('D MMMM Y') ?? 'N/A',
    //             'end_date' => $job?->end_date?->isoFormat('D MMMM Y') ?? 'N/A',
    //             'duration' => $job?->duration() ?? 'N/A',
    //             'LOS' => $employee->LOS($date),
    //             'department' => $job?->department?->department_name ?? 'N/A',
    //             'employment_status' => Str::ucfirst($job?->user_dakar_role ?? 'N/A'),
    //             'job_status' => $job?->contract ?? 'N/A',
    //             'job_type' => $job?->jobType?->job_type_name ?? 'N/A',
    //             'gol' => $job?->golongan?->golongan_name ?? 'N/A',
    //             'status' => $job?->is_active($date) ?? 'inactive',
    //         ];
    //     });

    //     if (request()->has('export') && request('export') == 'excel') {
    //         return Excel::download(new EmployeeExport($employees), 'employee-report-' . $date->isoFormat('MMMM Y') . '.xlsx');
    //     }

    //     if (request()->ajax()) {
    //         return DataTables::of($employees)
    //             ->addIndexColumn()
    //             ->addColumn('is_active', function ($employee) {
    //                 return match ($employee['status']) {
    //                     'active' => '<span class="badge text-bg-success">Active</span>',
    //                     'inactive' => '<span class="badge text-bg-danger">Inactive</span>',
    //                     default => '<span class="badge text-bg-light">N/A</span>',
    //                 };
    //             })
    //             ->rawColumns(['is_active'])
    //             ->make(true);
    //     }

    //     $departments = Department::all();
    //     $roles = DakarRole::whereNotIn('role_name', ['admin', 'admin 2', 'admin 3', 'admin 4'])->get();
    //     $jobStatus = JobStatus::all();
    //     $jobType = JobType::all();
    //     $golongan = Golongan::all();

    //     return view('admin.reporting.employee', compact('departments', 'roles', 'jobStatus', 'jobType', 'golongan'));
    // }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
