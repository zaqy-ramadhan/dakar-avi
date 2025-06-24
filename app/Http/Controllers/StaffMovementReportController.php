<?php

namespace App\Http\Controllers;

use App\Models\EmployeeJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;

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
        'Extension Contract',
        'Employee Transfer',
        'Employee Department Mutation',
        'Employee Internship Extension',
        'Employee Pemagangan Extension',
        'One Year Service',
        'Termination',
    ];

    public function index(Request $request)
    {
        $note = $request->input('note') ?? 'New Employee Kontrak'; // default jika tidak ada
        return view('admin.reporting.staff_movement', compact('note'));
    }

    public function getData(Request $request)
    {
        $note = $request->input('note');
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        if ($request->has('export') && $request->input('export') == 'excel') {
            $employees = match ($note) {
                'New Employee Tetap' => $this->newEmployeeTetap($request)->getData(true),
                'New Employee Kontrak' => $this->newEmployeeKontrak($request)->getData(true),
                'New Employee Pemagangan' => $this->newEmployeePemagangan($request)->getData(true),
                'New Employee Internship' => $this->newEmployeeIntern($request)->getData(true),
                'Extension Contract' => $this->EmployeeContractExtension($request)->getData(true),
                'Employee Transfer' => $this->EmployeeContractPositionChange($request)->getData(true),
                'Employee Department Mutation' => $this->EmployeeDepartmentMutation($request)->getData(true),
                'One Year Service' => $this->EmployeeContractLongTerm($request)->getData(true),
                'Termination' => $this->expiredContract($request)->getData(true),
                default => collect(),
            };
            $export = match ($note) {
                'New Employee Kontrak', 'New Employee Pemagangan', 'New Employee Tetap' =>
                collect($employees['data'])->map(fn($item) => [
                    'NPK' => $item['npk'],
                    'Fullname' => $item['fullname'],
                    'Department' => $item['department'],
                    'Section' => $item['section'],
                    'Position' => $item['position'],
                    'Start Date' => $item['start_date'],
                ]),

                'New Employee Internship' =>
                collect($employees['data'])->map(fn($item) => [
                    'NPK' => $item['npk'],
                    'Fullname' => $item['fullname'],
                    'Department' => $item['department'],
                    'Start Date' => $item['start_date'],
                    'Duration' => $item['duration'],
                ]),

                'Extension Contract' =>
                collect($employees['data'])->map(fn($item) => [
                    'NPK' => $item['npk'],
                    'Fullname' => $item['fullname'],
                    'Department' => $item['department'],
                    'Position' => $item['position'],
                    'Start Date' => $item['start_date'],
                    'End Date' => $item['end_date'],
                    'Duration' => $item['duration'],
                    'Length of Service' => $item['LOS'],
                    'Contract' => $item['contract'],
                ]),

                'Employee Transfer' =>
                collect($employees['data'])->map(fn($item) => [
                    'NPK' => $item['npk'],
                    'Fullname' => $item['fullname'],
                    'Last Department' => $item['last_department'],
                    'New Department' => $item['department'],
                    // 'Section' => $item['section'],
                    'Last Position' => $item['last_position'],
                    'New Position' => $item['position'],
                    'Start Date' => $item['start_date'],
                ]),

                'Employee Department Mutation' =>
                collect($employees['data'])->map(fn($item) => [
                    'NPK' => $item['npk'],
                    'Fullname' => $item['fullname'],
                    'Old Department' => $item['old_department'],
                    'New Department' => $item['department'],
                    'Section' => $item['section'],
                    'Position' => $item['position'],
                    'Start Date' => $item['start_date'],
                ]),

                'One Year Service' =>
                collect($employees['data'])->map(fn($item) => [
                    'NPK' => $item['npk'],
                    'Fullname' => $item['fullname'],
                    'Department' => $item['department'],
                    'Section' => $item['section'],
                    'Position' => $item['position'],
                    'Start Date' => $item['start_date'],
                ]),

                'Termination' =>
                collect($employees['data'])->map(fn($item) => [
                    'NPK' => $item['npk'],
                    'Fullname' => $item['fullname'],
                    'Department' => $item['department'],
                    'Position' => $item['position'],
                    'Start Date' => $item['start_date'],
                    'End Date' => $item['end_date'],
                    'Out Date' => $item['out_date'],
                    'Reason' => $item['reason'],
                    'Status' => $item['status'],
                ]),


                default => collect($employees['data']),
            };

            $filename = str_replace(' ', '-', strtolower($note)) . '-report-' . $date->isoFormat('MMMM-Y') . '.xlsx';
            return Excel::download(new \App\Exports\EmployeeExport($export), $filename);
        }

        return match ($note) {
            'New Employee Tetap' => $this->newEmployeeTetap($request),
            'New Employee Kontrak' => $this->newEmployeeKontrak($request),
            'New Employee Pemagangan' => $this->newEmployeePemagangan($request),
            'New Employee Internship' => $this->newEmployeeIntern($request),
            'Extension Contract' => $this->EmployeeContractExtension($request),
            'Employee Transfer' => $this->EmployeeContractPositionChange($request),
            'Employee Department Mutation' => $this->EmployeeDepartmentMutation($request),
            'One Year Service' => $this->EmployeeContractLongTerm($request),
            'Termination' => $this->expiredContract($request),
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
            ->where('notes', 'Extension Contract')
            ->whereBetween('start_date', [$date, $endDate]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
            ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            // ->addColumn('section', fn($job) => $job->section->section_name ?? 'N/A')
            ->addColumn('position', fn($job) => $job->position->position_name ?? 'N/A')
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            ->addColumn('start_date', fn($job) => Carbon::parse($job->start_date)->isoFormat('D MMM Y'))
            ->addColumn('end_date', fn($job) => Carbon::parse($job->end_date)->isoFormat('D MMM Y'))
            ->addColumn('LOS', fn($job) => $job->user->LOS() ?? 'N/A')
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
            ->where('notes', 'Employee Transfer')
            ->whereBetween('start_date', [$date, $endDate]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
            ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            // ->addColumn('section', fn($job) => $job->section->section_name ?? 'N/A')
            ->addColumn('position', fn($job) => $job->position->position_name ?? 'N/A')
            ->addColumn('last_position', function ($job) {
                $oldJob = EmployeeJob::with('position')
                    ->where('user_id', $job->user_id)
                    ->where('start_date', '<', $job->start_date)
                    ->orderByDesc('start_date')
                    ->first();

                return $oldJob?->position?->position_name ?? '-';
            })
            ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
            ->addColumn('last_department', function ($job) {
                $oldJob = EmployeeJob::with('department')
                    ->where('user_id', $job->user_id)
                    ->where('start_date', '<', $job->start_date)
                    ->orderByDesc('start_date')
                    ->first();
                return $oldJob?->department?->department_name ?? '-';
            })
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
            ->filter(function ($user) use ($oneYearAgo, $date) {
                $firstJob = $user->firstEmployeeJob;
                $currentJob = $user->currentEmployeeJob($date);
                if (!$firstJob) return false;
                $startDate = Carbon::parse($firstJob->start_date);
                if (!$currentJob) return false;
                if ($user->currentEmployeeJob($date)->is_active($date) === 'inactive') return false;

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
            ->addColumn('start_date', fn($user) =>
            optional($user->employeeJob->first())
                ? Carbon::parse($user->employeeJob->first()->start_date)->isoFormat('D MMM Y')
                : 'N/A')

            ->addColumn('age_in_months', function ($user) use ($date) {
                $firstJob = $user->employeeJob->first();
                return $firstJob
                    ? Carbon::parse($firstJob->start_date)->diffInMonths($date) . ' bulan'
                    : 'N/A';
            })->addColumn('end_date', function ($user) use ($date) {
                $job = $user->currentEmployeeJob($date);
                return $job && $job->end_date
                    ? Carbon::parse($job->end_date)->isoFormat('D MMM Y')
                    : 'N/A';
            })
            ->make(true);
    }

    public function expiredContract(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $expiredContracts = EmployeeJob::with(['user', 'department', 'position'])
            ->whereBetween('end_date', [$date, $endDate])
            ->get();

        $transformedContracts = $expiredContracts->transform(function($job) {
            $user = $job->user;
            return [
                'npk' => $user ? $user->npk : 'N/A',
                'fullname' => $user ? $user->fullname : 'N/A',
                'department' => $job->department ? $job->department->department_name : 'N/A',
                'position'=> $job->position ? $job->position->position_name : 'N/A',
                'start_date' => $job->start_date ? Carbon::parse((string)$job->start_date)->isoFormat('D MMMM Y') : 'N/A',
                'end_date' => $job->end_date ? Carbon::parse((string)$job->end_date)->isoFormat('D MMMM Y') : 'N/A',
                'out_date' => $user && $user->offboarding?->resign_date ? Carbon::parse((string)$user->offboarding->resign_date)->isoFormat('D MMMM Y') : 'N/A',
                'reason' => $user && $user->offboarding?->reason ? $user->offboarding->reason : 'N/A',
                'status' => $job->contract,
            ];
        });

        return DataTables::of($transformedContracts)
            ->addIndexColumn()
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
