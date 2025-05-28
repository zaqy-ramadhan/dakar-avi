<?php

namespace App\Http\Controllers;

use App\Models\JobType;

class JobTypeController extends UniversalCrudController
{
    public function __construct()
    {
        parent::__construct(JobType::class, 'dakar_job_type', ['id', 'job_type_name']);
    }
}
