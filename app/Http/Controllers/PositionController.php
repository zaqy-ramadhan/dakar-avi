<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $departments = Position::with(['department' => function ($query) {
                $query->select('id', 'department_name');
            }])->select(['dakar_positions.id', 'dakar_positions.position_name', 'dakar_positions.department_id']);

            return DataTables::of($departments)
                ->addIndexColumn() 
                ->addColumn('department_name', function ($row) {
                    return $row->department ? $row->department->department_name : '-';
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

        $departments = Department::all();

        return view('admin.positions.index', compact('departments'));
    }

    public function show($id)
    {
        $position = Position::with('department')->findOrFail($id);

        return response()->json($position);
    }

    public function store(Request $request)
    {
        $request->validate([
            'position_name' => 'required|unique:dakar_positions,position_name',
            'department_id' => 'nullable|exists:dakar_departments,id'
        ]);

        try {
            Position::create([
                'position_name' => $request->position_name,
                'department_id' => $request->department_id
            ]);

            return response()->json(['success' => 'Position added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add position: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'position_name' => 'required|unique:dakar_positions,position_name,'.$id,
            'department_id' => 'nullable|exists:dakar_departments,id'
        ]);

        try {
            $position = Position::findOrFail($id);

            $position->update([
                'position_name' => $request->position_name,
                'department_id' => $request->department_id
            ]);

            return response()->json(['success' => 'Position updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update position: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Position::findOrFail($id)->delete();

            return response()->json(['success' => 'Position deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete position: ' . $e->getMessage()], 500);
        }
    }
}