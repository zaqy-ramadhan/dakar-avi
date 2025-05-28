<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $divisions = Division::select(['id', 'division_name']);

            return DataTables::of($divisions)
                ->addIndexColumn()
                ->addColumn('actions', function ($row) {
                    return '
                        <button class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal" data-id="'.$row->id.'"><i class="ti ti-edit fs-4"></i></button>
                        <button class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="modal" data-id="'.$row->id.'"><i class="ti ti-trash fs-4"></i></button>
                    ';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.divisions.index');
    }

    public function show($id){
        $division = Division::findOrFail($id);

        return response()->json($division);
    }

    public function store(Request $request)
    {
        $request->validate([
            'division_name' => 'required|unique:dakar_divisions,division_name'
        ]);

        Division::create(['division_name' => $request->division_name]);

        return response()->json(['success' => 'Division added successfully!']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'division_name' => 'required|unique:dakar_divisions,division_name,'.$id
        ]);

        $division = Division::findOrFail($id);
        $division->update(['division_name' => $request->division_name]);

        return response()->json(['success' => 'Division updated successfully!']);
    }

    public function destroy($id)
    {
        Division::findOrFail($id)->delete();

        return response()->json(['success' => 'Division deleted successfully!']);
    }
}
