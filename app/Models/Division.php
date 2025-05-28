<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $table = 'dakar_divisions';

    protected $guarded = [
        'id'
    ];

    protected $fillable = [
        'division_name'
    ];

    public function department()
    {
        return $this->hasMany(Department::class);
    }

    public function employeeJob()
    {
        return $this->hasMany(EmployeeJob::class);
    }
}
