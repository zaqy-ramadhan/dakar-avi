<?php

namespace App\Http\Controllers;

use App\Models\EmployeeInventoryNumber;
use App\Models\EmployeeJob;
use App\Models\Item;
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
        'Employee Promotion',
        'Employee Department Mutation',
        'Employee Internship Extension',
        'Employee Pemagangan Extension',
        'One Year Service',
        'Termination',
        'Expired Contract',
        'Onboarding Report',
        'Offboarding Report'
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
                'Employee Promotion' => $this->EmployeePromotion($request)->getData(true),
                'Employee Department Mutation' => $this->EmployeeDepartmentMutation($request)->getData(true),
                'One Year Service' => $this->EmployeeContractLongTerm($request)->getData(true),
                'Termination' => $this->termination($request)->getData(true),
                'Expired Contract' => $this->expiredContract($request)->getData(true),
                'Onboarding Report' => $this->onboardingReport($request)->getData(true),
                'Offboarding Report' => $this->offboardingReport($request)->getData(true),
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

                'Employee Promotion' =>
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

                'Expired Contract' =>
                collect($employees['data'])->map(fn($item) => [
                    'NPK' => $item['npk'],
                    'Fullname' => $item['fullname'],
                    'Department' => $item['department'],
                    'Position' => $item['position'],
                    'Start Date' => $item['start_date'],
                    'End Date' => $item['end_date'],
                    'Status' => $item['status'],
                ]),

                'Onboarding Report' => collect($employees['data'] ?? [])->map(fn($item) => [
                    'NPK' => $item['npk'] ?? '-',
                    'Fullname' => $item['fullname'] ?? '-',
                    'Department' => $item['department'] ?? '-',
                    'Position' => $item['position'] ?? '-',
                    'Start Date' => $item['start_date'] ?? '-',
                    'Status' => $item['status'] ?? '-',
                    'Deadline Pre' => $item['deadline_pre'] ?? '-',
                    'Create Employee Overdue' => $item['create_employee_overdue_days'] ?? 0,
                    'Create Employee Date' => $item['create_employee_completion_date'] ?? '-',
                    'Employment Data Overdue' => $item['employment_data_overdue_days'] ?? '-',
                    'Employment Data Date' => $item['employment_data_completion_date'] ?? '-',
                    'Starter Kit Overdue' => $item['starter_kit_overdue_days'] ?? 0,
                    'Starter Kit Date' => $item['starter_kit_completion_date'] ?? '-',
                    'Deadline On' => $item['deadline_on'] ?? '-',
                    'Starter Kit Accepted Overdue' => $item['starter_kit_acc_overdue_days'] ?? 0,
                    'Starter Kit Accepted Date' => $item['starter_kit_acc_completion_date'] ?? '-',
                    'Contract Signature Overdue' => $item['contract_signature_overdue_days'] ?? 0,
                    'Contract Signature Date' => $item['contract_signature_completion_date'] ?? '-',
                    'Deadline Post' => $item['deadline_post'] ?? '-',
                    'Eslip Overdue' => $item['eslip_overdue_days'] ?? 0,
                    'Eslip Date' => $item['eslip_completion_date'] ?? '-',
                    'Greatday Overdue' => $item['greatday_overdue_days'] ?? 0,
                    'Greatday Date' => $item['greatday_completion_date'] ?? '-',
                    'BPJS Kes Overdue' => $item['bpjskes_overdue_days'] ?? 0,
                    'BPJS Kes Date' => $item['bpjskes_completion_date'] ?? '-',
                    'BPJS TK Overdue' => $item['bpjstk_overdue_days'] ?? 0,
                    'BPJS TK Date' => $item['bpjstk_completion_date'] ?? '-',
                ]),

                'Offboarding Report' => collect($employees['data'] ?? [])->map(fn($item) => [
                    'NPK' => $item['npk'] ?? '-',
                    'Fullname' => $item['fullname'] ?? '-',
                    'Department' => $item['department'] ?? '-',
                    'Position' => $item['position'] ?? '-',
                    'Status' => $item['status'] ?? '-',
                    'Resign Date' => $item['resign_date'] ?? '-',
                    'Reason' => $item['reason'] ?? '-',
                    'SKSMK First Party Signature' => $item['signature_1_date'] ?? '-',
                    'SKSMK Second Party Signature' => $item['signature_2_date'] ?? '-',
                    'Starter Kit Return' => $item['starter_kit_date'] ?? '-',
                    'Exit Interview' => $item['exit_interview_date'] ?? '-',
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
            'Employee Promotion' => $this->EmployeePromotion($request),
            'Employee Department Mutation' => $this->EmployeeDepartmentMutation($request),
            'One Year Service' => $this->EmployeeContractLongTerm($request),
            'Termination' => $this->termination($request),
            'Expired Contract' => $this->expiredContract($request),
            'Onboarding Report' => $this->onboardingReport($request),
            'Offboarding Report' => $this->offboardingReport($request),
            default => response()->json([]),
        };
    }

    // public function newEmployeeTetap(Request $request)
    // {
    //     $date = $request->input('date')
    //         ? Carbon::parse($request->input('date'))->startOfMonth()
    //         : Carbon::now()->startOfMonth();

    //     $endDate = $date->copy()->endOfMonth();

    //     $query = EmployeeJob::with(['user', 'position', 'department', 'section'])
    //         ->where('notes', 'New Employee Tetap')
    //         ->whereHas('user')
    //         ->whereBetween('start_date', [$date, $endDate]);

    //     return DataTables::of($query)
    //         ->addIndexColumn()
    //         ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
    //         ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
    //         ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
    //         ->addColumn('section', fn($job) => $job->section->section_name ?? 'N/A')
    //         ->addColumn('position', fn($job) => $job->position->position_name ?? 'N/A')
    //         ->addColumn('start_date', fn($job) => Carbon::parse($job->start_date)->isoFormat('D MMM Y'))
    //         ->make(true);
    // }

    public function newEmployeeTetap(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = User::whereHas('employeeJob', function ($q) use ($date, $endDate) {
            $q->where('notes', 'New Employee Tetap')
            ->whereBetween('start_date', [$date, $endDate]);
        })
        ->with(['employeeJob.department', 'employeeJob.position']);

        $interns = $query->get();

        $interns = $interns->transform(function ($user) use ($date, $endDate) {
            $job = $user->employeeJob
            ->where('notes', 'New Employee Tetap')
            ->whereBetween('start_date', [$date, $endDate])
            ->first();; 
            
            return [
                'npk' => $user->npk,
                'fullname' => $user->fullname,
                'department' => $job->department?->department_name ?? 'N/A',
                'section' => $job->section?->section_name ?? 'N/A',
                'position' => $job->position?->position_name ?? 'N/A',
                'start_date' => $job->start_date ? Carbon::parse($job->start_date)->isoFormat('D MMMM Y') : 'N/A',
                // 'end_date' => $job->end_date ? Carbon::parse($job->end_date)->isoFormat('D MMMM Y') : 'N/A',
                'duration' => $job->duration() ?? 'N/A',
                'notes' => $job->notes ?? 'N/A',
            ];
        });

        return DataTables::of($interns)
            ->addIndexColumn()
            ->make(true);
    }


    // public function newEmployeeKontrak(Request $request)
    // {
    //     $date = $request->input('date')
    //         ? Carbon::parse($request->input('date'))->startOfMonth()
    //         : Carbon::now()->startOfMonth();

    //     $endDate = $date->copy()->endOfMonth();

    //     $query = EmployeeJob::with(['user', 'position', 'department', 'section'])
    //         ->where('notes', 'New Employee Kontrak')
    //         ->whereHas('user')
    //         ->whereBetween('start_date', [$date, $endDate]);

    //     return DataTables::of($query)
    //         ->addIndexColumn()
    //         ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
    //         ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
    //         ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
    //         ->addColumn('section', fn($job) => $job->section->section_name ?? 'N/A')
    //         ->addColumn('position', fn($job) => $job->position->position_name ?? 'N/A')
    //         ->addColumn('start_date', fn($job) => Carbon::parse($job->start_date)->isoFormat('D MMM Y'))
    //         ->make(true);
    // }

    // public function newEmployeePemagangan(Request $request)
    // {
    //     $date = $request->input('date')
    //         ? Carbon::parse($request->input('date'))->startOfMonth()
    //         : Carbon::now()->startOfMonth();

    //     $endDate = $date->copy()->endOfMonth();

    //     $query = EmployeeJob::with(['user', 'position', 'department', 'section'])
    //         ->where('notes', 'New Employee Pemagangan')
    //         ->whereHas('user')
    //         ->whereBetween('start_date', [$date, $endDate]);

    //     return DataTables::of($query)
    //         ->addIndexColumn()
    //         ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
    //         ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
    //         ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
    //         ->addColumn('section', fn($job) => $job->section->section_name ?? 'N/A')
    //         ->addColumn('position', fn($job) => $job->position->position_name ?? 'N/A')
    //         ->addColumn('start_date', fn($job) => Carbon::parse($job->start_date)->isoFormat('D MMM Y'))
    //         ->make(true);
    // }

    
    public function newEmployeeKontrak(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = User::whereHas('employeeJob', function ($q) use ($date, $endDate) {
            $q->where('notes', 'New Employee Kontrak')
            ->whereBetween('start_date', [$date, $endDate]);
        })->with(['employeeJob.department', 'employeeJob.position']);

        $interns = $query->get();

        $interns = $interns->transform(function ($user) use ($date, $endDate) {
            $job = $user->employeeJob
            ->where('notes', 'New Employee Kontrak')
            ->whereBetween('start_date', [$date, $endDate])
            ->first();; 
            
            return [
                'npk' => $user->npk,
                'fullname' => $user->fullname,
                'department' => $job->department?->department_name ?? 'N/A',
                'section' => $job->section?->section_name ?? 'N/A',
                'position' => $job->position?->position_name ?? 'N/A',
                'start_date' => $job->start_date ? Carbon::parse($job->start_date)->isoFormat('D MMMM Y') : 'N/A',
                'end_date' => $job->end_date ? Carbon::parse($job->end_date)->isoFormat('D MMMM Y') : 'N/A',
                'duration' => $job->duration() ?? 'N/A',
                'notes' => $job->notes ?? 'N/A',
            ];
        });

        return DataTables::of($interns)
            ->addIndexColumn()
            ->make(true);
    }


    public function newEmployeePemagangan(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = User::whereHas('employeeJob', function ($q) use ($date, $endDate) {
            $q->where('notes', 'New Employee Pemagangan')
            ->whereBetween('start_date', [$date, $endDate]);
        })->with(['employeeJob.department', 'employeeJob.position']);

        $interns = $query->get();

        $interns = $interns->transform(function ($user) use ($date, $endDate) {
            $job = $user->employeeJob
            ->where('notes', 'New Employee Pemagangan')
            ->whereBetween('start_date', [$date, $endDate])
            ->first();; 
            
            return [
                'npk' => $user->npk,
                'fullname' => $user->fullname,
                'department' => $job->department?->department_name ?? 'N/A',
                'section' => $job->section?->section_name ?? 'N/A',
                'position' => $job->position?->position_name ?? 'N/A',
                'start_date' => $job->start_date ? Carbon::parse($job->start_date)->isoFormat('D MMMM Y') : 'N/A',
                'end_date' => $job->end_date ? Carbon::parse($job->end_date)->isoFormat('D MMMM Y') : 'N/A',
                'duration' => $job->duration() ?? 'N/A',
                'notes' => $job->notes ?? 'N/A',
            ];
        });

        return DataTables::of($interns)
            ->addIndexColumn()
            ->make(true);
    }

    public function newEmployeeIntern(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = User::whereHas('employeeJob', function ($q) use ($date, $endDate) {
            $q->where('notes', 'New Employee Internship')
            ->whereBetween('start_date', [$date, $endDate]);
        })->with(['employeeJob.department', 'employeeJob.position']);

        $interns = $query->get();

         $interns = $interns->transform(function ($user) use ($date, $endDate) {
            $job = $user->employeeJob
            ->where('notes', 'New Employee Internship')
            ->whereBetween('start_date', [$date, $endDate])
            ->first();; 
            
            return [
                'npk' => $user->npk,
                'fullname' => $user->fullname,
                'department' => $job->department?->department_name ?? 'N/A',
                'position' => $job->position?->position_name ?? 'N/A',
                'start_date' => $job->start_date ? Carbon::parse($job->start_date)->isoFormat('D MMMM Y') : 'N/A',
                'end_date' => $job->end_date ? Carbon::parse($job->end_date)->isoFormat('D MMMM Y') : 'N/A',
                'duration' => $job->duration() ?? 'N/A',
                'notes' => $job->notes ?? 'N/A',
            ];
        });

        return DataTables::of($interns)
            ->addIndexColumn()
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
            ->whereHas('user')
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
            ->whereHas('user')
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

    public function EmployeePromotion(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = EmployeeJob::with(['user', 'position', 'department'])
            ->where('notes', 'Employee Promotion')
            ->whereHas('user')
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
            ->whereHas('user')
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

        // $expiredContracts = EmployeeJob::with(['user', 'department', 'position'])
        //     ->whereBetween('end_date', [$date, $endDate])
        //     // ->whereNull('resign_date')
        //     //->where('employment_status', true)
        //     ->whereHas('user')
        //     ->get();

        $expiredContracts = EmployeeJob::with(['user', 'department', 'position'])
        ->where(function ($query) use ($date, $endDate) {
            $query->where(function ($q) use ($date, $endDate) {
                $q->whereNotNull('resign_date')
                ->whereBetween('resign_date', [$date, $endDate]);
            })
            ->orWhere(function ($q) use ($date, $endDate) {
                $q->whereNull('resign_date')
                ->whereBetween('end_date', [$date, $endDate]);
            });
        })
        ->whereHas('user')
        ->get();

        // dd($expiredContracts);

        $transformedContracts = $expiredContracts->transform(function ($job) {
            $user = $job->user;
            return [
                'npk' => $user ? $user->npk : 'N/A',
                'fullname' => $user ? $user->fullname : 'N/A',
                // 'department' => $job->department ? $job->department->department_name : 'N/A',
                // 'position' => $job->position ? $job->position->position_name : 'N/A',
                'department' => $job->department ? html_entity_decode($job->department->department_name) : 'N/A',
                'position' => $job->position ? html_entity_decode($job->position->position_name) : 'N/A',
                'start_date' => $job->start_date ? Carbon::parse((string)$job->start_date)->isoFormat('D MMMM Y') : 'N/A',
                'end_date' => $job->end_date ? Carbon::parse((string)$job->end_date)->isoFormat('D MMMM Y') : 'N/A',
                'status' => $job->contract,
            ];
        });

        return DataTables::of($transformedContracts)
            ->addIndexColumn()
            ->rawColumns(['department', 'position'])
            ->make(true);
    }

    public function termination(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $termination = User::whereHas('offboarding')->whereHas('latestEmployeeJob', function ($q) use ($date, $endDate) {
            $q->where('employment_status', false);
            $q->whereBetween('resign_date', [$date, $endDate]);
        })->get();
        // dd($termination);

        $termination = $termination->transform(function ($user) {
            return [
                'npk' => $user->npk,
                'fullname' => $user->fullname,
                'department' => $user->latestEmployeeJob->department?->department_name ?? 'N/A',
                'position' => $user->latestEmployeeJob->position?->position_name ?? 'N/A',
                'start_date' => $user->latestEmployeeJob->start_date ? Carbon::parse((string)$user->latestEmployeeJob->start_date)->isoFormat('D MMMM Y') : 'N/A',
                'end_date' => $user->latestEmployeeJob->end_date ? Carbon::parse((string)$user->latestEmployeeJob->end_date)->isoFormat('D MMMM Y') : 'N/A',
                'out_date' => $user->latestEmployeeJob->resign_date ? Carbon::parse((string)$user->latestEmployeeJob->resign_date)->isoFormat('D MMMM Y') : 'N/A',
                'reason' => $user->offboarding->reason ? $user->offboarding->reason : 'N/A',
                'status' => $user->latestEmployeeJob?->contract ? $user->latestEmployeeJob->contract : 'N/A',
            ];
        });

        return DataTables::of($termination)
            ->addIndexColumn()
            ->make(true);
    }

    // public function onboardingReport(Request $request)
    // {
    //     $date = $request->input('date')
    //         ? Carbon::parse($request->input('date'))->startOfMonth()
    //         : Carbon::now()->startOfMonth();

    //     $endDate = $date->copy()->endOfMonth();

    //     $query = EmployeeJob::with(['user', 'position', 'department', 'section'])
    //         ->whereIn('notes', [
    //             'New Employee Kontrak',
    //             'New Employee Tetap',
    //             'New Employee Pemagangan',
    //             'New Employee Internship'
    //         ])
    //         ->whereHas('user')
    //         ->whereBetween('start_date', [$date, $endDate]);

    //     return DataTables::of($query)
    //         ->addIndexColumn()
    //         ->addColumn('fullname', fn($job) => $job->user->fullname ?? 'N/A')
    //         ->addColumn('npk', fn($job) => $job->user->npk ?? 'N/A')
    //         ->addColumn('department', fn($job) => $job->department->department_name ?? 'N/A')
    //         ->addColumn('section', fn($job) => $job->section->section_name ?? 'N/A')
    //         ->addColumn('position', fn($job) => $job->position->position_name ?? 'N/A')
    //         ->addColumn('start_date', fn($job) => Carbon::parse($job->start_date)->isoFormat('D MMM Y'))
    //         ->addColumn('status', fn($job) => $job->contract ?? 'N/A')
    //         ->addColumn('deadline_pre', function ($job) {
    //             $user = $job->user;
    //             $job = $user->firstEmployeeJob;
    //             if (!$job || !$job->start_date) {
    //                 return 'N/A';
    //             }
    //             $startDate = Carbon::parse($job->start_date)->endOfDay();
    //             $deadline = $startDate->copy()->subDay();
    //             return $deadline->isoFormat('D MMMM YYYY') ?? 'N/A';
    //         })

    //         // STEP: Create Employee
    //         ->addColumn('create_employee', function ($job) {
    //             return $this->checkStepStatus($job, fn($user, $job) => $user->created_at, '-1 day');
    //         })
    //         ->addColumn('create_employee_overdue_days', function ($job) {
    //             return $this->getOverdueDays($job, fn($user, $job) => $user->created_at, '-1 day');
    //         })
    //         ->addColumn('create_employee_completion_date', function ($job) {
    //             $user = $job->user;
    //             return optional($user->created_at)?->format('Y-m-d') ?? '-';
    //         })

    //         // STEP: Employment Data
    //         ->addColumn('employment_data', function ($job) {
    //             return $this->checkStepStatus($job, fn($user, $job) => $user->progressOnboardingAdmin()['progress'] >= 17 ? $job->created_at : null, '-1 day');
    //         })
    //         ->addColumn('employment_data_overdue_days', function ($job) {
    //             return $this->getOverdueDays($job, fn($user, $job) => $user->progressOnboardingAdmin()['progress'] >= 17 ? $job->created_at : null, '-1 day');
    //         })
    //         ->addColumn('employment_data_completion_date', function ($job) {
    //             $user = $job->user;
    //             return $user->progressOnboardingAdmin()['progress'] >= 17 && $job->created_at ? $job->created_at->format('Y-m-d') : '-';
    //         })

    //         // STEP: Starter Kit
    //         ->addColumn('starter_kit', function ($job) {
    //             return $this->checkStepStatus($job, function ($user, $job) {
    //                 return optional($job?->inventory)->where('employee_job_id', $job?->id)?->last()?->created_at;
    //             }, '-1 day');
    //         })
    //         ->addColumn('starter_kit_overdue_days', function ($job) {
    //             return $this->getOverdueDays($job, function ($user, $job) {
    //                 return optional($job?->inventory)->where('employee_job_id', $job?->id)?->last()?->created_at;
    //             }, '-1 day');
    //         })
    //         ->addColumn('starter_kit_completion_date', function ($job) {
    //             $user = $job->user;
    //             $completionDate = optional($job?->inventory)->where('employee_job_id', $job?->id)?->last()?->created_at;
    //             return $completionDate ? $completionDate->format('Y-m-d') : '-';
    //         })

    //         ->addColumn('deadline_on', function ($job) {
    //             $user = $job->user;
    //             $job = $user->firstEmployeeJob;
    //             if (!$job || !$job->start_date) return 'N/A';
    //             return $job->start_date->isoFormat('D MMMM YYYY') ?? 'N/A';
    //         })

    //         ->addColumn('starter_kit_acc', function ($job) {
    //             return $this->checkStepStatus($job, function ($user, $job) {
    //                 return optional($job?->inventory)->where('employee_job_id', $job?->id)?->where('status', 'Diterima')?->last()?->updated_at;
    //             }, '0 day');
    //         })

    //          ->addColumn('starter_kit_acc_overdue_days', function ($job) {
    //             return $this->getOverdueDays($job, function ($user, $job) {
    //                 return optional($job?->inventory)->where('employee_job_id', $job?->id)?->where('status', 'Diterima')?->last()?->created_at;
    //             }, '0 day');
    //         })
    //         ->addColumn('starter_kit_acc_completion_date', function ($job) {
    //             $user = $job->user;
    //             $completionDate = optional($job?->inventory)->where('employee_job_id', $job?->id)?->where('status', 'Diterima')?->last()?->created_at;
    //             return $completionDate ? $completionDate->format('Y-m-d') : '-';
    //         })

    //         ->addColumn('contract_signature', function ($job) {
    //             return $this->checkStepStatus($job, function ($user, $job) {
    //                 return optional($job?->jobDoc)
    //                     ->where('employee_job_id', $job?->id)
    //                     ->where('type', 'contract')
    //                     ->whereNotNull('first_party_signature')
    //                     ?->last()?->updated_at;
    //             }, '0 day');
    //         })
    //         ->addColumn('contract_signature_completion_date', function ($job) {
    //             $completionDate = optional($job?->jobDoc)
    //                 ->where('employee_job_id', $job?->id)
    //                 ->where('type', 'contract')
    //                 ->whereNotNull('first_party_signature')
    //                 ?->last()?->updated_at;
    //             return $completionDate ? $completionDate->format('Y-m-d') : '-';
    //         })
    //         ->addColumn('contract_signature_overdue_days', function ($job) {
    //             return $this->getOverdueDays($job, function ($user, $job) {
    //                 return optional($job?->jobDoc)
    //                     ->where('employee_job_id', $job?->id)
    //                     ->where('type', 'contract')
    //                     ->whereNotNull('first_party_signature')
    //                     ?->last()?->updated_at;
    //             }, '0 day');
    //         })

    //         ->addColumn('deadline_post', function ($job) {
    //             $user = $job->user;
    //             $isKaryawan = $user->dakarRole->contains(function ($role) {
    //                 return strtolower($role->role_name) === 'karyawan';
    //             });
    //             if (!$isKaryawan) return 'N/A';

    //             $job = $user->firstEmployeeJob;
    //             if (!$job || !$job->start_date) {
    //                 return 'N/A';
    //             }
    //             $startDate = Carbon::parse($job->start_date)->addMonth();
    //             $deadline = $startDate->copy()->subDay();
    //             return $deadline->isoFormat('D MMMM YYYY') ?? 'N/A';
    //         })

    //         // STEP: Greatday
    //         ->addColumn('greatday', function ($job) {
    //             return $this->checkStepStatus($job, fn($user, $job) => $this->getItemCompletion($user, 'User Account Great Day'), '30 day', true);
    //         })
    //         ->addColumn('greatday_overdue_days', function ($job) {
    //             return $this->getOverdueDays($job, fn($user, $job) => $this->getItemCompletion($user, 'User Account Great Day'), '30 day', true);
    //         })
    //         ->addColumn('greatday_completion_date', function ($job) {
    //             $completion = $this->getItemCompletion($job->user, 'User Account Great Day');
    //             return $completion ? $completion->format('Y-m-d') : '-';
    //         })

    //         // STEP: E-Slip
    //         ->addColumn('eslip', function ($job) {
    //             return $this->checkStepStatus($job, fn($user, $job) => $this->getItemCompletion($user, 'User Account E-Slip'), '30 day', true);
    //         })
    //         ->addColumn('eslip_overdue_days', function ($job) {
    //             return $this->getOverdueDays($job, fn($user, $job) => $this->getItemCompletion($user, 'User Account E-Slip'), '30 day', true);
    //         })
    //         ->addColumn('eslip_completion_date', function ($job) {
    //             $completion = $this->getItemCompletion($job->user, 'User Account E-Slip');
    //             return $completion ? $completion->format('Y-m-d') : '-';
    //         })

    //         // STEP: BPJS Kesehatan
    //         ->addColumn('bpjskes', function ($job) {
    //             return $this->checkStepStatus($job, fn($user, $job) => $this->getItemCompletion($user, 'BPJS Kesehatan'), '30 day', true);
    //         })
    //         ->addColumn('bpjskes_overdue_days', function ($job) {
    //             return $this->getOverdueDays($job, fn($user, $job) => $this->getItemCompletion($user, 'BPJS Kesehatan'), '30 day', true);
    //         })
    //         ->addColumn('bpjskes_completion_date', function ($job) {
    //             $completion = $this->getItemCompletion($job->user, 'BPJS Kesehatan');
    //             return $completion ? $completion->format('Y-m-d') : '-';
    //         })

    //         // STEP: BPJS TK
    //         ->addColumn('bpjstk', function ($job) {
    //             return $this->checkStepStatus($job, fn($user, $job) => $this->getItemCompletion($user, 'BPJS TK'), '30 day', true);
    //         })
    //         ->addColumn('bpjstk_overdue_days', function ($job) {
    //             return $this->getOverdueDays($job, fn($user, $job) => $this->getItemCompletion($user, 'BPJS TK'), '30 day', true);
    //         })
    //         ->addColumn('bpjstk_completion_date', function ($job) {
    //             $completion = $this->getItemCompletion($job->user, 'BPJS TK');
    //             return $completion ? $completion->format('Y-m-d') : '-';
    //         })

    //         ->rawColumns(['create_employee', 'employment_data', 'starter_kit', 'greatday', 'eslip', 'bpjskes', 'bpjstk', 'starter_kit_acc', 'contract_signature'])
    //         ->make(true);
    // }

    public function onboardingReport(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $query = User::whereHas('employeeJob', function ($q) use ($date, $endDate) {
            $q->whereIn('notes', [
                'New Employee Kontrak',
                'New Employee Tetap',
                'New Employee Pemagangan',
                'New Employee Internship'
            ])
            ->whereBetween('start_date', [$date, $endDate]);
        })->with([
            'employeeJob.department', 
            'employeeJob.section', 
            'employeeJob.position',
            'employeeJob.inventory',
            'employeeJob.jobDoc',
            'dakarRole'
        ]);

        $users = $query->get();

        $report = $users->transform(function ($user) use ($date, $endDate) {
            $job = $user->employeeJob
                ->whereIn('notes', [
                    'New Employee Kontrak',
                    'New Employee Tetap',
                    'New Employee Pemagangan',
                    'New Employee Internship'
                ])
                ->whereBetween('start_date', [$date, $endDate])
                ->first();

            $startDate = $job->start_date ? Carbon::parse($job->start_date) : null;
            $deadlinePreText = $startDate ? $startDate->copy()->subDay()->isoFormat('D MMMM YYYY') : 'N/A';
            
            $isKaryawan = $user->dakarRole->contains(fn($role) => strtolower($role->role_name) === 'karyawan');
            $deadlinePostText = ($isKaryawan && $startDate) ? $startDate->copy()->addMonth()->subDay()->isoFormat('D MMMM YYYY') : 'N/A';

            return [
                'npk' => $user->npk,
                'fullname' => $user->fullname,
                'department' => $job->department?->department_name ?? 'N/A',
                'section' => $job->section?->section_name ?? 'N/A',
                'position' => $job->position?->position_name ?? 'N/A',
                'start_date' => $startDate ? $startDate->isoFormat('D MMM Y') : 'N/A',
                'status' => $job->contract ?? 'N/A',
                
                'deadline_pre' => $deadlinePreText,
                'deadline_on' => $startDate ? $startDate->isoFormat('D MMMM YYYY') : 'N/A',
                'deadline_post' => $deadlinePostText,

                'create_employee' => $this->checkStepStatus($job, fn($u, $j) => $u->created_at, '-1 day'),
                'create_employee_overdue_days' => $this->getOverdueDays($job, fn($u, $j) => $u->created_at, '-1 day'),
                'create_employee_completion_date' => optional($user->created_at)->format('Y-m-d') ?? '-',

                'employment_data' => $this->checkStepStatus($job, fn($u, $j) => $u->progressOnboardingAdmin()['progress'] >= 17 ? $j->created_at : null, '-1 day'),
                'employment_data_completion_date' => ($user->progressOnboardingAdmin()['progress'] >= 17 && $job->created_at) ? $job->created_at->format('Y-m-d') : '-',

                'starter_kit' => $this->checkStepStatus($job, function ($u, $j) {
                    return $j->inventory->where('employee_job_id', $j->id)->last()?->created_at;
                }, '-1 day'),
                
                'starter_kit_acc' => $this->checkStepStatus($job, function ($u, $j) {
                    return $j->inventory->where('employee_job_id', $j->id)->where('status', 'Diterima')->last()?->updated_at;
                }, '0 day'),

                'contract_signature' => $this->checkStepStatus($job, function ($u, $j) {
                    return $j->jobDoc->where('employee_job_id', $j->id)->where('type', 'contract')->whereNotNull('first_party_signature')->last()?->updated_at;
                }, '0 day'),

                'greatday' => $this->checkStepStatus($job, fn($u, $j) => $this->getItemCompletion($u, 'User Account Great Day'), '30 day', true),
                'eslip'    => $this->checkStepStatus($job, fn($u, $j) => $this->getItemCompletion($u, 'User Account E-Slip'), '30 day', true),
                'bpjskes'  => $this->checkStepStatus($job, fn($u, $j) => $this->getItemCompletion($u, 'BPJS Kesehatan'), '30 day', true),
                'bpjstk'   => $this->checkStepStatus($job, fn($u, $j) => $this->getItemCompletion($u, 'BPJS TK'), '30 day', true),
                
                'greatday_completion_date' => $this->getItemCompletion($user, 'User Account Great Day')?->format('Y-m-d') ?? '-',
                'bpjskes_completion_date'  => $this->getItemCompletion($user, 'BPJS Kesehatan')?->format('Y-m-d') ?? '-',
            ];
        });

        return DataTables::of($report)
            ->addIndexColumn()
            ->rawColumns(['create_employee', 'employment_data', 'starter_kit', 'greatday', 'eslip', 'bpjskes', 'bpjstk', 'starter_kit_acc', 'contract_signature'])
            ->make(true);
    }

    // public function offboardingReport(Request $request)
    // {
    //     $date = $request->input('date')
    //         ? Carbon::parse($request->input('date'))->startOfMonth()
    //         : Carbon::now()->startOfMonth();

    //     $endDate = $date->copy()->endOfMonth();

    //     $query = User::whereHas('latestEmployeeJob')->whereHas('offboarding', function($q) use ($date, $endDate){
    //         $q->whereBetween('resign_date', [$date, $endDate]);
    //     })
    //     ->with([
    //         'latestEmployeeJob.department', 
    //         'latestEmployeeJob.section', 
    //         'latestEmployeeJob.position',
    //         'latestEmployeeJob.inventory',
    //         'latestEmployeeJob.jobDoc',
    //         'dakarRole',
    //         'offboarding',
    //     ]);

    //     $users = $query->get();

    //     $report = $users->transform(function ($user) use ($date, $endDate) {
    //         $job = $user->latestEmployeeJob;
    //         $offboard = $user->offboarding->first();
    //         // $offboard = $user->offboarding
    //         // ->whereBetween('resign_date', [$date->format('Y-m-d'), $endDate->format('Y-m-d')])
    //         // ->first();
    //         $deadline = $offboard?->resign_date ? Carbon::parse($offboard->resign_date)->addDays(2) : null;            
    //         //$isKaryawan = $user->dakarRole->contains(fn($role) => strtolower($role->role_name) === 'karyawan');
    //         //$exitIntv = $user->offboarding->where('exit_interview', true)->first();

    //         return [
    //             'npk' => $user->npk,
    //             'fullname' => $user->fullname,
    //             'department' => $job->department?->department_name ?? 'N/A',
    //             'section' => $job->section?->section_name ?? 'N/A',
    //             'position' => $job->position?->position_name ?? 'N/A',
    //             'status' => $job->contract ?? 'N/A',
    //             'resign_date' => $offboard->resign_date ? Carbon::parse($offboard->resign_date)->isoFormat('D MMMM YYYY') : 'N/A',
    //             'reason' => $offboard->reason,
    //             'deadline' => $deadline,

    //             'exit_interview' => $this->checkStepStatusOff($job, function ($u, $j) {
    //                 return $j->user->offboarding->where('exit_interview', true)->first()?->updated_at;
    //             }, '2 day'),

    //             'exit_interview_date' => $job->user->offboarding->where('exit_interview', true)->first()?->updated_at?->format('d M Y') ?? '-',
                
    //             'starter_kit_return' => $this->checkStepStatusOff($job, function ($u, $j) {
    //                 return $j->inventory->where('employee_job_id', $j->id)->where('status', '!=', 'Diterima')->first()?->updated_at;
    //             }, '2 day'),

    //             'starter_kit_date' => $job->inventory->where('employee_job_id', $job->id)->where('status', '!=', 'Diterima')->first()?->updated_at?->format('d M Y') ?? '-',

    //             'sksmk_signature_1' => $job->user_dakar_role === 'internship' ? '-' : $this->checkStepStatusOff($job, function ($u, $j) {
    //                 return $j->jobDoc->where('employee_job_id', $j->id)->where('type', 'sksmk')->whereNotNull('first_party_signature')->first()?->first_party_signature_date;
    //             }, '2 day'),

    //             'signature_1_date' => $job->user_dakar_role === 'internship' ? '-' : ($job->jobDoc->where('employee_job_id', $job->id)->where('type', 'sksmk')->whereNotNull('first_party_signature')->first() ? Carbon::parse($job->jobDoc->where('employee_job_id', $job->id)->where('type', 'sksmk')->whereNotNull('first_party_signature')->first()->first_party_signature_date)->format('d M Y') : '-'),

    //             'sksmk_signature_2' => $job->user_dakar_role === 'internship' ? '-' : $this->checkStepStatusOff($job, function ($u, $j) {
    //                 return $j->jobDoc->where('employee_job_id', $j->id)->where('type', 'sksmk')->whereNotNull('second_party_signature')->first()?->second_party_signature_date;
    //             }, '2 day'),

    //             'signature_2_date' => $job->user_dakar_role === 'internship' ? '-' : ($job->jobDoc->where('employee_job_id', $job->id)->where('type', 'sksmk')->whereNotNull('second_party_signature')->first() ? Carbon::parse($job->jobDoc->where('employee_job_id', $job->id)->where('type', 'sksmk')->whereNotNull('second_party_signature')->first()->second_party_signature_date)->format('d M Y') : '-'),

    //         ];
    //     });

    //     return DataTables::of($report)
    //         ->addIndexColumn()
    //         ->rawColumns(['exit_interview', 'starter_kit_return', 'sksmk_signature_1', 'sksmk_signature_2'])
    //         ->make(true);
    // }

    public function offboardingReport(Request $request)
    {
        // 1. Gunakan parse()->format() agar kita punya string murni, bukan objek yang bisa berubah-ubah
        $requestedDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        
        $startDateString = $requestedDate->copy()->startOfMonth()->toDateString(); // Contoh: 2026-03-01
        $endDateString = $requestedDate->copy()->endOfMonth()->toDateString();   // Contoh: 2026-03-31

        // 2. Query Utama - Load semua employeeJob bukan hanya latestEmployeeJob
        $users = User::whereHas('latestEmployeeJob')
            ->whereHas('offboardingMany', function($q) use ($startDateString, $endDateString) {
                // Gunakan string mentah agar tidak ada mutasi objek
                $q->whereBetween('resign_date', [$startDateString, $endDateString]);
            })
            // ->whereHas('dakarRole', function($q){
            //     $q->where('role_name', 'karyawan');
            // })
            ->with([
                'employeeJob.department', 
                'employeeJob.section', 
                'employeeJob.position',
                'employeeJob.inventory',
                'employeeJob.jobDoc',
                'dakarRole',
                'offboardingMany' => function($q) use ($startDateString, $endDateString) {
                    $q->whereBetween('resign_date', [$startDateString, $endDateString])
                    ->orderBy('resign_date', 'desc');
                },
            ])
            ->get();

        // 3. Transformasi
        $report = $users->map(function ($user) use ($startDateString, $endDateString) {
            // Cari offboardingMany yang masuk dalam range bulan ini saja dari koleksi yang sudah di-load
            $offboard = $user->offboardingMany
                ->whereBetween('resign_date', [$startDateString, $endDateString])
                ->first();

            // Jika offboard null karena alasan teknis, ambil yang paling pertama tersedia
            if (!$offboard) {
                $offboard = $user->offboardingMany->first();
            }

            // PERBAIKAN: Cari job yang sesuai dengan resign_date dari offboarding, bukan latestEmployeeJob
            // Ini mengatasi kasus: karyawan offboarding dari pemagangan lalu naik kontrak
            $job = null;
            
            if ($offboard && $offboard->resign_date) {
                $resignDate = Carbon::parse($offboard->resign_date);
                $resignDateString = $resignDate->toDateString();
                
                // Prioritas 1: Cari job dengan employment_status = false (sudah offboard)
                // dan resign_date dekat dengan offboarding (toleransi 1 hari untuk handle perbedaan tanggal)
                $job = $user->employeeJob
                    ->where('employment_status', false)
                    ->filter(function ($j) use ($resignDate) {
                        if (!$j->resign_date) return false;
                        $jobResignDate = Carbon::parse($j->resign_date);
                        // Toleransi 1 hari untuk handle kasus resign_date yang sedikit berbeda
                        return $jobResignDate->diffInDays($resignDate) <= 1;
                    })
                    ->sortByDesc('start_date')
                    ->first();
                
                // Prioritas 2: Jika tidak ada, cari berdasarkan start_date <= resign_date (job yang aktif saat resign)
                if (!$job) {
                    $job = $user->employeeJob
                        ->filter(function ($j) use ($resignDateString) {
                            $start = Carbon::parse($j->start_date)->toDateString();
                            $end = $j->end_date ? Carbon::parse($j->end_date)->toDateString() : null;
                            
                            // Job aktif saat resign jika: start_date <= resign_date dan (end_date tidak ada atau end_date >= resign_date)
                            return $start <= $resignDateString && (!$end || $end >= $resignDateString);
                        })
                        ->sortByDesc('start_date')
                        ->first();
                }
            }
            
            // Fallback ke latestEmployeeJob jika tidak menemukan job yang sesuai
            if (!$job) {
                $job = $user->latestEmployeeJob ?? $user->employeeJob->last();
            }

            return [
                'npk'               => $job->npk,
                'fullname'          => $user->fullname,
                'department'        => $job->department?->department_name ?? 'N/A',
                'section'           => $job->section?->section_name ?? 'N/A',
                'position'          => $job->position?->position_name ?? 'N/A',
                'status'            => $job->contract ?? 'N/A',
                
                // Tampilkan Tanggal Resign
                'offboard_id'       => $offboard?->id ?? '-',
                'resign_date'       => $offboard && $offboard->resign_date 
                                        ? Carbon::parse($offboard->resign_date)->isoFormat('D MMMM YYYY') 
                                        : 'N/A',
                                        
                'reason'            => $offboard?->reason ?? '-',
                'deadline'          => $offboard?->resign_date 
                                        ? Carbon::parse($offboard->resign_date)->addDays(2)->format('d M Y') 
                                        : '-',

                'exit_interview'    => ($offboard?->reason != 'Join AVI') ? $this->checkStepStatusOff($job, function ($u, $j) {
                    return $j->user->offboardingMany->where('exit_interview', true)->first()?->updated_at;
                }, '2 day') : '-',

                'exit_interview_date' => $user->offboardingMany->where('exit_interview', true)->first()?->updated_at?->format('d M Y') ?? '-',
                
                'starter_kit_return' => $this->checkStepStatusOff($job, function ($u, $j) {
                    return $j->inventory->where('status', '!=', 'Diterima')->first()?->updated_at;
                }, '2 day'),

                'starter_kit_date'   => $job->inventory->where('status', '!=', 'Diterima')->first()?->updated_at?->format('d M Y') ?? '-',

                'sksmk_signature_1'  => $job->user_dakar_role === 'internship' ? '-' : $this->checkStepStatusOff($job, function ($u, $j) {
                    return $j->jobDoc->where('type', 'sksmk')->whereNotNull('first_party_signature')->first()?->first_party_signature_date;
                }, '2 day'),

                'signature_1_date'   => $job->user_dakar_role === 'internship' ? '-' : ($job->jobDoc->where('type', 'sksmk')->whereNotNull('first_party_signature')->first() ? Carbon::parse($job->jobDoc->where('type', 'sksmk')->whereNotNull('first_party_signature')->first()->first_party_signature_date)->format('d M Y') : '-'),

                'sksmk_signature_2'  => $job->user_dakar_role === 'internship' ? '-' : $this->checkStepStatusOff($job, function ($u, $j) {
                    return $j->jobDoc->where('type', 'sksmk')->whereNotNull('second_party_signature')->first()?->second_party_signature_date;
                }, '2 day'),

                'signature_2_date'   => $job->user_dakar_role === 'internship' ? '-' : ($job->jobDoc->where('type', 'sksmk')->whereNotNull('second_party_signature')->first() ? Carbon::parse($job->jobDoc->where('type', 'sksmk')->whereNotNull('second_party_signature')->first()->second_party_signature_date)->format('d M Y') : '-'),
            ];
        });

        return DataTables::of($report)
            ->addIndexColumn()
            ->rawColumns(['exit_interview', 'starter_kit_return', 'sksmk_signature_1', 'sksmk_signature_2'])
            ->make(true);
    }
    
    private function checkStepStatus($job, $completionCallback, $deadlineDiff, $onlyKaryawan = false)
    {
        $user = $job->user;
        if ($onlyKaryawan && !$user->dakarRole->contains(fn($role) => strtolower($role->role_name) === 'karyawan')) {
            return 'N/A';
        }

        $job = $user->firstEmployeeJob;
        if (!$job || !$job->start_date) return 'N/A';

        $deadline = Carbon::parse($job->start_date)->add($deadlineDiff);
        $completionDate = $completionCallback($user, $job);

        if ($completionDate) {
            $completion = Carbon::parse($completionDate)->startOfDay();
            if ($completion->lte($deadline)) {
                return '<span class="badge bg-success">On Time</span><br><small>' . $completion->format('d M Y') . '</small>';
            } else {
                $overdueDays = $deadline->diffInDays($completion);
                return '<span class="badge bg-danger">Overdue ' . $overdueDays . ' hari</span><br><small>' . $completion->format('d M Y') . '</small>';
            }
        } else {
            $now = Carbon::now()->startOfDay();
            if ($now->lte($deadline)) {
                return '<span class="badge bg-warning">Deadline : ' . $deadline->format('d M Y') . '</span><br><small> Today : ' . $now->format('d M Y') . '</small>';
            } else {
                $overdueDays = $deadline->diffInDays($now);
                return '<span class="badge bg-danger">Overdue ' . $overdueDays . ' hari</span><br><small> Today : ' . $now->format('d M Y') . '</small>';
            }
        }
    }

    private function checkStepStatusOff($job, $completionCallback, $deadlineDiff, $onlyKaryawan = false)
    {
        $user = $job->user;
        if ($onlyKaryawan && !$user->dakarRole->contains(fn($role) => strtolower($role->role_name) === 'karyawan')) {
            return 'N/A';
        }

        $job = $user->latestEmployeeJob;
        if (!$job || !$job->start_date) return 'N/A';

        $offboard = $user->offboarding->first();
        $deadline = $offboard?->resign_date ? Carbon::parse($offboard->resign_date)->addDays(2) : null;

        // $deadline = Carbon::parse($job->start_date)->add($deadlineDiff);
        $completionDate = $completionCallback($user, $job);

        if ($completionDate) {
            $completion = Carbon::parse($completionDate)->startOfDay();
            if ($completion->lte($deadline)) {
                return '<span class="badge bg-success">On Time</span><br><small>' . $completion->format('d M Y') . '</small>';
            } else {
                $overdueDays = $deadline->diffInDays($completion);
                return '<span class="badge bg-danger">Overdue ' . $overdueDays . ' hari</span><br><small>' . $completion->format('d M Y') . '</small>';
            }
        } else {
            $now = Carbon::now()->startOfDay();
            if ($now->lte($deadline)) {
                return '<span class="badge bg-warning">Deadline : ' . $deadline->format('d M Y') . '</span><br><small> Today : ' . $now->format('d M Y') . '</small>';
            } else {
                $overdueDays = $deadline->diffInDays($now);
                return '<span class="badge bg-danger">Overdue ' . $overdueDays . ' hari</span><br><small> Today : ' . $now->format('d M Y') . '</small>';
            }
        }
    }

    private function getOverdueDays($job, $completionCallback, $deadlineDiff, $onlyKaryawan = false)
    {
        $user = $job->user;
        if ($onlyKaryawan && !$user->dakarRole->contains(fn($role) => strtolower($role->role_name) === 'karyawan')) {
            return '-';
        }

        $job = $user->firstEmployeeJob;
        if (!$job || !$job->start_date) return '-';

        $deadline = Carbon::parse($job->start_date)->add($deadlineDiff);
        $completionDate = $completionCallback($user, $job);

        if ($completionDate) {
            $completion = Carbon::parse($completionDate)->startOfDay();
            if ($completion->lte($deadline)) return '-';
            return $deadline->diffInDays($completion);
        } else {
            $now = Carbon::now()->startOfDay();
            if ($now->lte($deadline)) return '-';
            return $deadline->diffInDays($now);
        }
    }

    private function getItemCompletion($user, $itemName)
    {
        $itemId = Item::where('item_name', $itemName)->value('id');
        $record = EmployeeInventoryNumber::where('user_id', $user->id)
            ->where('item_id', $itemId)
            ->orderByDesc('created_at')
            ->first();

        return $record ? Carbon::parse($record->created_at) : null;
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
