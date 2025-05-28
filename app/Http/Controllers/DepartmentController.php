<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Division;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $departments = Department::with(['division' => function ($query) {
                $query->select('id', 'division_name');
            }])->select('dakar_departments.id', 'dakar_departments.department_name', 'dakar_departments.division_id');

            return DataTables::of($departments)
                ->addIndexColumn() 
                ->addColumn('division_name', function ($row) {
                    return $row->division ? $row->division->division_name : '-';
                })
                ->addColumn('actions', function ($row) {
                    return '
                        <button class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal" data-id="'.$row->id.'">
                            <i class="ti ti-edit fs-4"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="modal" data-id="'.$row->id.'">
                            <i class="ti ti-trash fs-4"></i>
                        </button>
                    ';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        $divisions = Division::all();

        return view('admin.departments.index', compact('divisions'));
    }

    public function show($id)
    {
        $department = Department::with('division')->findOrFail($id);

        return response()->json($department);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'department_name' => 'required|unique:dakar_departments,department_name',
            'division_id' => 'nullable|exists:dakar_divisions,id'
        ]);

        try {
            Department::create([
                'department_name' => $request->department_name,
                'division_id' => $request->division_id
            ]);

            return response()->json(['success' => 'Department added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add department: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'department_name' => 'required|unique:dakar_departments,department_name,'.$id,
            'division_id' => 'nullable|exists:dakar_divisions,id'
        ]);

        try {
            $department = Department::findOrFail($id);

            $department->update([
                'department_name' => $request->department_name,
                'division_id' => $request->division_id
            ]);

            return response()->json(['success' => 'Department updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update department: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Department::findOrFail($id)->delete();

            return response()->json(['success' => 'Department deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete department: ' . $e->getMessage()], 500);
        }
    }
}