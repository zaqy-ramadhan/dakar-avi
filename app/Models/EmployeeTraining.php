<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTraining extends Model
{
    use HasFactory;

    protected $table = 'dakar_employee_trainings';

    protected $fillable = [
        'user_id',
        'training_institution',
        'training_year',
        'training_duration',
        'training_certificate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
