<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Division;
use App\Models\Section;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $departments = Section::with(['department' => function ($query) {
                $query->select('id', 'department_name');
            }])->select(['dakar_sections.id', 'dakar_sections.section_name', 'dakar_sections.department_id']);

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

        return view('admin.sections.index', compact('departments'));
    }

    public function show($id)
    {
        $section = Section::with('department')->findOrFail($id);

        return response()->json($section);
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_name' => 'required|unique:dakar_sections,section_name',
            'department_id' => 'nullable|exists:dakar_departments,id'
        ]);

        try {
            Section::create([
                'section_name' => $request->section_name,
                'department_id' => $request->department_id
            ]);

            return response()->json(['success' => 'Section added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add section: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'section_name' => 'required|unique:dakar_sections,section_name,'.$id,
            'department_id' => 'nullable|exists:dakar_departments,id'
        ]);

        try {
            $section = Section::findOrFail($id);

            $section->update([
                'section_name' => $request->section_name,
                'department_id' => $request->department_id
            ]);

            return response()->json(['success' => 'Section updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update section: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Section::findOrFail($id)->delete();

            return response()->json(['success' => 'Section deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete section: ' . $e->getMessage()], 500);
        }
    }
}