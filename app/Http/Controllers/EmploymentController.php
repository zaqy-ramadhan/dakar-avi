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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmploymentController extends Controller
{
    public function index(JobEmploymentDataTables $dataTable, Request $request, $id = null)
    {
        try {
            $id = $id ?? Auth::id();

            $user = User::with('employeeJob.jobDoc', 'inventory.employeeJob', 'dakarRole', 'employeeDetail', 'firstEmployeeJob')->findOrFail($id);

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
            })->sortBy('item_id')->values();
            // dd($inventories);

            $rule = $user->rule();
            $items = $user->items();

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
            $allItems = Item::whereNotIn('item_name', ['User Password Great Day', 'User Password E-Slip'])->get();
            // $lastContractInventory = optional(optional($user->employeeJob->last())->inventory)->isEmpty() ?? true;
            $previousRole = false;
            if ($user->employeeJob && $user->employeeJob->count() > 1) {
                $previousJob = $user->employeeJob->slice(-2, 1)->first();
                $role = optional($previousJob)->user_dakar_role;
                $previousRole = in_array(strtolower($role), ['pemagangan', 'internship']);
            }
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
                // 'lastContractInventory',
                'previousRole',
                'rule',
                'groupedItems',
                'bpjs',
                'bpjstk',
                'greatday',
                'eslip',
                'pass_greatday',
                'pass_eslip',
            ));
        } catch (\Exception $e) {
            // Log error
            Log::error($e->getMessage());
            // Redirect back with error message
            return back()->with('error', 'Terjadi kesalahan saat mengambil data.' . $e->getMessage());
        }
    }
}
