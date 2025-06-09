<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeBirthdayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $now = Carbon::now();

        $birthMonth = request()->input('month') ?? $now->month;

        $employeeDetails = EmployeeDetail::with([
            'user.employeeJob.department',
            'user.employeeJob.group',
            'user.employeeJob.jobType',
            'user.employeeJob.position'
        ])
            ->whereMonth('birth_date', (int) $birthMonth)
            ->whereHas('user.employeeJob', function ($query) {
                $query->where('employment_status', true);
                $query->where('user_dakar_role', 'karyawan');
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
                    'birth_date' => Carbon::parse($detail->birth_date)->isoFormat('D MMMM Y'),
                ];
            });
        
            // dd($employeeDetails);
            return view('admin.reporting.birthday', compact('employeeDetails', 'birthMonth'));

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
