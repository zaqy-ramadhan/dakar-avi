<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobWageAllowance extends Model
{
    protected $table = 'dakar_job_wage_allowance';

    public $timestamps = false;

    protected $fillable = [
        'employee_job_id',
        'type',
        'amount',
        'calculation',
        'status'
    ];

    public function employeeJob(){
        return $this->belongsTo(EmployeeJob::class, 'employee_job_id', 'id');
    }
}
