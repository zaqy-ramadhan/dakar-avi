<?php

namespace App\Console\Commands;

use App\Mail\ActivityLogDailySummary;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendActivityLogDailySummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:activity-log-summary {--recipients=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily activity log summary email with filtered categories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📊 Starting daily activity log summary email process...');

        try {
            // Get date to summarize (default: yesterday)
            $dateString = $this->option('date') ?? Carbon::now()->subDay()->toDateString();
            $summaryDate = Carbon::createFromFormat('Y-m-d', $dateString)->startOfDay();

            $this->info("📅 Processing activity logs for: {$dateString}");

            // Get filtered categories from config
            $allowedCategories = config('mail.activity_log_summary_categories', [
                'dakar_job_documents',
                'dakar_employee_jobs',
            ]);

            // Get activity logs from the specified date with allowed categories only
            $activities = ActivityLog::whereBetween('created_at', [
                $summaryDate,
                $summaryDate->clone()->endOfDay(),
            ])
                ->whereIn('table_name', $allowedCategories)
                ->with(['actor:id,fullname,npk,email', 'employee:id,fullname,npk,email'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('table_name');

            // Calculate statistics
            $stats = [
                'total_activities' => ActivityLog::whereBetween('created_at', [
                    $summaryDate,
                    $summaryDate->clone()->endOfDay(),
                ])
                    ->whereIn('table_name', $allowedCategories)
                    ->count(),
                'total_users_acted' => ActivityLog::whereBetween('created_at', [
                    $summaryDate,
                    $summaryDate->clone()->endOfDay(),
                ])
                    ->whereIn('table_name', $allowedCategories)
                    ->distinct('actor_id')
                    ->count(),
                'categories_count' => $activities->count(),
            ];

            if ($stats['total_activities'] === 0) {
                $this->warn('No activity logs found for the specified date and categories.');
                return self::SUCCESS;
            }

            $this->info("✅ Found {$stats['total_activities']} activities across {$stats['categories_count']} categories");

            // Get recipients
            $recipients = $this->getRecipients();

            if (empty($recipients)) {
                $this->warn('No recipients configured for activity log summary.');
                return self::SUCCESS;
            }

            // Send email to all recipients
            foreach ($recipients as $recipient) {
                $this->info("📧 Sending summary to: {$recipient}");

                Mail::to($recipient)->send(new ActivityLogDailySummary(
                    activities: $activities,
                    summaryDate: $summaryDate,
                    stats: $stats,
                ));
            }

            $this->info('✅ Daily activity log summary emails sent successfully!');
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error sending activity log summary: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Get email recipients from option or config
     */
    private function getRecipients(): array
    {
        $recipientsOption = $this->option('recipients');

        if ($recipientsOption) {
            return array_map('trim', explode(',', $recipientsOption));
        }

        return config('mail.activity_log_summary_recipients', [
            'buddy.service@astra-visteon.com',
        ]);
    }
}
