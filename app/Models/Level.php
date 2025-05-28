<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $table = 'role_level';

    public $timestamps = false; 

    protected $fillable = [
        'level_name',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_level_id');
    }

    public function employeeJob()
    {
        return $this->hasMany(EmployeeJob::class);
    }
}
