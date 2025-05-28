<?php

namespace App\Http\Controllers;

use App\Models\WorkHour;

class WorkHourController extends UniversalCrudController
{
    public function __construct()
    {
        parent::__construct(WorkHour::class, 'dakar_work_hour_code', ['id', 'work_hour']);
    }
}
