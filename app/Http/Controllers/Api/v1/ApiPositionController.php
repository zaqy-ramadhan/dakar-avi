<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiPositionController extends Controller
{
    /**Position
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $positions = Position::with('department')->get();

            $data = $positions->map(function ($position) {
                $employee = User::whereHas('latestEmployeeJob', function ($query) use ($position) {
                    $query->where('position_id', $position->id)
                        ->where('employment_status', true);
                })->get();

                $employee = $employee->map(function ($emp) {
                    return [
                        'id' => $emp->id,
                        'fullname' => $emp->fullname,
                        'email' => $emp->email,
                    ];
                })->toArray();

                return [
                    'id' => $position->id,
                    'name' => $position->position_name,
                    'department' => $position->department->department_name ?? null,
                    'employee' => $employee ?? null,
                ];
            });

            return response()->json(
                [
                    'data' => $data,
                    'total' => $data->count(),
                    'message' => 'Positions fetched successfully.'
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch positions.' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $position = Position::findOrFail($id);

            $employee = User::whereHas('latestEmployeeJob', function ($query) use ($position) {
                $query->where('position_id', $position->id)
                    ->where('employment_status', true);
            })->get();

            $employee = $employee->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'fullname' => $emp->fullname,
                    'email' => $emp->email,
                ];
            })->toArray();



            $data = [
                'id' => $position->id,
                'name' => $position->position_name,
                'department' => $position->department->department_name ?? null,
                'employee' => $employee ?? null,
            ];

            return response()->json(
                [
                    'data' => $data,
                    'message' => 'Position details fetched successfully.'
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Position not found.' . $e->getMessage()], 404);
        }
    }
}
