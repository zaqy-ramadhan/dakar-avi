<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkHour extends Model
{
    protected $table = 'dakar_work_hour_code';

    protected $fillable = [
        'work_hour',
    ];

    public function employeeJob()
    {
        return $this->hasMany(EmployeeJob::class);
    }
}
