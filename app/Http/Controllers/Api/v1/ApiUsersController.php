<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;

class ApiUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = User::whereHas('employeeJob', function ($q) {
                $q->where('employment_status', true);
            })
                ->whereHas('dakarRole', function ($q) {
                    $q->whereNotIn('role_name', ['admin', 'admin 2', 'admin 3', 'admin 4']);
                })
                ->with(['latestEmployeeJob'])
                ->get();

            $data = $users->map(function ($user) {
                $job = $user->latestEmployeeJob;
                return [
                    'id' => $user->id,
                    'npk' => $user->npk,
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                    'position' => $job->position->position_name ?? null,
                    'section' => $job->section->section_name ?? null,
                    'department' => $job->department->department_name ?? null,
                    'division' => $job->division->division_name ?? null,
                    'cost_center' => $job->costCenter->cost_center_name ?? null,
                    'job_type' => $job->jobType->job_type_name ?? null,
                    'golongan' => $job->golongan->golongan_name ?? null,
                    'sub_golongan' => $job->subGolongan->sub_golongan_name ?? null,
                    'group' => $job->group->group_name ?? null,
                    'line' => $job->line->line_name ?? null,
                    'level' => $job->level->level_name ?? null,
                    'work_hour' => $job->workHour->work_hour ?? null,
                    'job_status' => $job->job_status ?? null,
                ];
            });

            return response()->json(
                [
                    'data' => $data,
                    'total' => $data->count(),
                    'message' => 'Employees fetched successfully.'
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch employees.' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with([
                'latestEmployeeJob',
            ])->findOrFail($id);

            $job = $user->latestEmployeeJob;

            $data =  [
                'id' => $user->id,
                'npk' => $user->npk,
                'fullname' => $user->fullname,
                'email' => $user->email,
                'position' => $job->position->position_name ?? null,
                'section' => $job->section->section_name ?? null,
                'department' => $job->department->department_name ?? null,
                'division' => $job->division->division_name ?? null,
                'cost_center' => $job->costCenter->cost_center_name ?? null,
                'job_type' => $job->jobType->job_type_name ?? null,
                'golongan' => $job->golongan->golongan_name ?? null,
                'sub_golongan' => $job->subGolongan->sub_golongan_name ?? null,
                'group' => $job->group->group_name ?? null,
                'line' => $job->line->line_name ?? null,
                'level' => $job->level->level_name ?? null,
                'work_hour' => $job->workHour->work_hour ?? null,
                'job_status' => $job->job_status ?? null,
            ];

            return response()->json(
                [
                    'data' => $data,
                    'message' => 'Employee details fetched successfully.'
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'User not found.' . $e->getMessage()], 404);
        }
    }
}
