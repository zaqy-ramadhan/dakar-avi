<?php

namespace App\Http\Middleware;

use App\Models\JobDoc;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckEmployeeEmploymentStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user->employeeJob->isNotEmpty()) {
            $employeeJob = $user->latestEmployeeJob;
            $employeeOffboarding = $user->offboarding;
            $employeeJob = $user->latestEmployeeJob;
            $paklaring = JobDoc::where('employee_job_id', $employeeJob->id)->where('type', 'paklaring')->first();
            $sksmk = JobDoc::where('employee_job_id', $employeeJob->id)->where('type', 'sksmk')->first();

            $isEmployed = $employeeJob->employment_status;
            $isOffboarded = $employeeOffboarding?->exit_interview;

            if (!$isEmployed && $isOffboarded && !$paklaring && !$sksmk) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'npk' => 'Akun Anda sudah dinonaktifkan.',
                ]);
            }
        }

        return $next($request);
    }
}
