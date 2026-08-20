<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Station;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $stations = Station::with(['department' => function ($query) {
                $query->select('id', 'department_name');
            }])->select(['dakar_stations.id', 'dakar_stations.station_name', 'dakar_stations.department_id', 'dakar_stations.is_active']);

            return DataTables::of($stations)
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

        return view('admin.stations.index', compact('departments'));
    }

    public function show($id)
    {
        $station = Station::with('department')->findOrFail($id);

        return response()->json($station);
    }

    public function store(Request $request)
    {
        $request->validate([
            'station_name' => 'required|unique:dakar_stations,station_name',
            'department_id' => 'nullable|exists:dakar_departments,id',
            'is_active' => 'required'
        ]);

        try {
            Station::create([
                'station_name' => $request->station_name,
                'department_id' => $request->department_id,
                'is_active' => $request->is_active
            ]);

            return response()->json(['success' => 'Station added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add station: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'station_name' => 'required|unique:dakar_stations,station_name,'.$id,
            'department_id' => 'nullable|exists:dakar_departments,id',
            'is_active' => 'required'
        ]);

        try {
            $station = Station::findOrFail($id);

            $station->update([
                'station_name' => $request->station_name,
                'department_id' => $request->department_id,
                'is_active' => $request->is_active
            ]);

            return response()->json(['success' => 'Station updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update station: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Station::findOrFail($id)->delete();

            return response()->json(['success' => 'Station deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete station: ' . $e->getMessage()], 500);
        }
    }
}
