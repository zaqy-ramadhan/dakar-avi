<?php

namespace App\Console\Commands;

use App\Exports\ContractExpiryReminderExport;
use App\Mail\ContractExpiryReminder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SendContractExpiryReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:contract-expiry-reminder {--recipients=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send contract expiry reminder email for employees with contracts expiring in next 3 months';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔔 Starting contract expiry reminder email process...');

        try {
            // Get employees with contracts expiring in next 3 months
            $now = Carbon::now();
            $threeMonthsLater = $now->clone()->addMonths(3);

            $employees = User::whereHas('employeeJob', function ($query) use ($now, $threeMonthsLater) {
                $query->whereBetween('end_date', [$now, $threeMonthsLater])
                    ->where('job_status', 'kontrak');
            })
                ->with([
                    'employeeJob' => function ($query) {
                        $query->whereBetween('end_date', [Carbon::now(), Carbon::now()->addMonths(3)])
                            ->where('job_status', 'kontrak')
                            ->orderBy('end_date', 'asc');
                    },
                    'employeeJob.position:id,position_name',
                    'employeeJob.department:id,department_name',
                    'employeeJob.division:id,division_name',
                ])
                ->select('id', 'fullname', 'email', 'npk')
                ->get()
                ->map(function ($employee) {
                    // Get the first (nearest expiry) employeeJob
                    $job = $employee->employeeJob->first();
                    
                    if ($job) {
                        // Calculate remaining days - from now until end date
                        $employee->remaining_days = $job->end_date ? now()->diffInDays($job->end_date) : 0;
                        $employee->npk = $employee->npk ?? $employee->id;
                        $employee->employment_status = $job->employment_status ?? 'Kontrak';
                        $employee->position_name = $job->position?->position_name ?? '-';
                        $employee->department_name = $job->department?->department_name ?? '-';
                        $employee->division_name = $job->division?->division_name ?? '-';
                        $employee->current_job = $job;
                    }
                    
                    return $employee;
                });

            if ($employees->isEmpty()) {
                $this->warn('No employees found with contracts expiring in the next 3 months.');
                return self::SUCCESS;
            }

            $this->info("Found {$employees->count()} employee(s) with expiring contracts in the next 3 months.");

            // Create Excel file
            $filename = 'contract_expiry_reminder_' . now()->format('Y-m-d_His') . '.xlsx';
            $filePath = 'temp/' . $filename;

            if (!Storage::exists('temp')) {
                Storage::makeDirectory('temp');
            }

            Excel::store(new ContractExpiryReminderExport($employees), $filePath);
            $fullPath = Storage::path($filePath);

            $this->info("✅ Excel file created: {$filename}");

            // Get recipients from command option or use default
            $recipients = $this->option('recipients')
                ? explode(',', $this->option('recipients'))
                : config('mail.contract_expiry_recipients', [
                    'buddy.service@astra-visteon.com',
                    'hr@astra-visteon.com',
                ]);

            // Remove whitespace from emails
            $recipients = array_map('trim', $recipients);

            // Send email to all recipients
            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient)->send(new ContractExpiryReminder($employees, $fullPath));
                    $this->info("✉️ Email sent to: {$recipient}");
                } catch (\Exception $e) {
                    $this->error("Failed to send email to {$recipient}: " . $e->getMessage());
                }
            }

            // Clean up temporary file
            if (file_exists($fullPath)) {
                unlink($fullPath);
                $this->info('🧹 Temporary file cleaned up.');
            }

            $this->info('✅ Contract expiry reminder process completed successfully!');
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
