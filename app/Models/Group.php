<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'dakar_group';

    protected $fillable = [
        'group_name',
    ];

    public $timestamps = false;

    public function employeeJob()
    {
        return $this->hasMany(EmployeeJob::class);
    }
}
