<?php

namespace App\Http\Controllers;

use App\Models\EmployeeJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StaffMovementReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $categories = [
        'New Employee Kontrak',
        'New Employee Tetap',
        'New Employee Pemagangan',
        'New Employee Internship',
        'Employee Contract Extension',
        'Employee Contract Position Change',
        'Employee Department Mutation',
        'Employee Internship Extension',
        'Employee Pemagangan Extension',
        'Employee 1 Year',
    ];

    public function index(Request $request)
    {
        $note = $request->input('note') ?? 'New Employee Kontrak'; // default jika tidak ada
        return view('admin.reporting.staff_movement', compact('note'));
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

    public function getData(Request $request)
    {
        $note = $request->input('note');
        return match ($note) {
            'New Employee Tetap' => $this->newEmployeeTetap($request),
            'New Employee Kontrak' => $this->newEmployeeKontrak($request),
            'New Employee Pemagangan' => $this->newEmployeePemagangan($request),
            'New Employee Internship' => $this->newEmployeeIntern($request),
            'Employee Contract Extension' => $this->EmployeeContractExtension($request),
            'Employee Contract Position Change' => $this->EmployeeContractPositionChange($request),
            'Employee Department Mutation' => $this->EmployeeDepartmentMutation($request),
            'Employee 1 Year' => $this->EmployeeContractLongTerm($request),
            default => response()->json([]),
        };
    }

    public function newEmployeeTetap(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = EmployeeJob::with(['user', 'position', 'department', 'section'])
            ->where('notes', 'New Employee Tetap')
            ->whereBetween('start_date', [$date, $endDate]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
            ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            ->addColumn('section', fn($job) => $job->section->section_name ?? 'N/A')
            ->addColumn('position', fn($job) => $job->position->position_name ?? 'N/A')
            ->addColumn('start_date', fn($job) => Carbon::parse($job->start_date)->isoFormat('D MMM Y'))
            ->make(true);
    }


    public function newEmployeeKontrak(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = EmployeeJob::with(['user', 'position', 'department', 'section'])
            ->where('notes', 'New Employee Kontrak')
            ->whereBetween('start_date', [$date, $endDate]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
            ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            ->addColumn('section', fn($job) => $job->section->section_name ?? 'N/A')
            ->addColumn('position', fn($job) => $job->position->position_name ?? 'N/A')
            ->addColumn('start_date', fn($job) => Carbon::parse($job->start_date)->isoFormat('D MMM Y'))
            ->make(true);
    }

    public function newEmployeePemagangan(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = EmployeeJob::with(['user', 'position', 'department', 'section'])
            ->where('notes', 'New Employee Pemagangan')
            ->whereBetween('start_date', [$date, $endDate]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
            ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            ->addColumn('section', fn($job) => $job->section->section_name ?? 'N/A')
            ->addColumn('position', fn($job) => $job->position->position_name ?? 'N/A')
            ->addColumn('start_date', fn($job) => Carbon::parse($job->start_date)->isoFormat('D MMM Y'))
            ->make(true);
    }

    public function newEmployeeIntern(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = EmployeeJob::with(['user', 'position', 'department', 'section'])
            ->where('notes', 'New Employee Internship')
            ->whereBetween('start_date', [$date, $endDate]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
            ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            ->addColumn('start_date', fn($job) => Carbon::parse($job->start_date)->isoFormat('D MMM Y'))
            ->addColumn('duration', fn($job) => $job->duration() ?? 'N/A')
            ->make(true);
    }

    public function EmployeeContractExtension(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = EmployeeJob::with(['user', 'position', 'department'])
            ->where('notes', 'Employee Contract Extension')
            ->whereBetween('start_date', [$date, $endDate]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
            ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            ->addColumn('section', fn($job) => $job->section->section_name ?? 'N/A')
            ->addColumn('position', fn($job) => $job->position->position_name ?? 'N/A')
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            ->addColumn('start_date', fn($job) => Carbon::parse($job->start_date)->isoFormat('D MMM Y'))
            ->addColumn('end_date', fn($job) => Carbon::parse($job->end_date)->isoFormat('D MMM Y'))
            ->addColumn('duration', fn($job) => $job->duration() ?? 'N/A')
            ->addColumn('contract', fn($job) => $job->contract ?? 'N/A')
            ->make(true);
    }

    public function EmployeeContractPositionChange(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = EmployeeJob::with(['user', 'position', 'department'])
            ->where('notes', 'Employee Contract Position Change')
            ->whereBetween('start_date', [$date, $endDate]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
            ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            ->addColumn('section', fn($job) => $job->section->section_name ?? 'N/A')
            ->addColumn('position', fn($job) => $job->position->position_name ?? 'N/A')
            ->addColumn('old_position', function ($job) {
                $oldJob = EmployeeJob::with('position')
                    ->where('user_id', $job->user_id)
                    ->where('start_date', '<', $job->start_date)
                    ->orderByDesc('start_date')
                    ->first();

                return $oldJob?->position?->position_name ?? '-';
            })
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            ->addColumn('start_date', fn($job) => Carbon::parse($job->start_date)->isoFormat('D MMM Y'))
            ->make(true);
    }

    public function EmployeeDepartmentMutation(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = EmployeeJob::with(['user', 'position', 'department'])
            ->where('notes', 'Employee Department Mutation')
            ->whereBetween('start_date', [$date, $endDate]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
            ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            ->addColumn('section', fn($job) => $job->section->section_name ?? 'N/A')
            ->addColumn('position', fn($job) => $job->position->position_name ?? 'N/A')
            ->addColumn('old_department', function ($job) {
                $oldJob = EmployeeJob::with('department')
                    ->where('user_id', $job->user_id)
                    ->where('start_date', '<', $job->start_date)
                    ->orderByDesc('start_date')
                    ->first();

                return $oldJob?->department?->department_name ?? '-';
            })
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


    public function EmployeeContractLongTerm(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $oneYearAgo = $date->copy()->subYear();

        $users = User::with([
            'employeeJob' => function ($q) {
                $q->orderBy('start_date', 'asc');
            },
        ])
            ->whereHas('employeeJob', function ($q) {
                $q->where('user_dakar_role', 'karyawan')
                    ->where('employment_status', true);
            })
            ->get()
            ->filter(function ($user) use ($oneYearAgo) {
                $firstJob = $user->employeeJob->first();
                if (!$firstJob) return false;

                $startDate = Carbon::parse($firstJob->start_date);

                return $startDate->year === $oneYearAgo->year &&
                    $startDate->month === $oneYearAgo->month;
            });

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('fullname', fn($user) => $user->fullname ?? 'N/A')
            ->addColumn('npk', fn($user) => $user->npk ?? 'N/A')
            ->addColumn('department', fn($user) => $user->currentEmployeeJob($date)?->department->department_name ?? 'N/A')
            ->addColumn('section', fn($user) => $user->currentEmployeeJob($date)?->section->section_name ?? 'N/A')
            ->addColumn('position', fn($user) => $user->currentEmployeeJob($date)?->position->position_name ?? 'N/A')
            ->addColumn('start_date', fn($user) => Carbon::parse($user->employeeJob->first()->start_date)->isoFormat('D MMM Y'))
            ->addColumn('end_date', fn($user) => Carbon::parse($user->$user->currentEmployeeJob($date)?->end_date)->isoFormat('D MMM Y'))
            ->addColumn('age_in_months', function ($user) use ($date) {
                $start = Carbon::parse($user->employeeJob->first()->start_date);
                return $start->diffInMonths($date) . ' bulan';
            })
            ->make(true);
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
