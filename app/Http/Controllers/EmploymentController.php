<?php

namespace App\Http\Controllers;

use App\DataTables\JobEmploymentDataTables;
use App\Models\{
    EmployeeJob,
    CostCenter,
    DakarRole,
    Department,
    Division,
    EmployeeInventoryNumber,
    Golongan,
    Section,
    SubGolongan,
    Group,
    Item,
    JobStatus,
    JobType,
    Level,
    Line,
    Position,
    User,
    WorkHour
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmploymentController extends Controller
{
    public function index(JobEmploymentDataTables $dataTable, Request $request, $id = null)
    {
        try {
            $id = $id ?? Auth::id();

            $user = User::with([
                'employeeJob.jobDoc',
                'inventory.employeeJob', // hanya relasi, JANGAN panggil field
                'dakarRole',
                'employeeDetail',
                'firstEmployeeJob'
            ])->findOrFail($id);

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
                    // Ambil field contract dari relasi employeeJob
                    'contract' => optional($inventory->employeeJob)->contract
                ];
            })->sortBy('item_id')->values();
            // dd($inventories);

            $masters = [
                'costCenters'    => CostCenter::all(),
                'levels'         => Level::all(),
                'types'          => JobType::all(),
                'golongans'      => Golongan::all(),
                'sub_golongans'  => SubGolongan::all(),
                'groups'         => Group::all(),
                'lines'          => Line::all(),
                'jobStatus'      => JobStatus::all(),
                'positions'      => Position::with(['department.division'])->get(),
                'sections'       => Section::with(['department.division'])->get(),
                'workHour'       => WorkHour::all(),
                'departments'    => Department::with('division')->get(),
                'divisions'      => Division::all(),
                'roles'          => DakarRole::whereIn('role_name', ['karyawan', 'pemagangan', 'internship'])->get(),
                'allItems'       => Item::whereNotIn('item_name', ['User Password Great Day', 'User Password E-Slip'])->get(),
            ];

            $rule  = $user->rule();
            $items = $user->items();

            $previousRole = false;
            if ($user->employeeJob && $user->employeeJob->count() > 1) {
                $previousJob = $user->employeeJob->slice(-2, 1)->first();
                $role = optional($previousJob)->user_dakar_role;
                $previousRole = in_array(strtolower($role), ['pemagangan', 'internship']);
            }

            $groupedItems = $inventories->where('status', 'Diterima')->groupBy('item_name');

            $itemNames = [
                'BPJS Kesehatan',
                'BPJS TK',
                'User Account Great Day',
                'User Account E-Slip',
                'User Password Great Day',
                'User Password E-Slip',
                'Email AVI',
                'Email Visteon'
            ];
            $itemMap = Item::whereIn('item_name', $itemNames)->pluck('id', 'item_name');

            $inventoryNumbers = EmployeeInventoryNumber::where('user_id', $user->id)
                ->whereIn('item_id', $itemMap->values())
                ->get()
                ->keyBy('item_id');

            $bpjs = $inventoryNumbers[$itemMap['BPJS Kesehatan']] ?? null;
            $bpjstk = $inventoryNumbers[$itemMap['BPJS TK']] ?? null;
            $greatday = $inventoryNumbers[$itemMap['User Account Great Day']] ?? null;
            $eslip = $inventoryNumbers[$itemMap['User Account E-Slip']] ?? null;
            $pass_greatday = $inventoryNumbers[$itemMap['User Password Great Day']] ?? null;
            $pass_eslip = $inventoryNumbers[$itemMap['User Password E-Slip']] ?? null;
            $mail_avi = $inventoryNumbers[$itemMap['Email AVI']] ?? null;
            $mail_visteon = $inventoryNumbers[$itemMap['Email Visteon']] ?? null;

            return $dataTable->render('admin.onboarding.onboarding', array_merge(
                compact(
                    'user',
                    'inventories',
                    'rule',
                    'items',
                    'previousRole',
                    'groupedItems',
                    'bpjs',
                    'bpjstk',
                    'greatday',
                    'eslip',
                    'pass_greatday',
                    'pass_eslip',
                    'mail_avi',
                    'mail_visteon',
                ),
                $masters
            ));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function completeOnboarding($id) 
    { 
        try { 
            $job = EmployeeJob::find($id);

            if (!$job) {
                return ['success' => false, 'message' => 'Employee job not found.'];
            }

            $job->update([
                'is_onboarding_completed' => true,
                'onboarding_progress'     => 100
            ]);

            return ['success' => true, 'message' => 'Onboarding successfully completed.'];

        } catch (\Exception $e) { 
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        } 
    }
}
