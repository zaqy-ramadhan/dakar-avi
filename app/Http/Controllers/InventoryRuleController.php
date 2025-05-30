<?php

namespace App\Http\Controllers;

use App\Models\DakarRole;
use App\Models\Department;
use App\Models\Golongan;
use App\Models\InventoryRule;
use App\Models\item;
use App\Models\JobStatus;
use App\Models\Level;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;

class InventoryRuleController extends Controller
{
    public function index()
    {
        $rules = InventoryRule::with(['dakarRole', 'department', 'items'])->get();
        return view('admin.rule.index', compact('rules'));
    }

    public function create()
    {
        $roles = DakarRole::whereNotIn('role_name', ['admin', 'admin 2', 'admin 3'])->get();
        $departments = Department::all();
        // $levels = Level::all();
        // $jobStatus = JobStatus::all();
        $items = item::all();

        return view('admin.rule.form', compact('roles', 'departments', 'items'));
    }

    public function edit($id)
    {
        $rule = InventoryRule::findOrFail($id);
        $roles = DakarRole::whereNotIn('role_name', ['admin', 'admin 2', 'admin 3'])->get();
        $departments = Department::all();
        // $jobStatus = JobStatus::all();
        // $levels = Level::all();
        $items = item::all();

        return view('admin.rule.edit', compact('rule', 'roles', 'departments', 'items'));
    }

    public function getUserRules($userId)
    {
        $user = User::with('employeeJob')->findOrFail($userId);

        $rule = InventoryRule::where('dakar_role_id', $user->getRoleId())
            ->orWhere('department_id', $user->employeeJob->department_id)
            // ->orWhere('level_id', $user->employeeJob->level_id)
            ->first();

        if (!$rule) {
            return response()->json([
                'role' => null,
                'department' => null,
                // 'level' => null,
                'items' => []
            ]);
        }

        return response()->json([
            // 'role' => $rule->role ? $rule->role->role_name : '-',
            // 'position' => $rule->position ? $rule->position->position_name : '-',
            // 'golongan' => $rule->golongan ? $rule->golongan->golongan_name : '-',
            'items' => $rule->items->map(function ($items) {
                return ['id' => $items->id, 'name' => $items->items_name];
            })
        ]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'dakar_role_id' => 'nullable|exists:dakar_role,id',
            'department_id' => 'required|array',
            // 'level_id' => 'nullable|exists:role_level,id',
            // 'job_status' => 'nullable|exists:dakar_job_status,job_status_name',
            'items' => 'required|array'
        ]);

        // Simpan Inventory Rule baru
        $rule = InventoryRule::create([
            'dakar_role_id' => $request->dakar_role_id,
            // 'department_id' => $request->department_id,
            // 'level_id' => $request->level_id,
            // 'job_status' =>$request->job_status,
        ]);
        $rule->department()->attach($request->department_id);
        $rule->items()->attach($request->items);

        return redirect()->route('inventory-rules.index')->with('success', 'Rule berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'dakar_role_id' => 'nullable|exists:dakar_role,id',
            'department_id' => 'nullable|array',
            // 'level_id' => 'nullable|exists:role_level,id',
            // 'job_status' => 'nullable|exists:dakar_job_status,job_status_name',
            'items' => 'nullable|array'
        ]);

        $rule = InventoryRule::findOrFail($id);
        $rule->update($request->only(['dakar_role_id']));

        $rule->items()->detach();
        foreach ($request->items as $items) {
            $rule->items()->attach($items);
        }

        $rule->department()->detach();
        foreach ($request->department_id as $department) {
            $rule->department()->attach($department);
        }

        return redirect()->route('inventory-rules.index')->with('success', 'Rule berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rule = InventoryRule::findOrFail($id);
        $rule->items()->detach();
        $rule->department()->detach();
        $rule->delete();

        return redirect()->route('inventory-rules.index')->with('success', 'Rule berhasil dihapus.');
    }
}
