<?php

namespace App\Http\Controllers;

use App\Models\EmployeeJob;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpiredContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $now = Carbon::now();

        $month = request()->input('month') ?? $now->month;
        $year = request()->input('year') ?? $now->year;

        $expiredContracts = EmployeeJob::with(['department', 'user'])
            ->where('job_status', 'kontrak')
            ->where('user_dakar_role', 'karyawan')
            ->where('employment_status', true)
            ->whereMonth('end_date', $month)
            ->whereYear('end_date', $year)
            ->paginate(15);

        $expiredContracts->getCollection()->transform(function ($job) {
            return [
                'npk' => $job->user ? $job->user->npk : 'N/A',
                'name' => $job->user ? $job->user->fullname : 'N/A',
                'department' => $job->department ? $job->department->department_name : 'N/A',
                'join_date' => $job->user && $job->user->join_date
                    ? Carbon::parse($job->user->join_date)->isoFormat('D MMMM Y')
                    : ($job->user && $job->user->firstEmployeeJob && $job->user->firstEmployeeJob->start_date
                        ? Carbon::parse($job->user->firstEmployeeJob->start_date)->isoFormat('D MMMM Y')
                        : 'N/A'),
                'start_date' => $job->start_date ? Carbon::parse($job->start_date)->isoFormat('D MMMM Y') : 'N/A',
                'end_date' => $job->end_date ? Carbon::parse($job->end_date)->isoFormat('D MMMM Y') : 'N/A',
                'status' => $job->contract,
            ];
        });

        return view('admin.reporting.expired', compact('expiredContracts', 'month', 'year'));
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
