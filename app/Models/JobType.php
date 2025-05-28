<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobType extends Model
{
    protected $table = 'dakar_job_type';

    protected $fillable = [
        'job_type_name',
    ];

    public function employeeJob()
    {
        return $this->hasMany(EmployeeJob::class);
    }
}
