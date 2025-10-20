<?php

namespace App\Http\Controllers;

use App\Models\HolidayDate;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class HolidayDateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $HolidayDates = HolidayDate::select(['id', 'date', 'keterangan', 'is_active']);

            return DataTables::of($HolidayDates)
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

        return view('admin.holiday.index');
    }

    public function show($id){
        $HolidayDate = HolidayDate::findOrFail($id);

        return response()->json($HolidayDate);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        HolidayDate::create([
            'date' => $request->date,
            'keterangan' => $request->keterangan,
            'is_active' => $request->is_active ?? 1,
        ]);

        return response()->json(['success' => 'Holiday Date added successfully!']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        $HolidayDate = HolidayDate::findOrFail($id);
        $HolidayDate->update([
            'date' => $request->date,
            'keterangan' => $request->keterangan,
            'is_active' => $request->is_active ?? 1,
        ]);

        return response()->json(['success' => 'Holiday Date updated successfully!']);
    }

    public function destroy($id)
    {
        HolidayDate::findOrFail($id)->delete();

        return response()->json(['success' => 'Holiday Date deleted successfully!']);
    }
}
