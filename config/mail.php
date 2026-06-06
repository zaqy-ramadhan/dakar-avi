<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send all email
    | messages unless another mailer is explicitly specified when sending
    | the message. All additional mailers can be configured within the
    | "mailers" array. Examples of each type of mailer are provided.
    |
    */

    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers that can be used
    | when delivering an email. You may specify which one you're using for
    | your mailers below. You may also add additional mailers if needed.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "resend", "log", "array",
    |            "failover", "roundrobin"
    |
    */

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all emails sent by your application to be sent from
    | the same address. Here you may specify a name and address that is
    | used globally for all emails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Contract Expiry Reminder Recipients
    |--------------------------------------------------------------------------
    |
    | Email addresses to receive contract expiry reminder notifications.
    | Can be using a single email or comma-separated emails.
    |
    */

    'contract_expiry_recipients' => [
        // 'buddy.service@astra-visteon.com',
        // 'hr@astra-visteon.com',
        'nasghifarz619@gmail.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Log Recipients
    |--------------------------------------------------------------------------
    |
    | Email addresses to receive real-time activity log notifications.
    | Every activity log will trigger an email to these recipients.
    |
    */

    'activity_log_recipients' => [
        // 'buddy.service@astra-visteon.com',
        // 'hr@astra-visteon.com',
        'nasghifarz619@gmail.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Log Daily Summary Recipients
    |--------------------------------------------------------------------------
    |
    | Email addresses to receive daily activity log summary.
    | Summary is sent once per day with filtered categories only.
    |
    */

    'activity_log_summary_recipients' => [
        'buddy.service@astra-visteon.com',
        // Tambahkan email lain sesuai kebutuhan
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Log Summary Categories Filter
    |--------------------------------------------------------------------------
    |
    | Kategori table_name yang ingin ditampilkan dalam rekap harian.
    | Hanya aktivitas dari tabel-tabel ini yang akan disertakan dalam email summary.
    |
    | Nilai yang tersedia:
    | - dakar_job_documents (update dokumen karyawan)
    | - dakar_employee_jobs (update pekerjaan/kontrak karyawan)
    |
    */

    'activity_log_summary_categories' => [
        'dakar_job_documents',
        'dakar_employee_jobs',
    ],

    //production
    //  'contract_expiry_recipients' => [
    //     'nasghifarz619@gmail.com',
    //     'fitrimar@gmail.com',
    //     'sadtu.risdiyati@astra-visteon.com',
    //     'risyad0210@gmail.com',
    //     'hr@astra-visteon.com',
    // ],

    // 'activity_log_recipients' => [
    //     'nasghifarz619@gmail.com',
    //     'safitri@astra-visteon.com',
    //     // 'sadtu.risdiyati@astra-visteon.com',
    //     // 'risyad0210@gmail.com',
    //     'hr@astra-visteon.com',
    // ],

];
