<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'dakar_sections';

    public $timestamps = false;

    protected $fillable = [
        'department_id',
        'section_name'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function employeeJob()
    {
        return $this->hasMany(EmployeeJob::class);
    }
}
