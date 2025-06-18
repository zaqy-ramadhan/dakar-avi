<?php

namespace App\Http\Controllers;

use App\Models\EmployeeJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StaffMovementReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $categories = [
        'New Employee Contract',
        'New Employee Tetap',
        'New Employee Pemagangan',
        'New Employee Internship',
        'Employee Contract Extension',
        'Employee Contract Position Change',
        'Employee Department Mutation',
        'Employee Internship Extension',
        'Employee Pemagangan Extension',
    ];

    public function index()
    {
        return view('admin.reporting.staff_movement', [
            'categories' => $this->categories,
        ]);
    }

    public function data(Request $request)
    {
        $note = $request->input('note');
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = EmployeeJob::with(['user', 'position', 'department'])
            ->where('notes', $note)
            ->whereBetween('start_date', [$date, $endDate]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
            ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
            ->addColumn('position', fn($job) => $job->position->position_name ?? 'N/A')
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            ->addColumn('start_date', fn($job) => Carbon::parse($job->start_date)->isoFormat('D MMM Y'))
            ->make(true);
    }

    protected function getByNote(string $note, Carbon $date)
    {
        $startOfMonth = $date->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $date->copy()->endOfMonth()->endOfDay();

        return EmployeeJob::with(['user', 'position', 'department'])
            ->where('notes', $note)
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    // Mulai kerja di bulan ini
                    $q->whereBetween('start_date', [$startOfMonth, $endOfMonth]);
                })->orWhere(function ($q) use ($startOfMonth) {
                    // Masih aktif di bulan ini
                    $q->where(function ($q2) use ($startOfMonth) {
                        $q2->whereNull('resign_date')
                            ->orWhere('resign_date', '>=', $startOfMonth);
                    })->where(function ($q3) use ($startOfMonth) {
                        $q3->whereNull('end_date')
                            ->orWhere('end_date', '>=', $startOfMonth);
                    });
                });
            })
            ->get();
    }


    protected function getLongTermContracts(Carbon $date)
    {
        return EmployeeJob::with(['user', 'position', 'department'])
            ->where('job_status', 'Contract')
            ->whereDate('start_date', '<=', $date->copy()->subYear()->toDateString())
            ->where('employment_status', true)
            ->get();
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
