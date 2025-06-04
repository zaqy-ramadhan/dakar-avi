<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $departments = Department::with('division')->get();

            $data = $departments->map(function ($department) {
                $manager = User::whereHas('latestEmployeeJob', function ($query) use ($department) {
                    $query->where('department_id', $department->id)
                        ->where('employment_status', true)
                        ->whereHas('level', function ($q) {
                            $q->where('level_name', 'Department Head');
                        });
                })->first();
                return [
                    'id' => $department->id,
                    'name' => $department->department_name,
                    'division' => $department->division->division_name ?? null,
                    'manager' => $manager->fullname ?? null,
                    'manager_id' => $manager->id ?? null,
                ];
            });

            return response()->json(
                [
                    'data' => $data,
                    'total' => $data->count(),
                    'message' => 'Departments fetched successfully.'
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch departments.' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $department = Department::findOrFail($id);

            $manager = User::whereHas('latestEmployeeJob', function ($query) use ($department) {
                $query->where('department_id', $department->id)
                    ->where('employment_status', true)
                    ->whereHas('level', function ($q) {
                        $q->where('level_name', 'Department Head');
                    });
            })->first();

            $data = [
                'id' => $department->id,
                'name' => $department->department_name,
                'division' => $department->division->division_name ?? null,
                'manager' => $manager->fullname ?? null,
                'manager_id' => $manager->id ?? null,
            ];

            return response()->json(
                [
                    'data' => $data,
                    'message' => 'Department details fetched successfully.'
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Department not found.' . $e->getMessage()], 404);
        }
    }
}
