<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobWageAllowance;
use Illuminate\Support\Facades\Auth;

class JobWageAllowanceController extends Controller
{
    public function index($jobEmploymentId)
    {
        $employeeJob = \App\Models\EmployeeJob::find($jobEmploymentId);

        if (!$employeeJob) {
            abort(404, 'Employee Job not found');
        }

        $subGolongan = $employeeJob->subGolongan->sub_golongan_name ?? '';
        $userRole = Auth::user()->getRole();

        // Access control logic
        if($employeeJob->user_id != Auth::user()->id){
        if (in_array($subGolongan, ['4 A'])) {
            if (!in_array($userRole, ['admin', 'admin 2'])) {
                abort(403, 'Unauthorized');
            }
        } elseif (in_array($subGolongan, [
            '4 B', '4 C', '4 D', '4 E', '4 F', '5 A', '5 B', '5 C', '5 D',
            '6 A', '6 B', '6 C', '6 D'
        ])) {
            if ($userRole !== 'admin') {
                abort(403, 'Unauthorized');
            }
        } else {
            if (!in_array($userRole, ['admin', 'admin 1', 'admin 2'])) {
                abort(403, 'Unauthorized');
            }
        }
    }

        $jobWageAllowances = JobWageAllowance::where('employee_job_id', $jobEmploymentId)->get();

        if ($jobWageAllowances === null || $jobWageAllowances->count() <= 0) {
            $jobWageAllowances = collect([
                ['type' => 'Gaji Pokok', 'amount' => '', 'calculation' => 'Per Month', 'status' => 'Gross'],
                ['type' => 'Tunjangan Transport', 'amount' => '', 'calculation' => 'Per Month', 'status' => 'Gross'],
                ['type' => 'Tunjangan Makan', 'amount' => '', 'calculation' => 'Per Month', 'status' => 'Gross'],
            ]);
        }

        return view('admin.onboarding.jobWage', [
            'jobWageAllowance' => $jobWageAllowances,
            'jobEmploymentId' => $jobEmploymentId
        ]);
    }

    public function store(Request $request, $jobEmploymentId)
    {
        $employeeJob = \App\Models\EmployeeJob::find($jobEmploymentId);

        if (!$employeeJob) {
            abort(404, 'Employee Job not found');
        }

        $subGolongan = $employeeJob->subGolongan->sub_golongan_name ?? '';
        $userRole = Auth::user()->getRole();

        if (in_array($subGolongan, ['4 A'])) {
            if (!in_array($userRole, ['admin', 'admin 2'])) {
                abort(403, 'Unauthorized');
            }
        } elseif (in_array($subGolongan, [
            '4 B', '4 C', '4 D', '4 E', '4 F', '5 A', '5 B', '5 C', '5 D',
            '6 A', '6 B', '6 C', '6 D'
        ])) {
            if ($userRole !== 'admin') {
                abort(403, 'Unauthorized');
            }
        } else {
            if (!in_array($userRole, ['admin', 'admin 1', 'admin 2'])) {
                abort(403, 'Unauthorized');
            }
        }

        $request->validate([
            'type.*' => 'required|string|max:255',
            'amount.*' => 'required|string',
            'calculation.*' => 'required|string|max:20',
            'status.*' => 'required|string|max:10',
        ]);

        JobWageAllowance::where('employee_job_id', $jobEmploymentId)->delete();

        foreach ($request->type as $index => $type) {
            $amount = str_replace('.', '', $request->amount[$index]);
            JobWageAllowance::create([
                'employee_job_id' => $jobEmploymentId,
                'type' => $type,
                'amount' => (int)$amount,
                'calculation' => $request->calculation[$index],
                'status' => $request->status[$index],
            ]);
        }

        return redirect()->back()->with('success', 'Wage/Allowance data saved successfully!');
    }
}
