<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeFamily extends Model
{
    use HasFactory;

    protected $table = 'dakar_employee_family';

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'birth_date',
        'education',
        'occupation',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
