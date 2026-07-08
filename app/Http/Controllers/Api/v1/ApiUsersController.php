<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $authHeader = $request->header('Authorization');

            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                return response()->json(['error' => 'Unauthorized. API key missing or invalid.'], 401);
            }

            $apiKey = str_replace('Bearer ', '', $authHeader);

             $isValid = env('API_KEY') === $apiKey;

            if (!$isValid) {
                return response()->json(['error' => 'Unauthorized. Invalid API key.'], 401);
            }

            $id_email_avi = Item::where('item_name', 'Email AVI')->first()?->id;

            $query = User::query();

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('npk', 'like', "%{$search}%")
                    ->orWhere('fullname', 'like', "%{$search}%");
                });
            }

            $showAll = $request->boolean('showAll', false);
            $filter = $request->filter ?? null;

            $users = $query
                ->whereHas('employeeJob', function ($q) use ($showAll,$filter) {
                    if (!$showAll) {
                        $q->where('employment_status', true);
                    }

                    if($filter){
                        $q->where('user_dakar_role', strtolower($filter));
                    }
                })
                ->whereHas('dakarRole', function ($q) {
                    $q->whereNotIn('role_name', ['admin', 'admin 2', 'admin 3', 'admin 4']);
                })
                ->with(['latestEmployeeJob', 'firstEmployeeJob','employeeInventoryNumber'])
                ->get();

            $data = $users->map(function ($user) use ($id_email_avi) {
                $job = $user->latestEmployeeJob;
                $email_avi = $user->employeeInventoryNumber->filter(function($q)use($id_email_avi){
                    return $q->item_id == $id_email_avi;
                })->first();
                return [
                    // 'ytes' => $email_avi,
                    'id' => $user->id,
                    'npk' => $user->npk,
                    'fullname' => $user->fullname,
                    'email_avi' => $email_avi ? $email_avi->number : null,
                    'email'=> $user->email ?? null,
                    'position_id' => $job->position->id ?? null,
                    'position' => $job->position->position_name ?? null,
                    'section_id' => $job->section->id ?? null,
                    'section' => $job->section->section_name ?? null,
                    'department_id' => $job->department->id ?? null,
                    'department' => $job->department->department_name ?? null,
                    'division_id' => $job->division->id ?? null,
                    'division' => $job->division->division_name ?? null,
                    'cost_center_id' => $job->costCenter->id ?? null,
                    'cost_center' => $job->costCenter->cost_center_name ?? null,
                    'job_type' => $job->jobType->job_type_name ?? null,
                    'golongan' => $job->golongan->golongan_name ?? null,
                    'sub_golongan' => $job->subGolongan->sub_golongan_name ?? null,
                    'group' => $job->group->group_name ?? null,
                    'line_id' => $job->line->id ?? null,
                    'line' => $job->line->line_name ?? null,
                    'level' => $job->level->level_name ?? null,
                    'work_hour' => $job->workHour->work_hour ?? null,
                    'job_status' => $job->job_status ?? null,
                    'job_role' => $job->user_dakar_role ?? null,
                    'join_date' => $user->join_date ? \Carbon\Carbon::parse($user->join_date)->format('Y-m-d') : ($user->firstEmployeeJob->start_date ? $user->firstEmployeeJob->start_date->format('Y-m-d') : null),
                    'start_date' => $job->start_date ? $job->start_date->format('Y-m-d') : null,
                    'end_date' => $job->end_date ? $job->end_date->format('Y-m-d') : null,
                    'contract' => $job->contract ?? null,
                    'gender' => $user->employeeDetail?->gender === null ? null : ($user->employeeDetail->gender === '0' ? 'Laki-laki' : 'Perempuan'),
                    'age' => $user->employeeDetail?->birth_date ? \Carbon\Carbon::parse($user->employeeDetail->birth_date)->age : null,
                    'no_telp' => $user->employeeDetail?->no_phone ?? null,
                    'employment_status' => (bool)$job->employment_status
                ];
            });

            return response()->json(
                [
                    'total' => $data->count(),
                    'data' => $data,
                    'message' => 'Employees fetched successfully.'
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch employees.' . $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $id)
    {
        #$id = NPK
        try {

            $authHeader = $request->header('Authorization');

            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                return response()->json(['error' => 'Unauthorized. API key missing or invalid.'], 401);
            }

            $apiKey = str_replace('Bearer ', '', $authHeader);

             $isValid = env('API_KEY') === $apiKey;

            if (!$isValid) {
                return response()->json(['error' => 'Unauthorized. Invalid API key.'], 401);
            }

            $user = User::with([
                'latestEmployeeJob',
                'firstEmployeeJob',
                'employeeInventoryNumber',
                'employeeDetail'
            ])->where('npk', $id)->firstOrFail();

            $id_email_avi = Item::where('item_name', 'Email AVI')->first()?->id;

            $job = $user->latestEmployeeJob;
            $email_avi = $user->employeeInventoryNumber->filter(function($q)use($id_email_avi){
                return $q->item_id == $id_email_avi;
            })->first();

            $data =  [
                'id' => $user->id,
                'npk' => $user->npk,
                'fullname' => $user->fullname,
                'email' => $user->email,
                'email_avi' => $email_avi ? $email_avi->number : null,
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
                'join_date' => $user->join_date ? \Carbon\Carbon::parse($user->join_date)->format('Y-m-d') : ($user->firstEmployeeJob->start_date ? $user->firstEmployeeJob->start_date->format('Y-m-d') : null),
                'start_date' => $job->start_date ? $job->start_date->format('Y-m-d') : null,
                'end_date' => $job->end_date ? $job->end_date->format('Y-m-d') : null,
                'contract' => $job->contract ?? null,
                'no_telp' => $user->employeeDetail?->no_phone ?? null,
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
