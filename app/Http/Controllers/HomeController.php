<?php

namespace App\Http\Controllers;

use App\Models\DakarRole;
use App\Models\EmployeeDetail;
use App\Models\EmployeeJob;
use App\Models\Inventory;
use App\Models\JobDoc;
use App\Models\User;
use App\Exports\SignaturesExport;
use App\Exports\CompensationsExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        try {
            $adminRoles = ['admin', 'admin 2', 'admin 3', 'admin 4'];
            $authRole = Auth::user()->getRole();
            $user = Auth::user();
            //dd($authRole);
            // dd(in_array($authRole, $adminRoles));
            // dd($user->password);
            //dd($user && Hash::check('Avi123!', $user->password_hash ?? $user->password));
            if ($user && Hash::check('Avi123!', $user->password_hash ?? $user->password)) {
                // dd('true');
                return redirect('/update-password');
            }

            if (in_array($authRole, $adminRoles)) {
                $baseUser = User::query()
                    ->with([
                        'dakarRole',
                        'latestEmployeeJob',
                        'latestEmployeeJob.department',
                        'employeeJob.jobDoc',
                        'employeeJob.jobWageAllowance',
                        'employeeJob.inventory.item',
                        'firstEmployeeJob',
                        'inventory',
                        'inventory.item',
                        'inventory.employeeJob',
                        'employeeDetail',
                        'employeeEducations',
                        'employeeBanks',
                        'employeeDocs',
                        'employeeInventoryNumber',
                    ]);


                $pemaganganRoleId = DakarRole::where('role_name', 'pemagangan')->value('id');
                $internshipRoleId = DakarRole::where('role_name', 'internship')->value('id');
                $karyawanRoleId = DakarRole::where('role_name', 'karyawan')->value('id');

                $pemagangan = (clone $baseUser)
                    ->whereHas('dakarRole', fn($q) => $q->where('dakar_role_user.dakar_role_id', $pemaganganRoleId))
                    ->whereHas('latestEmployeeJob', fn($q) => $q->where('employment_status', true))
                    ->count();

                $internship = (clone $baseUser)
                    ->whereHas('dakarRole', fn($q) => $q->where('dakar_role_user.dakar_role_id', $internshipRoleId))
                    ->whereHas('latestEmployeeJob', fn($q) => $q->where('employment_status', true))
                    ->count();

                $karyawan = (clone $baseUser)
                    ->whereHas('dakarRole', fn($q) => $q->where('dakar_role_user.dakar_role_id', $karyawanRoleId))
                    ->whereHas('latestEmployeeJob', fn($q) => $q->where('employment_status', true))
                    ->count();

                $jobType = DB::table('dakar_employee_job as ej')
                    ->join('dakar_job_type as jt', 'ej.job_type_id', '=', 'jt.id')
                    ->where('ej.employment_status', true)
                    ->select('jt.job_type_name', DB::raw('COUNT(*) as total'))
                    ->groupBy('jt.job_type_name')
                    ->pluck('total', 'jt.job_type_name');

                $departments = DB::table('dakar_departments')
                    ->leftJoin('dakar_employee_job', function ($join) {
                        $join->on('dakar_departments.id', '=', 'dakar_employee_job.department_id')
                            ->where('dakar_employee_job.employment_status', true);
                    })
                    ->select('dakar_departments.department_name', DB::raw('COUNT(dakar_employee_job.id) as total'))
                    ->groupBy('dakar_departments.department_name')
                    ->pluck('total', 'dakar_departments.department_name');

                $now = Carbon::now()->addMonth(2);
                $expiredThisMonth = EmployeeJob::with(['department', 'user'])
                    ->where('employment_status', true)
                    ->where('job_status', 'kontrak')
                    ->where('user_dakar_role', 'karyawan')
                    ->whereMonth('end_date', $now->month)
                    ->whereYear('end_date', $now->year)
                    ->take(5)
                    ->get();

                // $uniformRefresh = Inventory::with(['user', 'item', 'employeeJob.department'])
                //     ->whereHas('item', fn($q) => $q->where('type', 'baju'))
                //     ->whereHas('employeeJob', fn($q) => $q->where('employment_status', true))
                //     ->where('acc_date', '<=', Carbon::now()->subMonths(12))
                //     ->where('status', 'Diterima')
                //     ->get()
                //     ->map(fn($inv) => [
                //         'id' => $inv->user_id,
                //         'npk' => $inv->user->npk,
                //         'name' => $inv->user->fullname,
                //         'department' => optional($inv->employeeJob->department)->department_name ?? 'N/A',
                //     ])
                //     ->unique('id')
                //     ->values();

                $signatures = JobDoc::with('employeeJob')
                    ->whereHas('employeeJob', function($q){
                        $q->where('job_status', 'kontrak');
                    })
                    ->where('type', 'contract')
                    ->where(function($q) {
                        $q->whereNull('first_party_signature')
                          ->orWhere('first_party_signature', '');
                    })
                    ->get();

                $compensations = EmployeeJob::where('employment_status', true)
                ->where('user_dakar_role', 'karyawan')
                ->whereHas('jobdoc', function($q){
                    $q->where('type', 'contract')
                    ->whereNotNull('first_party_signature');
                })->whereDoesntHave('jobdoc', function($q2){
                    $q2->where('type', 'kompensasi');
                })->get();
                

                $birthdays = EmployeeDetail::with(['user.latestEmployeeJob.department'])
                    ->whereMonth('birth_date', Carbon::now()->month)
                    ->whereHas('user.latestEmployeeJob', fn($q) => $q->where('employment_status', true))
                    ->take(5)
                    ->get();

                // Onboarding yang belum selesai
                $uncomplete = User::with(['employeeDetail', 'firstEmployeeJob'])
                    ->whereHas('employeeDetail', function ($q) {
                        $q->where('is_draft', 0);
                    })->whereHas('firstEmployeeJob', function ($q) {
                        $q->where('is_onboarding_completed', false);
                        $q->where('employment_status', true);
                    })->count();
                // dd($uncomplete);
                // ->whereHas('employeeDetail', fn($q) => $q->where('is_draft', 0))
                // ->get()
                // ->filter(fn($u) => $u->progressOnboardingAdmin()['progress'] < 100);

                return view('home', compact(
                    'pemagangan',
                    'internship',
                    'karyawan',
                    'jobType',
                    'departments',
                    'expiredThisMonth',
                    'signatures',
                    'compensations',
                    //'uniformRefresh',
                    'birthdays',
                    'uncomplete'
                ));
            }

            // Karyawan biasa
            $user = User::with([
                'employeeDocs',
                'employeeJob.jobDoc',
                'employeeJob.department',
            ])->findOrFail(Auth::id());
            // dd($user);
            $personal_status = $user->personal_status()['status'];
            $personal_date = $user->personal_status()['date'];
            $job = $user->employeeJob->first();
            $permissionModal   = empty($user->permission_signature);

            $contract_status = false;
            $contract_date = null;
            $spk_status = false;
            $spk_date = null;
            $inventories_status = false;
            $inventories_date = null;

            if ($job) {
                $contractDoc = $job->jobDoc->firstWhere('type', 'contract');
                $contract_status = $contractDoc && $contractDoc->second_party_signature;
                $contract_date = optional($contractDoc)->created_at;

                $spkDoc = $job->jobDoc->firstWhere('type', 'kerahasiaan');
                $spk_status = $spkDoc && $spkDoc->second_party_signature;
                $spk_date = optional($spkDoc)->created_at;

                $inventories_status = $user->inventory_acc_status()['status'];
                $inventories_date = $user->inventory_acc_status()['date'];
            }

            $inumber_status = $user->inumber_status()['status'];
            $inumber_date = $user->inumber_status()['date'];

            return view('home', compact(
                'user',
                'personal_status',
                'personal_date',
                'contract_status',
                'contract_date',
                'spk_status',
                'spk_date',
                'inventories_status',
                'inventories_date',
                'inumber_status',
                'inumber_date',
                'permissionModal'
            ));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function exportSignatures()
    {
        $signatures = JobDoc::with('employeeJob')
            ->whereHas('employeeJob', function($q){
                $q->where('job_status', 'kontrak');
            })
            ->where('type', 'contract')
            ->where(function($q) {
                $q->whereNull('first_party_signature')
                  ->orWhere('first_party_signature', '');
            })
            ->get();

        return Excel::download(new SignaturesExport($signatures), 'signatures_' . now()->format('Y-m-d_His') . '.xlsx');
    }

    public function exportCompensations()
    {
        $compensations = EmployeeJob::where('employment_status', true)
            ->where('user_dakar_role', 'karyawan')
            ->whereHas('jobdoc', function($q){
                $q->where('type', 'contract')
                ->whereNotNull('first_party_signature');
            })->whereDoesntHave('jobdoc', function($q2){
                $q2->where('type', 'kompensasi');
            })->get();

        return Excel::download(new CompensationsExport($compensations), 'compensations_' . now()->format('Y-m-d_His') . '.xlsx');
    }
}
