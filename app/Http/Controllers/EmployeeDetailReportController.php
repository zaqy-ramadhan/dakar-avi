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
        $date = request('date') ? Carbon::parse(request('date'))->endOfMonth()->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();

        $query = User::whereHas('employeeJob', function ($q) use ($date) {
            $q->whereDate('start_date', '<=', $date)
                ->where(function ($q2) use ($date) {
                    $q2->whereNull('resign_date')
                        ->orWhereDate('resign_date', '>=', $date);
                });
        });

        // FILTER-FILTER
        if ($department = request('department')) {
            $query->whereHas('employeeJob.department', function ($q) use ($department) {
                $q->where('department_name', $department);
            });
        }

        if ($position = request('position')) {
            $query->whereHas('employeeJob.position', function ($q) use ($position) {
                $q->where('position_name', $position);
            });
        }

        if (request('gender') !== null) {
            $query->whereHas('employeeDetail', function ($q) {
                $q->where('gender', request('gender'));
            });
        }

        if ($employmentStatus = request('employment_status')) {
            $query->whereHas('employeeJob', function ($q) use ($employmentStatus) {
                $q->where('user_dakar_role', $employmentStatus);
            });
        }

        if ($subGolongan = request('sub_golongan')) {
            $query->whereHas('employeeJob.subGolongan', function ($q) use ($subGolongan) {
                $q->where('sub_golongan_name', $subGolongan);
            });
        }

        if ($jobStatus = request('job_status')) {
            $query->whereHas('employeeJob', function ($q) use ($jobStatus) {
                $q->where('job_status', $jobStatus);
            });
        }

        if ($jobType = request('job_type')) {
            $query->whereHas('employeeJob.jobType', function ($q) use ($jobType) {
                $q->where('job_type_name', $jobType);
            });
        }

        if (!is_null(request('status'))) {
            $query->whereHas('employeeJob', function ($q) {
                $date = request('date') ? Carbon::parse(request('date')) : Carbon::now();

                $startOfMonth = $date->copy()->startOfMonth()->startOfDay();
                $endOfMonth = $date->copy()->endOfMonth()->endOfDay();

                $q->where(function ($subQuery) use ($startOfMonth, $endOfMonth) {
                    if (request('status') === 'active') {
                        $subQuery->where(function ($q2) use ($startOfMonth, $endOfMonth) {
                            $q2->whereDate('start_date', '<=', $endOfMonth)
                                ->where(function ($q3) use ($startOfMonth) {
                                    $q3->whereNull('resign_date')
                                        ->whereNull('end_date')
                                        ->orWhere('resign_date', '>=', $startOfMonth)
                                        ->orWhere('end_date', '>=', $startOfMonth);
                                });
                        });
                    } elseif (request('status') === 'inactive') {
                        $subQuery->where(function ($q2) use ($startOfMonth, $endOfMonth) {
                            $q2->whereDate('start_date', '>', $endOfMonth)
                                ->orWhere(function ($q3) use ($startOfMonth) {
                                    $q3->whereNotNull('resign_date')
                                        ->where('resign_date', '<', $startOfMonth);
                                })
                                ->orWhere(function ($q3) use ($startOfMonth) {
                                    $q3->whereNotNull('end_date')
                                        ->where('end_date', '<', $startOfMonth);
                                });
                        });
                    }
                });
            });
        }

        $employees = $query->get();

        // Filter berdasarkan education setelah get
        if ($latestEducation = request('education')) {
            $order = ['S3' => 6,  'S2' => 5, 'S1' => 4, 'D3' => 3, 'SMA' => 2, 'SMP' => 1, 'SD' => 0, '' => 0];
            $employees = $employees->filter(function ($employee) use ($latestEducation, $order) {
                $edu = $employee->latestEducation();
                return $edu && $edu->education_level === $latestEducation;
            })->values();
        }

        // Map hasil untuk dikembalikan
        $employees = $employees
            ->map(function ($employee) use ($date) {
                $detail = $employee->employeeDetail;
                $firstJob = $employee->firstEmployeeJob;
                $job = $employee->currentEmployeeJob($date);
                $latestJob = $employee->latestEmployeeJob;
                $latestEducation = $employee->latestEducation();

                return [
                    'npk' => $employee->npk,
                    'fullname' => $employee->fullname,
                    'gender' => in_array($detail?->gender, [1, '1'], true) ? 'P' : (in_array($detail?->gender, [0, '0'], true) ? 'L' : 'N/A'),
                    'age' => $detail?->age($date) ?? 'N/A',
                    'education' => $latestEducation?->education_level ?? 'N/A',
                    'blood_type' => $detail?->blood_type ?? 'N/A',
                    'join_date' => $employee->join_date
                        ? Carbon::parse($employee->join_date)->isoFormat('D MMMM Y')
                        : Carbon::parse($firstJob->start_date)->isoFormat('D MMMM Y'),
                    'start_date' => $job?->start_date?->isoFormat('D MMMM Y') ?? $latestJob?->start_date?->isoFormat('D MMMM Y') ?? 'N/A',
                    'end_date' => $job?->end_date?->isoFormat('D MMMM Y') ?? $latestJob?->end_date?->isoFormat('D MMMM Y') ?? 'N/A',
                    'duration' => $job?->duration() ?? $latestJob?->duration() ?? 'N/A',
                    'LOS' => $employee->LOS($date),
                    'department' => $job?->department?->department_name ?? $latestJob?->department?->department_name ?? 'N/A',
                    'employment_status' => Str::ucfirst($job?->user_dakar_role ?? $latestJob?->user_dakar_role ?? 'N/A'),
                    'job_status' => $job?->contract ?? $latestJob?->contract ?? 'N/A',
                    'job_type' => $job?->jobType?->job_type_name ?? $latestJob?->jobType?->job_type_name ?? 'N/A',
                    'gol' => $job?->golongan?->golongan_name ?? $latestJob?->golongan?->golongan_name ?? 'N/A',
                    'status' => $job?->is_active($date) ?? $latestJob?->is_active($date) ?? 'inactive',
                ];
            });

        // EXPORT EXCEL
        if (request()->has('export') && request('export') == 'excel') {
            return Excel::download(new EmployeeExport($employees), 'employee-report-'. $date->isoFormat('MMMM Y') .'.xlsx');
        }

        // RETURN DATATABLE
        if (request()->ajax()) {
            return DataTables::of($employees)
                ->addIndexColumn()
                ->addColumn('is_active', function ($employee) {
                    if ($employee['status'] === 'active') {
                        return '<span class="badge text-bg-success">Active</span>';
                    } elseif ($employee['status'] === 'inactive') {
                        return '<span class="badge text-bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge text-bg-light">N/A</span>';
                    }
                })
                ->rawColumns(['is_active'])
                ->make(true);
        }

        // RETURN VIEW
        $departments = Department::all();
        $roles = DakarRole::whereNotIn('role_name', ['admin', 'admin 2', 'admin 3', 'admin 4'])->get();
        $jobStatus = JobStatus::all();
        $jobType = JobType::all();
        $golongan = Golongan::all();
        return view('admin.reporting.employee', compact('departments', 'roles', 'jobStatus', 'jobType', 'golongan'));
    }


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
