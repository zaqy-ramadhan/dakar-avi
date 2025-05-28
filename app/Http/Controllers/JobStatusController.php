<?php

namespace App\Http\Controllers;

use App\Models\JobStatus;

class JobStatusController extends UniversalCrudController
{
    public function __construct()
    {
        parent::__construct(JobStatus::class, 'dakar_job_status', ['id', 'job_status_name']);
    }
}
