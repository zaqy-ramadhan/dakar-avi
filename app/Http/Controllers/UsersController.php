<?php

namespace App\Http\Controllers;

use App\DataTables\UserBoardingDataTables;
use App\DataTables\UserDataTables;
use App\DataTables\UserOffboardingDataTables;
use App\Models\CostCenter;
use App\Models\DakarRole;
use App\Models\Department;
use App\Models\Division;
use App\Models\EmployeeBank;
use App\Models\EmployeeDetail;
use App\Models\EmployeeDoc;
use App\Models\EmployeeEducation;
use App\Models\EmployeeFamily;
use App\Models\EmployeeInventoryNumber;
use App\Models\EmployeeJob;
use App\Models\EmployeeSocmed;
use App\Models\EmployeeTraining;
use App\Models\Golongan;
use App\Models\JobStatus;
use App\Models\Section;
use App\Models\SubGolongan;
use App\Models\Group;
use App\Models\InventoryRule;
use App\Models\Item;
use App\Models\JobType;
use App\Models\JobWageAllowance;
use App\Models\Level;
use App\Models\Line;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function index(UserDataTables $dataTable)
    {
        $routeName = request()->route()->getName();
        $roles = DakarRole::all();
        $jobStatus = JobStatus::all();
        $type = request()->route('role');

        $pageTitles = [
            'users.index.onboarding' => 'User Onboarding',
            'users.index.offboarding' => 'User Offboarding',
        ];
        $page = $pageTitles[$routeName] ?? 'User Management';

        $userRole = Auth::user()->getRole();
        $restrictedRoles = ['karyawan', 'pemagangan', 'internship'];

        if (in_array($userRole, $restrictedRoles)) {
            $detailRoutes = [
                'users.index.onboarding' => 'users.index.onboarding.detail',
                'users.index.offboarding' => 'users.index.offboarding.detail',
            ];
            if (isset($detailRoutes[$routeName])) {
                return redirect()->route($detailRoutes[$routeName], ['id' => Auth::id()]);
            }
        }

        return $dataTable->render('admin.users.user', compact('roles', 'type', 'page', 'jobStatus'));
    }

    public function indexBoarding(UserBoardingDataTables $dataTable)
    {
        $routeName = request()->route()->getName();
        // dd($routeName);
        $roles = DakarRole::whereIn('role_name', ['karyawan', 'pemagangan', 'internship'])->get();
        $jobStatus = JobStatus::all();
        $type = request()->route('role');

        $pageTitles = [
            'users.index.onboarding' => 'User Onboarding',
        ];
        $page = $pageTitles[$routeName] ?? 'User Management';

        $userRole = Auth::user()->getRole() ?? 'guest';
        $restrictedRoles = ['karyawan', 'pemagangan', 'internship', 'guest'];

        if (in_array($userRole, $restrictedRoles)) {
            $detailRoutes = [
                'users.index.onboarding' => 'users.index.onboarding.detail',
            ];
            if (isset($detailRoutes[$routeName])) {
                // return redirect()->route($detailRoutes[$routeName], ['id' => Auth::id()]);
                $user = User::with('employeeJob', 'inventory.employeeJob', 'dakarRole', 'firstEmployeeJob', 'latestEmployeeJob')->findOrFail(Auth::user()->id);
                $jobWageAllowance = JobWageAllowance::where('employee_job_id', optional($user->firstEmployeeJob)->id)->get();

                if ($jobWageAllowance === null || $jobWageAllowance->count() <= 0) {
                    if (optional($user->firstEmployeeJob)->user_dakar_role === 'karyawan') {
                        $jobWageAllowance = collect([
                            ['type' => 'Gaji Pokok', 'amount' => '', 'calculation' => 'Per Month', 'status' => 'Gross'],
                            ['type' => 'Tunjangan Transport', 'amount' => '', 'calculation' => 'Per Month', 'status' => 'Gross'],
                            ['type' => 'Tunjangan Makan', 'amount' => '', 'calculation' => 'Per Month', 'status' => 'Gross'],
                            ['type' => 'Tunjangan Kesehatan', 'amount' => '', 'calculation' => 'Per Month', 'status' => 'Gross'],
                        ]);
                    } else {
                        $jobWageAllowance = collect([
                            ['type' => 'Uang Saku', 'amount' => '', 'calculation' => 'Per Month', 'status' => 'Gross'],
                        ]);
                    }
                }

                $personal_status = $user->employeeDetail && $user->employeeEducations && $user->employeeBanks && $user->employeeDocs;
                $personal_date = optional($user->employeeDetail)->created_at;

                $job = $user->employeeJob->first();
                $employment_status = $job && $job->jobDoc->isNotEmpty() && $job->jobWageAllowance->isNotEmpty() && $job->inventory->where('employee_job_id', $job->id)->isNotEmpty();
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

                $inventories = $user->inventory->map(function ($inventory) {
                    return [
                        'id' => $inventory->id,
                        'item_id' => $inventory->item_id,
                        'item_name' => $inventory->item_name,
                        'size' => $inventory->size,
                        'status' => $inventory->status,
                        'due_date' => $inventory->due_date,
                        'acc_date' => $inventory->acc_date,
                        'return_date' => $inventory->return_date,
                        'return_notes' => $inventory->return_notes,
                        'employee_job_id' => $inventory->employee_job_id,
                        'contract' => $inventory->employeeJob ? $inventory->employeeJob->contract : $inventory->user->employeeJob->last()->contract ?? null,
                    ];
                });
                // dd($inventories);

                $rule = null;
                if ($user->dakarRole) {
                    $employeeJob = optional($user->employeeJob)->last();
                    if ($employeeJob) {
                        $ruleQuery = InventoryRule::where('dakar_role_id', $user->getRoleId());
                        if ($employeeJob->department_id) {
                            $ruleQuery->whereHas('department', function ($q) use ($employeeJob) {
                                $q->where('dakar_departments.id', $employeeJob->department_id);
                            });
                        }
                        $rule = $ruleQuery->first();
                    }
                }

                $items = $rule ? $rule->items->map(function ($item) use ($user) {
                    $size = '';
                    if (strpos(strtolower($item->item_name), 'eragam esd') !== false) {
                        $size = $user->employeeDetail->esd_uniform_size ?? 'Default Size';
                    } elseif (strpos(strtolower($item->item_name), 'sepatu esd') !== false) {
                        $size = $user->employeeDetail->esd_shoes_size ?? 'Default Size';
                    } elseif (strpos(strtolower($item->item_name), 'biru') !== false) {
                        $size = $user->employeeDetail->blue_uniform_size ?? 'Default Size';
                    } elseif (strpos(strtolower($item->item_name), 'polo') !== false) {
                        $size = $user->employeeDetail->polo_shirt_size ?? 'Default Size';
                    } elseif (strpos(strtolower($item->item_name), 'safety') !== false) {
                        $size = $user->employeeDetail->safety_shoes_size ?? 'Default Size';
                    } else {
                        $size = '-';
                    }
                    return [
                        'id' => $item->id,
                        'name' => $item->item_name,
                        'size' => $size,
                    ];
                }) : [];

                $costCenters = CostCenter::all();
                $levels = Level::all();
                $types = JobType::all();
                $golongans = Golongan::all();
                $sub_golongans = SubGolongan::all();
                $groups = Group::all();
                $lines = Line::all();
                $jobStatus = JobStatus::all();
                $positions = Position::with(['department.division'])->get();
                $workHour = WorkHour::get();
                $sections = Section::with(['department.division'])->get();
                $departments = Department::with('division')->get();
                $divisions = Division::all();
                $roles = DakarRole::whereIn('role_name', ['karyawan', 'pemagangan', 'internship'])->get();
                $allItems = Item::all();


                $acceptedItems = collect($inventories ?? [])->where('status', 'Diterima');
                $groupedItems = $acceptedItems->groupBy('item_name');

                $bpjsItemId = Item::where('item_name', 'BPJS Kesehatan')->first()->id ?? null;
                $bpjstkItemId = Item::where('item_name', 'BPJS TK')->first()->id ?? null;
                $greatdayItemId = Item::where('item_name', 'User Account Great Day')->first()->id ?? null;
                $eslipItemId = Item::where('item_name', 'User Account E-Slip')->first()->id ?? null;
                $pass_greatdayItemId = Item::where('item_name', 'User Password Great Day')->first()->id ?? null;
                $pass_eslipItemId = Item::where('item_name', 'User Password E-Slip')->first()->id ?? null;
                $lastContractInventory = optional(optional($user->employeeJob->last())->inventory)->isEmpty() ?? true;
                $bpjs = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $bpjsItemId)->first() ?? null;
                $bpjstk = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $bpjstkItemId)->first() ?? null;
                $greatday = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $greatdayItemId)->first() ?? null;
                $eslip = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $eslipItemId)->first() ?? null;
                $pass_greatday = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $pass_greatdayItemId)->first() ?? null;
                $pass_eslip = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $pass_eslipItemId)->first() ?? null;

                return view('admin.onboarding.onboarding', compact(
                    'user',
                    'divisions',
                    'departments',
                    'positions',
                    'sections',
                    'costCenters',
                    'levels',
                    'types',
                    'golongans',
                    'sub_golongans',
                    'groups',
                    'lines',
                    'workHour',
                    'jobStatus',
                    'roles',
                    'items',
                    'allItems',
                    'inventories',
                    'lastContractInventory',
                    'rule',
                    'groupedItems',
                    'bpjs',
                    'bpjstk',
                    'greatday',
                    'eslip',
                    'jobWageAllowance',
                    'pass_greatday',
                    'pass_eslip',
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
        }

        return $dataTable->render('admin.users.user', compact('roles', 'type', 'page', 'jobStatus'));
    }

    public function indexOffboarding(UserOffboardingDataTables $dataTable)
    {
        $roles = DakarRole::whereIn('role_name', ['karyawan', 'pemagangan', 'internship'])->get();
        $jobStatus = JobStatus::all();
        $type = request()->route('role');
        $page = 'User Offboarding';

        $userRole = Auth::user()->getRole() ?? 'guest';
        $restrictedRoles = ['karyawan', 'pemagangan', 'internship', 'guest'];

        if (in_array($userRole, $restrictedRoles)) {
            $user = User::with('employeeJob', 'inventory.employeeJob', 'dakarRole', 'offboarding')->findOrFail(Auth::id());
            // Inventory dan Rule logic
            $inventories = $user->inventory->map(function ($inventory) {
                return [
                    'id' => $inventory->id,
                    'item_id' => $inventory->item_id,
                    'item_name' => $inventory->item_name,
                    'size' => $inventory->size,
                    'status' => $inventory->status,
                    'due_date' => $inventory->due_date,
                    'acc_date' => $inventory->acc_date,
                    'return_date' => $inventory->return_date,
                    'return_notes' => $inventory->return_notes,
                    'employee_job_id' => $inventory->employee_job_id,
                    'contract' => $inventory->employeeJob ? $inventory->employeeJob->contract : $inventory->user->employeeJob->last()->contract ?? null,
                ];
            });

            $rule = null;
            if ($user->dakarRole) {
                $employeeJob = optional($user->employeeJob)->last();
                if ($employeeJob) {
                    $ruleQuery = InventoryRule::where('dakar_role_id', $user->getRoleId());
                    if ($employeeJob->department_id) {
                        $ruleQuery->whereHas('department', function ($q) use ($employeeJob) {
                            $q->where('dakar_departments.id', $employeeJob->department_id);
                        });
                    }
                    // if ($employeeJob->department_id) {
                    //     $ruleQuery->where('department_id', $employeeJob->department_id);
                    // }
                    // if ($employeeJob->level_id) {
                    //     $ruleQuery->Where('level_id', $employeeJob->level_id);
                    // }
                    // if ($employeeJob->job_status) {
                    //     $ruleQuery->Where('job_status', $employeeJob->job_status);
                    // }
                    $rule = $ruleQuery->first();
                }
            }

            $items = $rule ? $rule->items->map(function ($item) use ($user) {
                $size = '';
                if (strpos(strtolower($item->item_name), 'eragam esd') !== false) {
                    $size = $user->employeeDetail->esd_uniform_size ?? 'Default Size';
                } elseif (strpos(strtolower($item->item_name), 'sepatu esd') !== false) {
                    $size = $user->employeeDetail->esd_shoes_size ?? 'Default Size';
                } elseif (strpos(strtolower($item->item_name), 'biru') !== false) {
                    $size = $user->employeeDetail->blue_uniform_size ?? 'Default Size';
                } elseif (strpos(strtolower($item->item_name), 'polo') !== false) {
                    $size = $user->employeeDetail->polo_shirt_size ?? 'Default Size';
                } elseif (strpos(strtolower($item->item_name), 'safety') !== false) {
                    $size = $user->employeeDetail->safety_shoes_size ?? 'Default Size';
                } else {
                    $size = '-';
                }
                return [
                    'id' => $item->id,
                    'name' => $item->item_name,
                    'size' => $size,
                ];
            }) : [];

            $costCenters = CostCenter::all();
            $levels = Level::all();
            $types = JobType::all();
            $golongans = Golongan::all();
            $sub_golongans = SubGolongan::all();
            $groups = Group::all();
            $lines = Line::all();
            $positions = Position::with(['department.division'])->get();
            $workHour = WorkHour::get();
            $sections = Section::with(['department.division'])->get();

            $departments = Department::with('division')->get();
            $divisions = Division::all();
            $allItems = Item::all();

            $lastContractInventory = optional(optional($user->employeeJob->last())->inventory)->isEmpty() ?? true;
            $acceptedItems = collect($inventories)->where('status', 'Diterima');
            $groupedItems = $acceptedItems->groupBy('item_name');

            return view('admin.onboarding.onboarding', compact(
                'user',
                'divisions',
                'departments',
                'positions',
                'sections',
                'costCenters',
                'levels',
                'types',
                'golongans',
                'sub_golongans',
                'groups',
                'lines',
                'workHour',
                'jobStatus',
                'roles',
                'items',
                'allItems',
                'inventories',
                'lastContractInventory',
                'rule',
                'groupedItems',
            ));
        }

        return $dataTable->render('admin.users.user', compact('roles', 'type', 'page', 'jobStatus'));
    }

    public function create()
    {
        $roles = DakarRole::whereNotIn('role_name', ['admin', 'admin 2', 'admin 3'])->get();
        $divisions = Division::all();
        $departments = Department::all();
        $positions = Position::all();
        return view('admin.users.create', compact('roles', 'divisions', 'departments', 'positions'));
    }

    public function assignRole()
    {
        try {
            DB::beginTransaction();
            $users = User::all();
            foreach ($users as $user) {
                $role = $user->getRole() ?? null;
                if ($role === null) {
                    $user->dakarRole()->sync(8);
                }
            }
            DB::commit();
            return response()->json([
                'success' => 'user role assigned'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error assign role: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to assgin karyawan role. Please try again. ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'npk' => 'required|string|unique:users,npk|unique:users,username',
            'role' => 'required|exists:dakar_role,id',
            'password' => 'required|string',
        ]);

        try {
            $user = User::create([
                'npk' => $request->npk,
                'username' => $request->npk,
                'join_date' => now(),
                'password' => $request->password,
                'password_hash' => bcrypt($request->password)
            ]);

            $user->dakarRole()->sync($request->role);

            // $employeeJob = EmployeeJob::create([
            //     'user_id' => $user->id,
            //     'position_id' => $request->position_id
            // ]);

            return redirect()->back()->with('success', 'User created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while creating the User.' . $e)->withInput();
        }
    }

    public function updateDetails(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $employeeDetail = $user->employeeDetail;
        $employeeFamily = $user->employeeFamily;
        $employeeEducation = $user->employeeEducations;
        $employeeTraining = $user->employeeTrainings;
        $employeeBank = $user->employeeBanks->first();
        $employeeDoc = $user->employeeDocs;
        $departments = Department::all();
        $costCenters = CostCenter::all();
        $levels            = Level::all();
        $types            = JobType::all();
        $golongans            = Golongan::all();
        $sub_golongans = SubGolongan::all();
        $groups            = Group::all();
        $lines             = Line::all();
        $jobStatus = JobStatus::all();

        $bpjsItemId = Item::where('item_name', 'BPJS Kesehatan')->first()->id ?? null;
        $bpjstkItemId = Item::where('item_name', 'BPJS TK')->first()->id ?? null;
        $greatdayItemId = Item::where('item_name', 'User Account Great Day')->first()->id ?? null;
        $eslipItemId = Item::where('item_name', 'User Account E-Slip')->first()->id ?? null;
        $pass_greatdayItemId = Item::where('item_name', 'User Password Great Day')->first()->id ?? null;
        $pass_eslipItemId = Item::where('item_name', 'User Password E-Slip')->first()->id ?? null;

        $bpjs = EmployeeInventoryNumber::where('user_id', $id)->where('item_id', $bpjsItemId)->first() ?? null;
        $bpjstk = EmployeeInventoryNumber::where('user_id', $id)->where('item_id', $bpjstkItemId)->first() ?? null;
        $greatday = EmployeeInventoryNumber::where('user_id', $id)->where('item_id', $greatdayItemId)->first() ?? null;
        $eslip = EmployeeInventoryNumber::where('user_id', $id)->where('item_id', $eslipItemId)->first() ?? null;
        $pass_greatday = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $pass_greatdayItemId)->first() ?? null;
        $pass_eslip = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $pass_eslipItemId)->first() ?? null;


        $positions = Position::with(['department.division'])->get();

        return view('admin.users.update', compact('user', 'employeeDetail', 'employeeFamily', 'employeeEducation', 'employeeTraining', 'employeeBank', 'employeeDoc', 'departments', 'positions', 'costCenters', 'levels', 'types', 'golongans', 'sub_golongans', 'groups', 'lines', 'jobStatus', 'bpjs', 'bpjstk', 'greatday', 'eslip', 'pass_greatday', 'pass_eslip'));
    }

    public function details()
    {
        $user = Auth::user();

        $employeeDetail    = $user->employeeDetail;
        $employeeFamily    = $user->employeeFamily;
        $employeeEducation = $user->employeeEducations;
        $employeeTraining  = $user->employeeTrainings;
        $employeeBank      = $user->employeeBanks->first();
        $employeeDoc       = $user->employeeDocs;
        $departments       = Department::all();
        $divisions         = Division::all();
        $costCenters       = CostCenter::all();
        $levels            = Level::all();
        $types             = JobType::all();
        $golongans         = Golongan::all();
        $sub_golongans     = SubGolongan::all();
        $groups            = Group::all();
        $lines             = Line::all();
        $jobStatus         = JobStatus::all();
        $positions         = Position::with(['department.division'])->get();
        $employeeJobs      = $user->employeeJob;

        $bpjsItemId = Item::where('item_name', 'BPJS Kesehatan')->first()->id ?? null;
        $bpjstkItemId = Item::where('item_name', 'BPJS TK')->first()->id ?? null;
        $greatdayItemId = Item::where('item_name', 'User Account Great Day')->first()->id ?? null;
        $eslipItemId = Item::where('item_name', 'User Account E-Slip')->first()->id ?? null;
        $pass_greatdayItemId = Item::where('item_name', 'User Password Great Day')->first()->id ?? null;
        $pass_eslipItemId = Item::where('item_name', 'User Password E-Slip')->first()->id ?? null;

        $bpjs = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $bpjsItemId)->first() ?? null;
        $bpjstk = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $bpjstkItemId)->first() ?? null;
        $greatday = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $greatdayItemId)->first() ?? null;
        $eslip = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $eslipItemId)->first() ?? null;
        $pass_greatday = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $pass_greatdayItemId)->first() ?? null;
        $pass_eslip = EmployeeInventoryNumber::where('user_id', $user->id)->where('item_id', $pass_eslipItemId)->first() ?? null;

        if (
            ($employeeDetail && $employeeDetail->is_draft == 0) &&
            $employeeEducation->isNotEmpty() &&
            $employeeBank &&
            $employeeDoc->isNotEmpty()
        ) {

            return view('admin.users.update', compact(
                'user',
                'employeeDetail',
                'employeeFamily',
                'employeeEducation',
                'employeeTraining',
                'employeeBank',
                'employeeDoc',
                'departments',
                'employeeJobs',
                'positions',
                'costCenters',
                'levels',
                'types',
                'golongans',
                'sub_golongans',
                'groups',
                'lines',
                'jobStatus',
                'bpjs',
                'bpjstk',
                'greatday',
                'eslip',
                'pass_greatday',
                'pass_eslip'
            ));
        }

        return view('admin.users.details', compact(
            'user',
            'employeeDetail',
            'employeeFamily',
            'employeeEducation',
            'employeeTraining',
            'employeeBank',
            'employeeDoc',
            'departments',
            'employeeJobs',
            'positions',
            'costCenters',
            'levels',
            'types',
            'golongans',
            'sub_golongans',
            'groups',
            'lines',
            'jobStatus',
            'bpjs',
            'bpjstk',
            'greatday',
            'eslip',
            'pass_greatday',
            'pass_eslip'
        ));
    }

    public function storeDetails(Request $request, $id)
    {
        // dd($request);
        try {
            $request->validate([
                // Detail
                'npk'               => 'required|exists:users,npk',
                'fullname'          => 'required|string',
                'gender'            => 'required|string|max:255',
                'blood_type'        => 'required|string|in:A,B,AB,O',
                'birth_place'       => 'required|string|max:255',
                'birth_date'        => 'required|date',
                'religion'          => 'required|string|max:255',
                'email'             => 'required|string',
                'no_jamsostek'      => 'nullable|string',
                'no_npwp'           => 'nullable|string',
                'no_ktp'            => 'required|string|max:16',
                'phone_home'        => 'nullable|string',
                'phone_mobile'      => 'required|string',
                'address_ktp'       => 'required|string|max:255',
                'address_current'   => 'required|string|max:255',
                'emergency_contact' => 'required|string|max:255',
                'tax_status'        => 'required|string|max:255',
                'marital_status'    => 'required|string|max:255',
                'married_year'      => 'nullable|numeric',

                'blue_uniform_size'     => 'nullable|string',
                'polo_shirt_size'       => 'nullable|string',
                'safety_shoes_size'     => 'nullable|string',
                'esd_uniform_size'      => 'nullable|string',
                'esd_shoes_size'        => 'nullable|string',

                'facebook'          => 'nullable|string',
                'linkedin'          => 'nullable|string',
                'instagram'         => 'nullable|string',

                // Family
                'spouse_name'        => 'nullable|string',
                'spouse_birth_date'  => 'nullable|date',
                'spouse_education'   => 'nullable|string',
                'spouse_occupation'  => 'nullable|string',

                'father_name'        => Auth::user()->getRole() === 'internship' ? 'nullable|string' : 'required|string',
                'father_birth_date'  => 'nullable|date',
                'father_education'   => 'nullable|string',
                'father_occupation'  => Auth::user()->getRole() === 'internship' ? 'nullable|string' : 'required|string',

                'mother_name'        => Auth::user()->getRole() === 'internship' ? 'nullable|string' : 'required|string',
                'mother_birth_date'  => 'nullable|date',
                'mother_education'   => 'nullable|string',
                'mother_occupation'  => Auth::user()->getRole() === 'internship' ? 'nullable|string' : 'required|string',

                'siblings_name'      => 'nullable|string',
                'siblings_birth_date' => 'nullable|date',
                'siblings_education' => 'nullable|string',
                'siblings_occupation' => 'nullable|string',

                'child_name.*'       => 'nullable|string',
                'child_birth_date.*' => 'nullable|date',
                'child_education.*'  => 'nullable|string',
                'child_occupation.*' => 'nullable|string',

                // Bank
                'bank_name' => 'required|string|max:255',
                'account_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:50',

                // Education
                'education_level.*' => 'required|string',
                'education_institution.*' => 'required|string',
                'education_city.*' => 'required|string',
                'education_major.*' => 'nullable|string',
                'education_gpa.*' => 'nullable|numeric',
                'education_start_year.*' => 'required|numeric',
                'education_end_year.*' => 'required|numeric',

                // Training
                'training_institution.*' => 'nullable|string|max:255',
                'training_year.*' => 'nullable|numeric',
                'training_duration.*' => 'nullable|string|max:50',
                'training_certificate.*' => 'nullable|string|max:255',

                // Docs
                'ktp_file'                     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'npwp_file'                    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'family_card_file'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'resume_file'                  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'photo_file'                   => 'nullable|file|mimes:jpeg|max:2048',
                'bank_file'                    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'diploma_file'                 => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'sim_file'                     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'child_birth_certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'marriage_certificate_file'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'vaccine_certificate_file'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            // Mulai transaksi database
            DB::beginTransaction();

            // Cari user berdasarkan NPK
            $user = User::findOrFail($id);

            $user->email = $request->email;
            $user->fullname = $request->fullname;
            $user->update();

            // Simpan detail karyawan
            $employeeDetail = EmployeeDetail::updateOrCreate([
                'user_id'           => $user->id,
            ], [
                'no_ktp'            => $request->no_ktp,
                'gender'            => $request->gender,
                'blood_type'        => $request->blood_type,
                'birth_place'       => $request->birth_place,
                'birth_date'        => $request->birth_date,
                'religion'          => $request->religion,
                'no_jamsostek'      => $request->no_jamsostek ?? null,
                'no_npwp'           => $request->no_npwp ?? null,
                'no_phone_house'    => $request->phone_home ?? null,
                'no_phone'          => $request->phone_mobile,
                'ktp_address'       => $request->address_ktp,
                'current_address'   => $request->address_current,
                'emergency_contact' => $request->emergency_contact,
                'tax_status'        => $request->tax_status,
                'marital_status'    => $request->marital_status,
                'married_year'      => $request->married_year ?? null,
                'blue_uniform_size' => $request->blue_uniform_size ?? null,
                'polo_shirt_size'       => $request->polo_shirt_size ?? null,
                'safety_shoes_size'     => $request->safety_shoes_size ?? null,
                'esd_uniform_size'      => $request->esd_uniform_size ?? null,
                'esd_shoes_size'        => $request->esd_shoes_size ?? null,
                'is_draft' => 0,
            ]);



        // dd($employeeDetail->is_draft);


            if (!empty($request->facebook)) {
                EmployeeSocmed::updateOrCreate(
                    [
                        'user_id'  => $user->id,
                        'type'     => 'facebook',
                    ],
                    [
                        'account'  => $request->facebook,
                    ]
                );
            }

            if (!empty($request->linkedin)) {
                EmployeeSocmed::updateOrCreate(
                    [
                        'user_id'  => $user->id,
                        'type'     => 'linkedin',
                    ],
                    [
                        'account'  => $request->linkedin,
                    ]
                );
            }

            if (!empty($request->instagram)) {
                EmployeeSocmed::updateOrCreate(
                    [
                        'user_id'  => $user->id,
                        'type'     => 'instagram',
                    ],
                    [
                        'account'  => $request->instagram,
                    ]
                );
            }

            // Simpan data keluarga
            if ($user->getRole() !== 'internship') {
                // Ayah
                if (!empty($request->father_name)) {
                    EmployeeFamily::updateOrCreate(
                        ['user_id' => $user->id, 'type' => 'ayah'],
                        [
                            'name'       => $request->father_name,
                            'birth_date' => $request->father_birth_date,
                            'education'  => $request->father_education,
                            'occupation' => $request->father_occupation,
                        ]
                    );
                }

                // Ibu
                if (!empty($request->mother_name)) {
                    EmployeeFamily::updateOrCreate(
                        ['user_id' => $user->id, 'type' => 'ibu'],
                        [
                            'name'       => $request->mother_name,
                            'birth_date' => $request->mother_birth_date,
                            'education'  => $request->mother_education,
                            'occupation' => $request->mother_occupation,
                        ]
                    );
                }

                // Saudara
                if (!empty($request->siblings_name)) {
                    EmployeeFamily::updateOrCreate(
                        ['user_id' => $user->id, 'type' => 'saudara'],
                        [
                            'name'       => $request->siblings_name,
                            'birth_date' => $request->siblings_birth_date,
                            'education'  => $request->siblings_education,
                            'occupation' => $request->siblings_occupation,
                        ]
                    );
                }

                // Pasangan
                if (!empty($request->spouse_name)) {
                    EmployeeFamily::updateOrCreate(
                        ['user_id' => $user->id, 'type' => 'pasangan'],
                        [
                            'name'       => $request->spouse_name,
                            'birth_date' => $request->spouse_birth_date,
                            'education'  => $request->spouse_education,
                            'occupation' => $request->spouse_occupation,
                        ]
                    );
                }

                // Anak-anak
                if (!empty($request->child_name) && is_array($request->child_name)) {
                    // Hapus data anak sebelumnya agar tidak duplikat
                    EmployeeFamily::where('user_id', $user->id)->where('type', 'child')->delete();
                    foreach ($request->child_name as $key => $name) {
                        if (!empty($name)) {
                            EmployeeFamily::create([
                                'user_id'    => $user->id,
                                'type'       => 'child',
                                'name'       => $name,
                                'birth_date' => $request->child_birth_date[$key] ?? null,
                                'education'  => $request->child_education[$key] ?? null,
                                'occupation' => $request->child_occupation[$key] ?? null,
                            ]);
                        }
                    }
                }
            }

            // Simpan data pendidikan
            foreach ($request->education_level as $index => $level) {
                EmployeeEducation::updateOrCreate([
                    'user_id' => $user->id,
                    'education_level' => $level,
                    'education_institution' => $request->education_institution[$index],
                ], [
                    'education_city' => $request->education_city[$index],
                    'education_major' => $request->education_major[$index] ?? null,
                    'education_gpa' => $request->education_gpa[$index] ?? null,
                    'education_start_year' => $request->education_start_year[$index],
                    'education_end_year' => $request->education_end_year[$index],
                ]);
            }

            // Simpan data bank
            EmployeeBank::updateOrCreate([
                'user_id' => $user->id,
            ], [
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
            ]);

            // Simpan data training
            if (!empty($request->training_institution) && is_array($request->training_institution)) {
                foreach ($request->training_institution as $key => $institution) {
                    if (
                        !empty($institution) && !empty($request->training_year[$key]) &&
                        !empty($request->training_duration[$key]) && !empty($request->training_certificate[$key])
                    ) {
                        EmployeeTraining::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'training_institution' => $institution,
                                'training_year' => $request->training_year[$key],
                            ],
                            [
                                'training_duration' => $request->training_duration[$key],
                                'training_certificate' => $request->training_certificate[$key],
                            ]
                        );
                    }
                }
            }

            $documents = [
                'ktp_file'                     => 'KTP',
                'npwp_file'                    => 'NPWP',
                'family_card_file'             => 'Kartu Keluarga',
                'resume_file'                  => 'Resume',
                'photo_file'                   => 'Pas Foto',
                'vaccine_certificate_file'     => 'Sertifikat Vaksin',
                'diploma_file'                 => 'Ijazah dan Transkrip',
                'sim_file'                     => 'SIM',
                'child_birth_certificate_file' => 'Akte Kelahiran Anak',
                'marriage_certificate_file'    => 'Buku Nikah',
                'bank_file'                    => 'Buku Rekening',
            ];

            // Dokumen yang WAJIB minimal sudah pernah diunggah
            $requiredDocuments = [
                'ktp_file',
                'family_card_file',
                'resume_file',
                'photo_file',
                'bank_file',
            ];

            foreach ($documents as $fieldName => $docType) {
                // Jika user upload file baru
                if ($request->hasFile($fieldName)) {
                    $path = $request->file($fieldName)->store("documents/$docType", 'public');

                    EmployeeDoc::updateOrCreate([
                        'user_id' => $user->id,
                        'doc_type' => $docType,
                    ], [
                        'doc_path' => $path,
                    ]);
                } else {
                    if (in_array($fieldName, $requiredDocuments)) {
                        $existing = EmployeeDoc::where('user_id', $user->id)
                            ->where('doc_type', $docType)
                            ->exists();

                        if (!$existing) {
                            return redirect()->back()->withErrors([
                                $fieldName => "$docType wajib diunggah minimal satu kali.",
                            ])->withInput();
                        }
                    }
                }
            }


            // Commit transaksi jika semua berhasil
            DB::commit();

            // return redirect()->route('users.index')->with('success', 'User detail created successfully');

            return response()->json([
                'message' => 'User detail created successfully',
                'success' => 'User detail created successfully.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error storing user details: ' . $e->getMessage());

            // return redirect()->back()->with('error', 'Failed to create user details. Please try again.');

            return response()->json([
                'message' => 'Failed to create user details. Please try again. ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeUpdateDetails(Request $request, $id)
    {
        try {
            $request->validate([
                // Detail
                'npk'               => 'required|exists:users,npk',
                'fullname'          => 'required|string',
                'gender'            => 'required|string|max:255',
                'blood_type'        => 'required|string|in:A,B,AB,O',
                'birth_place'       => 'required|string|max:255',
                'birth_date'        => 'required|date',
                'religion'          => 'required|string|max:255',
                'email'             => 'required|string',
                'no_jamsostek'      => 'nullable|string|max:255',
                'no_npwp'           => 'nullable|string|max:255',
                'no_ktp'            => 'required|string|max:255',
                'phone_home'        => 'nullable|string',
                'phone_mobile'      => 'required|string',
                'address_ktp'       => 'required|string|max:255',
                'address_current'   => 'required|string|max:255',
                'emergency_contact' => 'required|string|max:255',
                'tax_status'        => 'required|string|max:255',
                'marital_status'    => 'required|string|max:255',
                'married_year'      => 'nullable|numeric',

                'blue_uniform_size'     => 'nullable|string',
                'polo_shirt_size'       => 'nullable|string',
                'safety_shoes_size'     => 'nullable|string',
                'esd_uniform_size'      => 'nullable|string',
                'esd_shoes_size'        => 'nullable|string',

                'facebook'          => 'nullable|string',
                'linkedin'          => 'nullable|string',
                'instagram'         => 'nullable|string',

                // Family
                'spouse_name'           => 'nullable|string',
                'spouse_birth_date'     => 'nullable|date',
                'spouse_education'      => 'nullable|string',
                'spouse_occupation'     => 'nullable|string',

                'father_name'           => 'nullable|string',
                'father_birth_date'     => 'nullable|date',
                'father_education'      => 'nullable|string',
                'father_occupation'     => 'nullable|string',

                'mother_name'           => 'nullable|string',
                'mother_birth_date'     => 'nullable|date',
                'mother_education'      => 'nullable|string',
                'mother_occupation'     => 'nullable|string',

                'siblings_name'         => 'nullable|string',
                'siblings_birth_date'   => 'nullable|date',
                'siblings_education'    => 'nullable|string',
                'siblings_occupation'   => 'nullable|string',

                'child_name.*'          => 'nullable|string',
                'child_birth_date.*'    => 'nullable|date',
                'child_education.*'     => 'nullable|string',
                'child_occupation.*'    => 'nullable|string',

                // Bank
                'bank_name'             => 'required|string|max:255',
                'account_name'          => 'required|string|max:255',
                'account_number'        => 'required|string|max:50',

                // Education
                'education_level.*'         => 'required|string',
                'education_institution.*'   => 'required|string',
                'education_city.*'          => 'required|string',
                'education_major.*'         => 'nullable|string',
                'education_gpa.*'           => 'nullable|numeric',
                'education_start_year.*'    => 'required|numeric',
                'education_end_year.*'      => 'required|numeric',

                // Training
                'training_institution.*'    => 'nullable|string|max:255',
                'training_year.*'           => 'nullable|numeric',
                'training_duration.*'       => 'nullable|string|max:50',
                'training_certificate.*'    => 'nullable|string|max:255',

                // Docs
                'ktp_file'                     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'npwp_file'                    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'family_card_file'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'resume_file'                  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'photo_file'                   => 'nullable|file|mimes:jpeg|max:2048',
                'bank_file'                    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'diploma_file'                 => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'sim_file'                     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'child_birth_certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'marriage_certificate_file'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            DB::beginTransaction();

            $user = User::findOrFail($id);
            $user->update([
                'email'    => $request->email,
                'fullname' => $request->fullname,
            ]);

            // Update Employee Detail
            EmployeeDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'gender'            => $request->gender,
                    'blood_type'        => $request->blood_type,
                    'birth_place'       => $request->birth_place,
                    'birth_date'        => $request->birth_date,
                    'religion'          => $request->religion,
                    'no_jamsostek'      => $request->no_jamsostek ?? null,
                    'no_npwp'           => $request->no_npwp ?? null,
                    'no_ktp'            => $request->no_ktp,
                    'no_phone_house'    => $request->phone_home ?? null,
                    'no_phone'          => $request->phone_mobile,
                    'ktp_address'       => $request->address_ktp,
                    'current_address'   => $request->address_current,
                    'emergency_contact' => $request->emergency_contact,
                    'tax_status'        => $request->tax_status,
                    'marital_status'    => $request->marital_status,
                    'married_year'      => $request->married_year ?? null,
                    'blue_uniform_size' => $request->blue_uniform_size ?? null,
                    'polo_shirt_size'   => $request->polo_shirt_size ?? null,
                    'safety_shoes_size' => $request->safety_shoes_size ?? null,
                    'esd_uniform_size'  => $request->esd_uniform_size ?? null,
                    'esd_shoes_size'    => $request->esd_shoes_size ?? null,
                    'is_draft' => 0,
                ]
            );

            if (!empty($request->facebook)) {
                EmployeeSocmed::updateOrCreate(
                    [
                        'user_id'  => $user->id,
                        'type'     => 'facebook',
                    ],
                    [

                        'account'  => $request->facebook,
                    ]
                );
            }

            if (!empty($request->linkedin)) {
                EmployeeSocmed::updateOrCreate(
                    [
                        'user_id'  => $user->id,
                        'type'     => 'linkedin',
                    ],
                    [
                        'account'  => $request->linkedin,
                    ]
                );
            }

            if (!empty($request->instagram)) {
                EmployeeSocmed::updateOrCreate(
                    [
                        'user_id'  => $user->id,
                        'type'     => 'instagram',
                    ],
                    [
                        'account'  => $request->instagram,
                    ]
                );
            }

            if ($user->getRole() !== 'internship') {
                EmployeeFamily::updateOrCreate(
                    ['user_id' => $user->id, 'type' => 'ayah'],
                    [
                        'name'       => $request->father_name,
                        'birth_date' => $request->father_birth_date,
                        'education'  => $request->father_education,
                        'occupation' => $request->father_occupation,
                    ]
                );

                EmployeeFamily::updateOrCreate(
                    ['user_id' => $user->id, 'type' => 'ibu'],
                    [
                        'name'       => $request->mother_name,
                        'birth_date' => $request->mother_birth_date,
                        'education'  => $request->mother_education,
                        'occupation' => $request->mother_occupation,
                    ]
                );

                if (empty($request->siblings_name)) {
                    EmployeeFamily::where('user_id', $user->id)
                        ->where('type', 'saudara')
                        ->delete();
                } else {
                    EmployeeFamily::updateOrCreate(
                        ['user_id' => $user->id, 'type' => 'saudara'],
                        [
                            'name'       => $request->siblings_name,
                            'birth_date' => $request->siblings_birth_date,
                            'education'  => $request->siblings_education,
                            'occupation' => $request->siblings_occupation,
                        ]
                    );
                }

                if ($request->filled('spouse_name')) {
                    EmployeeFamily::updateOrCreate(
                        ['user_id' => $user->id, 'type' => 'pasangan'],
                        [
                            'name'       => $request->spouse_name,
                            'birth_date' => $request->spouse_birth_date,
                            'education'  => $request->spouse_education,
                            'occupation' => $request->spouse_occupation,
                        ]
                    );
                }

                EmployeeFamily::where('user_id', $user->id)->where('type', 'child')->delete();
                if (!empty($request->child_name) && is_array($request->child_name)) {
                    foreach ($request->child_name as $key => $name) {
                        if (!empty($name)) {
                            EmployeeFamily::create([
                                'user_id'    => $user->id,
                                'type'       => 'child',
                                'name'       => $name,
                                'birth_date' => $request->child_birth_date[$key] ?? null,
                                'education'  => $request->child_education[$key] ?? null,
                                'occupation' => $request->child_occupation[$key] ?? null,
                            ]);
                        }
                    }
                }
            }

            // Update Education - delete old and create new ones
            EmployeeEducation::where('user_id', $user->id)->delete();
            foreach ($request->education_level as $index => $level) {
                EmployeeEducation::create([
                    'user_id'               => $user->id,
                    'education_level'       => $level,
                    'education_institution' => $request->education_institution[$index],
                    'education_city'        => $request->education_city[$index],
                    'education_major'       => $request->education_major[$index] ?? null,
                    'education_gpa'         => $request->education_gpa[$index] ?? null,
                    'education_start_year'  => $request->education_start_year[$index],
                    'education_end_year'    => $request->education_end_year[$index],
                ]);
            }

            // Update Training - delete old and create new ones
            EmployeeTraining::where('user_id', $user->id)->delete();
            if (!empty($request->training_institution) && is_array($request->training_institution)) {
                foreach ($request->training_institution as $key => $institution) {
                    if (
                        !empty($institution) && !empty($request->training_year[$key]) &&
                        !empty($request->training_duration[$key]) && !empty($request->training_certificate[$key])
                    ) {
                        EmployeeTraining::create([
                            'user_id'               => $user->id,
                            'training_institution'  => $institution,
                            'training_year'         => $request->training_year[$key],
                            'training_duration'     => $request->training_duration[$key],
                            'training_certificate'  => $request->training_certificate[$key],
                        ]);
                    }
                }
            }

            // Update Bank - update or create
            EmployeeBank::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'bank_name'      => $request->bank_name,
                    'account_name'   => $request->account_name,
                    'account_number' => $request->account_number,
                ]
            );

            // Update Docs
            $documents = [
                'ktp_file'                     => 'KTP',
                'npwp_file'                    => 'NPWP',
                'family_card_file'             => 'Kartu Keluarga',
                'resume_file'                  => 'Resume',
                'photo_file'                   => 'Pas Foto',
                'vaccine_certificate_file'     => 'Sertifikat Vaksin',
                'diploma_file'                 => 'Ijazah dan Transkrip',
                'sim_file'                     => 'SIM',
                'child_birth_certificate_file' => 'Akte Kelahiran Anak',
                'marriage_certificate_file'    => 'Buku Nikah',
                'bank_file'                    => 'Buku Rekening',
            ];

            foreach ($documents as $fieldName => $docType) {
                if ($request->hasFile($fieldName)) {
                    $path = $request->file($fieldName)->store("documents/$docType", 'public');

                    EmployeeDoc::updateOrCreate(
                        ['user_id' => $user->id, 'doc_type' => $docType],
                        ['doc_path' => $path]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'User detail updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating user details: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update user details. Please try again. ' . $e->getMessage(),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function storeJob(Request $request, $id)
    {
        // dd($request);
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'position_id' => 'nullable|exists:dakar_positions,id',
            'section_id' => 'nullable|exists:dakar_sections,id',
            'department_id' => 'nullable|exists:dakar_departments,id',
            'division_id' => 'nullable|exists:dakar_divisions,id',
            'job_status' => 'required|string|max:255',
            'cost_center_id' => 'nullable|exists:dakar_cost_centers,id',
            'level_id' => 'nullable|exists:role_level,id',
            'job_type_id' => 'nullable|exists:dakar_job_type,id',
            'golongan_id' => 'nullable|exists:dakar_golongan,id',
            'sub_golongan_id' => 'nullable|exists:dakar_sub_golongan,id',
            'group_id' => 'nullable|exists:dakar_group,id',
            'line_id' => 'nullable|exists:dakar_line,id',
            'employment_status' => 'nullable|exists:dakar_role,id',
            'work_hour' => 'nullable|string|max:255|exists:dakar_work_hour_code,id'
        ]);

        // dd($request);
        try {
            DB::beginTransaction();

            $user_role = DakarRole::findOrFail($request->employment_status)->role_name;

            $lastJob = EmployeeJob::where('user_id', $id)->latest()->first();
            if ($lastJob) {
                $lastJob->employment_status = false;
                $lastJob->save();
            }

            EmployeeJob::create([
                'user_id' => $id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date ?? null,
                'position_id' => $request->position_id ?? null,
                'section_id' => $request->section_id ?? null,
                'department_id' => $request->department_id ?? null,
                'division_id' => $request->division_id ?? null,
                'cost_center_id' => $request->cost_center_id ?? null,
                'role_level_id' => $request->level_id ?? null,
                'job_type_id' => $request->job_type_id ?? null,
                'golongan_id' => $request->golongan_id ?? null,
                'sub_golongan_id' => $request->sub_golongan_id ?? null,
                'group_id' => $request->group_id ?? null,
                'line_id' => $request->line_id ?? null,
                'job_status' => $request->job_status ?? null,
                'user_dakar_role' => $user_role ?? null,
                'employment_status' => true,
                'work_hour_code_id' => $request->work_hour
            ]);

            $user = User::findOrFail($id);

            if (isset($request->employment_status)) {
                $user->dakarRole()->sync($request->employment_status);
            }

            DB::commit();

            return redirect()->back()->with('success',  'Job Employment created succesfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating user jobs: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update user jobs. Please try again. ' . $e->getMessage(),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function updateJob(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'position_id' => 'nullable|exists:dakar_positions,id',
            'section_id' => 'nullable|exists:dakar_sections,id',
            'department_id' => 'nullable|exists:dakar_departments,id',
            'division_id' => 'nullable|exists:dakar_divisions,id',
            'job_status' => 'required|string|max:255',
            'cost_center_id' => 'nullable|exists:dakar_cost_centers,id',
            'level_id' => 'nullable|exists:role_level,id',
            'job_type_id' => 'nullable|exists:dakar_job_type,id',
            'golongan_id' => 'nullable|exists:dakar_golongan,id',
            'sub_golongan_id' => 'nullable|exists:dakar_sub_golongan,id',
            'group_id' => 'nullable|exists:dakar_group,id',
            'line_id' => 'nullable|exists:dakar_line,id',
            'employment_status' => 'nullable|exists:dakar_role,id',
            'work_hour' => 'nullable|string|max:255|exists:dakar_work_hour_code,id'
        ]);

        try {
            DB::beginTransaction();

            $job = EmployeeJob::findOrFail($id);
            $user = $job->user;

            $user_role = DakarRole::findOrFail($request->employment_status)->role_name;

            $job->update([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date ?? null,
                'position_id' => $request->position_id ?? null,
                'section_id' => $request->section_id ?? null,
                'department_id' => $request->department_id ?? null,
                'division_id' => $request->division_id ?? null,
                'cost_center_id' => $request->cost_center_id ?? null,
                'role_level_id' => $request->level_id ?? null,
                'job_type_id' => $request->job_type_id ?? null,
                'golongan_id' => $request->golongan_id ?? null,
                'sub_golongan_id' => $request->sub_golongan_id ?? null,
                'group_id' => $request->group_id ?? null,
                'line_id' => $request->line_id ?? null,
                'job_status' => $request->job_status ?? null,
                'user_dakar_role' => $user_role ?? null,
                'work_hour_code_id' => $request->work_hour,
            ]);

            if ($request->employment_status) {
                $user->dakarRole()->sync([$request->employment_status]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Job Employment updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user jobs: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update user jobs. Please try again. ' . $e->getMessage(),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function editJob($id)
    {
        $job = EmployeeJob::with([
            'position',
            'section',
            'department',
            'division',
            'costCenter',
            'level',
            'jobType',
            'golongan',
            'subGolongan',
            'group',
            'line',
            'workHour',
            'user.dakarRole',
        ])->findOrFail($id);

        // Ambil semua data referensi untuk dropdown
        $user = $job->user;
        $positions = Position::all();
        $sections = Section::all();
        $departments = Department::all();
        $divisions = Division::all();
        $costCenters = CostCenter::all();
        $levels = Level::all();
        $golongans = Golongan::all();
        $sub_golongans = SubGolongan::all();
        $groups = Group::all();
        $lines = Line::all();
        $workHour = WorkHour::all();
        $types = JobType::all();
        $jobStatus = JobStatus::all();
        $roles = DakarRole::all();

        return view('admin.users.form.editEmployment', compact(
            'job',
            'positions',
            'sections',
            'departments',
            'divisions',
            'costCenters',
            'levels',
            'types',
            'golongans',
            'sub_golongans',
            'groups',
            'lines',
            'workHour',
            'jobStatus',
            'roles',
            'user',
        ));
    }



    public function edit($id)
    {
        $Users = user::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'address' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // dd($request->has('status'));

        try {
            $user->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'date' => $request->date,
                'address' => $request->address,
                'description' => $request->description,
                'image' => $request->image,
                'status' => $request->has('status')
            ]);

            return redirect()->route('users.index')->with('success', 'user updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while updating the user. ')->withInput();
        }
    }

    public function resign(Request $request, $id)
    {
        $request->validate([
            'resign_date' => 'required|date'
        ]);
        try {
            $employeeJob = EmployeeJob::findOrFail($id);
            $employeeJob->resign_date = $request->resign_date;
            $employeeJob->employment_status = false;
            $employeeJob->update();
            return redirect()->back()->with('success',  'Out date added succesfully');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while changing the resign date. ' . $e->getMessage())->withInput();
        }
    }

    public function changePasswordView()
    {
        return view('admin.users.password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        try {
            $user = Auth::user();

            if (!password_verify($request->old_password, $user->password_hash)) {
                return back()->with('error', 'The old password is incorrect.');
            }

            $user->update([
                'password_hash' => bcrypt($request->new_password),
            ]);

            return back()->with('success', 'Password changed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while changing the password. ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->back()->with('success', 'user deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while deleting the user. ' . $e)->withInput();
        }
    }

    public function destroyJob($id)
    {
        try {
            $job = EmployeeJob::findOrFail($id);
            $job->delete();

            return redirect()->back()->with('success', 'Job Employment deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while deleting the Job Employment. ' . $e)->withInput();
        }
    }

    public function autosavePersonal(Request $request, $id)
    {
        try {
            $user = User::with('employeeDetail')->findOrFail($id);

            $user->update([
                'fullname' => $request->fullname,
                'email'    => $request->email,
            ]);

            EmployeeDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'gender'            => $request->gender,
                    'blood_type'        => $request->blood_type,
                    'birth_place'       => $request->birth_place,
                    'birth_date'        => $request->birth_date,
                    'religion'          => $request->religion,
                    'no_jamsostek'      => $request->no_jamsostek,
                    'no_npwp'           => $request->no_npwp,
                    'no_ktp'            => $request->no_ktp,
                    'no_phone_house'    => $request->phone_home,
                    'no_phone'          => $request->phone_mobile,
                    'ktp_address'       => $request->address_ktp,
                    'current_address'   => $request->address_current,
                    'emergency_contact' => $request->emergency_contact,
                    'tax_status'        => $request->tax_status,
                    'marital_status'    => $request->marital_status,
                    'married_year'      => $request->married_year,
                    'blue_uniform_size' => $request->blue_uniform_size,
                    'polo_shirt_size'   => $request->polo_shirt_size,
                    'safety_shoes_size' => $request->safety_shoes_size,
                    'esd_uniform_size'  => $request->esd_uniform_size,
                    'esd_shoes_size'    => $request->esd_shoes_size,
                    'is_draft'          => $user->employeeDetail->is_draft ?? 1,
                ]   
            );

            return response()->json(['message' => 'Draft saved.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save draft.'], 500);
        }
    }

    public function autosaveFamily(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->getRole() === 'internship') {
            return response()->json(['message' => 'Intern tidak perlu data keluarga.'], 200);
        }

        // Hapus semua data keluarga sebelumnya (autosave akan ganti semua)
        EmployeeFamily::where('user_id', $user->id)->delete();

        $familyData = [];

        // Ayah
        if ($request->filled('father_name')) {
            $familyData[] = [
                'type' => 'ayah',
                'name' => $request->father_name,
                'birth_date' => $request->father_birth_date,
                'education' => $request->father_education,
                'occupation' => $request->father_occupation,
            ];
        }

        // Ibu
        if ($request->filled('mother_name')) {
            $familyData[] = [
                'type' => 'ibu',
                'name' => $request->mother_name,
                'birth_date' => $request->mother_birth_date,
                'education' => $request->mother_education,
                'occupation' => $request->mother_occupation,
            ];
        }

        // Saudara
        if ($request->filled('siblings_name')) {
            $familyData[] = [
                'type' => 'saudara',
                'name' => $request->siblings_name,
                'birth_date' => $request->siblings_birth_date,
                'education' => $request->siblings_education,
                'occupation' => $request->siblings_occupation,
            ];
        }

        // Pasangan
        if ($request->filled('spouse_name')) {
            $familyData[] = [
                'type' => 'pasangan',
                'name' => $request->spouse_name,
                'birth_date' => $request->spouse_birth_date,
                'education' => $request->spouse_education,
                'occupation' => $request->spouse_occupation,
            ];
        }

        foreach ($familyData as $data) {
            EmployeeFamily::create(array_merge(['user_id' => $user->id], $data));
        }

        // Anak-anak
        if (!empty($request->child_name) && is_array($request->child_name)) {
            foreach ($request->child_name as $key => $name) {
                if (!empty($name)) {
                    EmployeeFamily::create([
                        'user_id' => $user->id,
                        'type' => 'child',
                        'name' => $name,
                        'birth_date' => $request->child_birth_date[$key] ?? null,
                        'education' => $request->child_education[$key] ?? null,
                        'occupation' => $request->child_occupation[$key] ?? null,
                    ]);
                }
            }
        }

        // Simpan ulang semua data baru

        return response()->json(['message' => 'Data keluarga berhasil disimpan (autosave).']);
    }

    public function autosaveSocmed(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:facebook,linkedin,instagram',
            'account' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($id);

        EmployeeSocmed::updateOrCreate(
            ['user_id' => $user->id, 'type' => $request->type],
            ['account' => $request->account]
        );

        return response()->json(['status' => 'success']);
    }

    public function autosaveEducation(Request $request, $id)
    {

        $user = User::findOrFail($id);

        foreach ($request->education_level as $index => $level) {
            EmployeeEducation::updateOrCreate([
                'user_id' => $user->id,
                'education_level' => $level,
                'education_start_year' => $request->education_start_year[$index],
                'education_end_year' => $request->education_end_year[$index],
            ], [
                'education_institution' => $request->education_institution[$index],
                'education_city' => $request->education_city[$index],
                'education_major' => $request->education_major[$index] ?? null,
                'education_gpa' => $request->education_gpa[$index] ?? null,
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function autosaveTraining(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!empty($request->training_institution) && is_array($request->training_institution)) {
            foreach ($request->training_institution as $key => $institution) {
                // Pastikan semua field wajib ada sebelum simpan
                if (
                    !empty($institution) &&
                    !empty($request->training_year[$key]) &&
                    !empty($request->training_duration[$key]) &&
                    !empty($request->training_certificate[$key])
                ) {
                    EmployeeTraining::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'training_institution' => $institution,
                            'training_year' => $request->training_year[$key],
                        ],
                        [
                            'training_duration' => $request->training_duration[$key],
                            'training_certificate' => $request->training_certificate[$key],
                        ]
                    );
                }
            }
        }

        return response()->json(['message' => 'Autosave success']);
    }

    public function autosaveBank(Request $request, $id)
    {
        $user = User::findOrFail($id);

        EmployeeBank::updateOrCreate(
            ['user_id' => $user->id],
            [
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
            ]
        );

        return response()->json(['status' => 'success']);
    }

    public function autosaveDocs(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $documents = [
            'ktp_file'                     => 'KTP',
            'npwp_file'                    => 'NPWP',
            'family_card_file'             => 'Kartu Keluarga',
            'resume_file'                  => 'Resume',
            'photo_file'                   => 'Pas Foto',
            'vaccine_certificate_file'     => 'Sertifikat Vaksin',
            'diploma_file'                 => 'Ijazah dan Transkrip',
            'sim_file'                     => 'SIM',
            'child_birth_certificate_file' => 'Akte Kelahiran Anak',
            'marriage_certificate_file'    => 'Buku Nikah',
            'bank_file'                    => 'Buku Rekening',
        ];
        foreach ($documents as $fieldName => $docType) {
            if ($request->hasFile($fieldName)) {
                $path = $request->file($fieldName)->store("documents/$docType", 'public');

                EmployeeDoc::updateOrCreate(
                    ['user_id' => $user->id, 'doc_type' => $docType],
                    ['doc_path' => $path]
                );
            }
        }
        return response()->json(['status' => 'success', 'message' => 'Documents autosaved successfully.']);
    }
}
