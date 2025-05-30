<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    protected $table = 'dakar_line';

    protected $fillable = [
        'line_name',
    ];

    public $timestamps = false;

    public function employeeJob()
    {
        return $this->hasMany(EmployeeJob::class);
    }
}
