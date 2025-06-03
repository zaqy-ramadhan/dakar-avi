<?php

namespace App\Http\Controllers;

use App\DataTables\JobEmploymentDataTables;
use App\Models\CostCenter;
use App\Models\DakarRole;
use App\Models\Department;
use App\Models\Division;
use App\Models\EmployeeInventoryNumber;
use App\Models\Golongan;
use App\Models\Section;
use App\Models\SubGolongan;
use App\Models\Group;
use App\Models\InventoryRule;
use App\Models\Item;
use App\Models\JobStatus;
use App\Models\JobType;
use App\Models\JobWageAllowance;
use App\Models\Level;
use App\Models\Line;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    public function index(JobEmploymentDataTables $dataTable, Request $request, $id)
    {
        try {
            $user = User::with('employeeJob.jobDoc', 'dakarRole', 'employeeDetail', 'employeeDocs', 'firstEmployeeJob', 'employeeJob.inventory.item', 'inventory.employeeJob')->findOrFail($id);
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
                // $jobWageAllowance = collect([
                //     ['type' => 'Gaji Pokok', 'amount' => '', 'calculation' => 'Per Month', 'status' => 'Gross'],
                //     ['type' => 'Tunjangan Transport', 'amount' => '', 'calculation' => 'Per Month', 'status' => 'Gross'],
                //     ['type' => 'Tunjangan Makan', 'amount' => '', 'calculation' => 'Per Month', 'status' => 'Gross'],
                // ]);
            }

            //progress

            $personal_status = ($user->employeeDetail && $user->employeeDetail->is_draft == 0) && $user->employeeEducations && $user->employeeBanks && $user->employeeDocs;
            $personal_date = optional($user->employeeDocs)->last()->created_at;
            // dd($personal_date);

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
            })->sortBy('due_date')->values();
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
                    // if ($employeeJob->department_id) {
                    //     $ruleQuery->where('department_id', $employeeJob->department_id);
                    // }
                    // if ($employeeJob->role_level_id) {
                    //     $ruleQuery->Where('level_id', $employeeJob->role_level_id);
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
            $jobStatus = JobStatus::all();
            $positions = Position::with(['department.division'])->get();
            $sections = Section::with(['department.division'])->get();
            $workHour = WorkHour::get();
            $departments = Department::with('division')->get();
            $divisions = Division::all();
            $roles = DakarRole::whereIn('role_name', ['karyawan', 'pemagangan', 'internship'])->get();
            $allItems = Item::all();
            $lastContractInventory = optional(optional($user->employeeJob->last())->inventory)->isEmpty() ?? true;
            $acceptedItems = collect($inventories ?? [])->where('status', 'Diterima');
            $groupedItems = $acceptedItems->groupBy('item_name');

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
            $pass_greatday = EmployeeInventoryNumber::where('user_id', $id)->where('item_id', $pass_greatdayItemId)->first() ?? null;
            $pass_eslip = EmployeeInventoryNumber::where('user_id', $id)->where('item_id', $pass_eslipItemId)->first() ?? null;


            return $dataTable->render('admin.onboarding.onboarding', compact(
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
                'pass_greatday',
                'pass_eslip',
                'jobWageAllowance',
                'personal_status',
                'personal_date',
                'employment_status',
                'employment_date',
                'inventories_status',
                'inventories_date',
                'inumber_status',
                'inumber_date',
            ));
        } catch (\Exception $e) {
            // Log error
            Log::error($e->getMessage());
            // Redirect back with error message
            return back()->with('error', 'Terjadi kesalahan saat mengambil data.' . $e->getMessage());
        }
    }
}
