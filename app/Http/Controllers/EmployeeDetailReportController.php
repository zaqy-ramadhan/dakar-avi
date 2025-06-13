<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Models\DakarRole;
use App\Models\Department;
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
        if (request()->has('export') && request('export') == 'excel') {
            return Excel::download(new EmployeeExport(request()->all()),'employee-report.xlsx'
            );
        }

        if (request()->ajax()) {
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

            $employee = $query->get()->map(function ($employee) {
                $detail = $employee->employeeDetail;
                $job = $employee->latestEmployeeJob;
                $firstJob = $employee->firstEmployeeJob;

                return [
                    'npk' => $employee->npk,
                    'fullname' => $employee->fullname,
                    'email' => $employee->email ?? 'N/A',
                    'gender' => $detail->gender == 1 ? 'P' : ($detail->gender == 0 ? 'L' : 'N/A'),
                    'join_date' => Carbon::parse($employee->join_date)->isoFormat('D MMMM Y') ??
                        Carbon::parse($firstJob->start_date)->isoFormat('D MMMM Y'),
                    'department' => $job->department->department_name,
                    'position' => $job->position->position_name ?? 'N/A',
                    'employment_status' => Str::ucfirst($job->user_dakar_role),
                    'job_status' => $job ? $job->contract : "N/A",
                    'job_type' => $job->jobType->job_type_name ?? 'N/A',
                    'gol'      => $job->golongan->golongan_name ?? 'N/A',
                    'sub_gol'  => $job->subGolongan->sub_golongan_name ?? 'N/A',
                    'status'   => $job->employment_status
                ];
            });

            return Datatables::of($employee)
                ->addIndexColumn()
                ->addColumn('is_active', function ($employee) {

                    if ($employee['status'] == true) {
                        return '<span class="badge text-bg-success">Active</span>';
                    } elseif ($employee['status'] == false) {
                        return '<span class="badge text-bg-danger">Termination</span>';
                    } else {
                        return '<span class="badge text-bg-light">N/A</span>';
                    }
                })
                ->rawColumns(['is_active'])
                ->make(true);
        } else {
            $departments = Department::all();
            $positions = Position::all();
            $roles = DakarRole::whereNotIn('role_name', ['admin', 'admin 2', 'admin 3', 'admin 4'])->get();
            $jobStatus = JobStatus::all();
            $jobType = JobType::all();
            $subGolongan = SubGolongan::all();
            return view('admin.reporting.employee', compact('departments', 'positions', 'roles', 'jobStatus', 'jobType', 'subGolongan'));
        }
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
