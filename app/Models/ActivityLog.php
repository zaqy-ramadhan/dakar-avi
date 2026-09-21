<?php

namespace App\Models;

use App\Mail\ActivityLogNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ActivityLog extends Model
{
    protected $table = 'dakar_activity_logs';
    public $timestamps = true;

    protected $fillable = [
        'actor_id',
        'employee_id',
        'note',
        'table_name',
        'table_id',
    ];

    protected static function boot()
    {
        parent::boot();

        /**
         * Send email notification setiap kali activity log dibuat
         */
        static::created(function ($activityLog) {
            // Get recipient emails from config, atau set default
            $recipients = config('mail.activity_log_recipients', [
                'nasghifarz619@gmail.com',
            ]);

            if (!empty($recipients)) {
                // Load relations untuk email
                $activityLog->load(['actor', 'employee']);

                // Send email ke semua recipients
                foreach ($recipients as $recipient) {
                    try {
                        Mail::to($recipient)
                            ->queue(new ActivityLogNotification($activityLog));
                    } catch (\Exception $e) {
                        // Tangkap error dan catat di log sistem, agar tidak menyebabkan kegagalan proses utama
                        Log::error('Failed to send activity log email to ' . $recipient . ': ' . $e->getMessage());
                    }
                }
            }
        });
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id', 'id');
    }

     public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

}
