<?php

namespace App\Http\Controllers;

use App\DataTables\JobEmploymentDataTables;
use App\Models\CostCenter;
use App\Models\DakarRole;
use App\Models\Department;
use App\Models\Division;
use App\Models\Golongan;
use App\Models\Section;
use App\Models\SubGolongan;
use App\Models\Group;
use App\Models\InventoryRule;
use App\Models\Item;
use App\Models\JobStatus;
use App\Models\JobType;
use App\Models\Level;
use App\Models\Line;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OffboardingController extends Controller
{
    public function index(JobEmploymentDataTables $dataTable, Request $request, $id)
    {
        try {
            $user = User::with('employeeJob.jobDoc', 'inventory.employeeJob', 'dakarRole', 'employeeDetail', 'offboarding')->findOrFail($id);

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

            $previousRole = false;
            if ($user->employeeJob && $user->employeeJob->count() > 1) {
                $previousJob = $user->employeeJob->slice(-2, 1)->first();
                $role = optional($previousJob)->user_dakar_role;
                $previousRole = in_array(strtolower($role), ['pemagangan', 'internship']);
            }

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
            $sections = Section::with(['department.division'])->get();
            $workHour = WorkHour::get();
            $departments = Department::with('division')->get();
            $divisions = Division::all();
            $roles = DakarRole::whereIn('role_name', ['karyawan', 'pemagangan', 'internship'])->get();
            $allItems = Item::all();
            $lastContractInventory = optional(optional($user->employeeJob->last())->inventory)->isEmpty() ?? true;
            $acceptedItems = collect($inventories ?? [])->where('status', 'Diterima');
            $groupedItems = $acceptedItems->groupBy('item_name');

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
                'previousRole',
            ));
        } catch (\Exception $e) {
            // Log error
            Log::error($e->getMessage());
            // Redirect back with error message
            return back()->with('error', 'Terjadi kesalahan saat mengambil data.' . $e->getMessage());
        }
    }

    public function store(Request $request, $id)
    {
        try {
            $request->validate([
                'resign_date' => 'required|date',
                'reason' => 'required|string',
            ]);

            $user = User::findOrFail($id);
            $user->offboarding()->create([
                'resign_date' => $request->resign_date,
                'reason' => $request->reason,
            ]);
            $user->latestEmployeeJob()->update([
                'resign_date' => $request->resign_date,
                'employment_status' => false,
            ]);
            return redirect()->back()->with('success', 'Data offboarding berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'resign_date' => 'required|date',
                'reason' => 'required|string',
            ]);

            $user = User::findOrFail($id);
            $user->offboarding()->update([
                'resign_date' => $request->resign_date,
                'reason' => $request->reason,
            ]);
            $user->latestEmployeeJob()->update([
                'resign_date' => $request->resign_date,
                'employment_status' => false,
            ]);
            return redirect()->back()->with('success', 'Data offboarding berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.' . $e->getMessage());
        }
    }
}
