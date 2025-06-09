<?php

namespace App\Http\Controllers;

use App\Models\EmployeeJob;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JoinedEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $now = Carbon::now();

        $month = request()->input('month') ?? $now->month;
        $year = request()->input('year') ?? $now->year;

        $employeeJob = EmployeeJob::with(['department', 'user'])
            ->whereHas('user')
            ->where('is_onboarding_completed', true)
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
                    // 'join_date' => $job->user?->join_date
                    //     ? Carbon::parse($job->user->join_date)->isoFormat('D MMMM Y')
                    //     : ($job->user?->firstEmployeeJob?->start_date
                    //         ? Carbon::parse($job->user->firstEmployeeJob->start_date)->isoFormat('D MMMM Y')
                    //         : 'N/A'),
                    'start_date' => $job->start_date
                        ? Carbon::parse($job->start_date)->isoFormat('D MMMM Y')
                        : 'N/A',
                    'end_date' => $job->end_date
                        ? Carbon::parse($job->end_date)->isoFormat('D MMMM Y')
                        : 'N/A',
                    'status' => $job->contract,
                ];
            });

            // dd($employeeJob);

            return view('admin.reporting.joined', compact('employeeJob', 'month', 'year'));

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
