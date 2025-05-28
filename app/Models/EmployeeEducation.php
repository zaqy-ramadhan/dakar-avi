<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    use HasFactory;

    protected $table = 'dakar_employee_educations';

    protected $fillable = [
        'user_id',
        'education_level',
        'education_institution',
        'education_city',
        'education_major',
        'education_gpa',
        'education_start_year',
        'education_end_year',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
