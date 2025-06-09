<?php

namespace App\Http\Controllers;

use App\Models\DakarRole;
use App\Models\EmployeeDetail;
use App\Models\EmployeeJob;
use App\Models\Inventory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            if (in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3', 'admin 4'])){
                $user = User::query()
                    ->with(['department', 'employeeJob.position'])
                    ->select('users.*');

                $pemaganganRole = DakarRole::where('role_name', 'pemagangan')->first();
                $pemagangan = (clone $user)
                    ->whereHas('dakarRole', function ($q) use ($pemaganganRole) {
                        $q->where('dakar_role_user.dakar_role_id', $pemaganganRole->id);
                    })
                    ->whereHas('latestEmployeeJob', function ($query) {
                        $query->where('employment_status', true);
                    })
                    ->get();

                $internshipRole = DakarRole::where('role_name', 'internship')->first();
                $internship = (clone $user)->whereHas('dakarRole', function ($y) use ($internshipRole) {
                    $y->where('dakar_role_user.dakar_role_id', $internshipRole->id);
                    })
                    ->whereHas('latestEmployeeJob', function ($query) {
                        $query->where('employment_status', true);
                    })
                    ->get();

                $jobType = DB::table('dakar_employee_job as ej')
                    ->join('dakar_job_type as jt', 'ej.job_type_id', '=', 'jt.id')
                    ->where('ej.employment_status', true)
                    ->select('jt.job_type_name', DB::raw('COUNT(*) as total'))
                    ->groupBy('jt.job_type_name')
                    ->pluck('total', 'jt.job_type_name');
                
                    // dd($jobType);

                $departments = DB::table('dakar_departments')
                    ->leftJoin('dakar_employee_job', function ($join) {
                        $join->on('dakar_departments.id', '=', 'dakar_employee_job.department_id')
                            ->where('dakar_employee_job.employment_status', true);
                    })->select('dakar_departments.department_name', DB::raw('COUNT(dakar_employee_job.id) as total'))
                    ->groupBy('dakar_departments.department_name')
                    ->pluck('total', 'dakar_departments.department_name');

                $now = Carbon::now()->addMonth(2);
                $expiredThisMonth = EmployeeJob::with(['department', 'user'])
                    ->where('employment_status', true)
                    ->where('job_status', 'kontrak')
                    ->where('user_dakar_role', 'karyawan')
                    ->whereMonth('end_date', $now->month)
                    ->whereYear('end_date', $now->year)
                    ->get();
                $expiredThisMonth = $expiredThisMonth->take(5);

                $uniformRefresh = Inventory::with(['user', 'item', 'employeeJob.department'])
                    ->whereHas('item', function ($query) {
                        $query->where('type', 'baju');
                    })
                    ->whereHas('employeeJob', function($query){
                        $query->where('employment_status', true);
                    })
                    ->where('acc_date', '<=', Carbon::now()->subMonths(12))
                    ->where('status', 'Diterima')
                    ->get()
                    ->map(function ($inventory) {
                        $user = $inventory->user;

                        return [
                            'id' => $inventory->user->id,
                            'npk' => $user?->npk ?? 'N/A',
                            'name' => $user?->fullname ?? 'N/A',
                            'department' => $inventory->employeeJob->department->department_name ?? 'N/A',
                            // 'acc_date' => $inventory->acc_date,
                            // 'status' => $inventory->status,
                            // 'item_name' => $inventory->item?->name ?? 'N/A',
                        ];
                    })
                    ->unique('user_id')
                    ->values();
                // dd($uniformRefresh);

                $birthdays = EmployeeDetail::with(['user.latestEmployeeJob.department'])
                    ->whereMonth('birth_date', (int) Carbon::now()->month)
                    ->whereHas('user.latestEmployeeJob', function ($query) {
                        $query->where('employment_status', true);
                    })
                    ->get()
                    ->take(5);
                // dd($birthdays);

                $karyawanRole = DakarRole::where('role_name', 'karyawan')->first();
                if ($karyawanRole) {
                    $karyawan = (clone $user)->whereHas('dakarRole', function ($z) use ($karyawanRole) {
                        $z->where('dakar_role_user.dakar_role_id', $karyawanRole->id);
                    })
                    ->whereHas('latestEmployeeJob', function ($query) {
                        $query->where('employment_status', true);
                    })
                    ->get();
                } else {
                    $karyawan = collect();
                }

                $uncomplete = User::whereHas('firstEmployeeJob', function ($query) {
                    $query
                        ->where('is_onboarding_completed', false)
                        ->where('employment_status', true)
                        ->whereRaw('id = (SELECT MIN(id) FROM dakar_employee_job WHERE user_id = users.id)')
                    ;
                })->get();

                return view('home', compact('pemagangan', 'uncomplete', 'internship', 'karyawan', 'jobType', 'departments', 'expiredThisMonth', 'uniformRefresh', 'birthdays'));
            } else {
                $user = User::with(['employeeDocs', 'employeeJob.department'])->find(Auth::user()->id);

                $personal_status = $user->employeeDetail && $user->employeeEducations && $user->employeeBanks && $user->employeeDocs;
                $personal_date = optional($user->employeeDetail)->created_at;

                $job = $user->employeeJob->first();
                $employment_status = $job && $job->jobDoc->isNotEmpty() && $job->jobWageAllowance->isNotEmpty() && $job->inventory->where('employee_job_id', $job->id);
                $employment_date = optional($job?->inventory)->where('employee_job_id', $job?->id)?->last()?->created_at;

                $specificItems = ['bpjs kesehatan', 'bpjs tk', 'user account great day', 'user account e-slip'];
                $inventories_status = false;
                if ($job && $job->inventory->isNotEmpty()) {
                    // dd($job->inventory);
                    $nonSpecificInventories = $job->inventory->filter(function ($item) use ($specificItems) {
                        // dd($item->item->item_name);
                        return !in_array(strtolower($item->item->item_name), $specificItems);
                    });
                    // dd($nonSpecificInventories);
                    $inventories_status = $nonSpecificInventories->where('status', '-')->isEmpty();
                }
                // dd($nonSpecificInventories);
                $inventories_date = optional($job?->inventory)->where('employee_job_id', $job?->id)?->last()?->updated_at ?? null;

                $inumber_status = (bool) $user->employeeInventoryNumber->isNotEmpty();
                // dd($user->employeeInventoryNumber->isNotEmpty());
                $inumber_date = optional($user->employeeInventoryNumber)->last()?->created_at;

                return view('home', compact(
                    'user',
                    'personal_status',
                    'personal_date',
                    'employment_status',
                    'employment_date',
                    'inventories_status',
                    'inventories_date',
                    'inumber_status',
                    'inumber_date',
                ));
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
