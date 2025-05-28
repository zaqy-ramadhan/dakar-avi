<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobDoc extends Model
{
    protected $table = 'dakar_job_documents';
    
    protected $fillable = [
        'employee_job_id',
        'type',
        'first_party_signature',
        'second_party_signature'
    ];

    public function employeeJob(){
        return $this->belongsTo(EmployeeJob::class, 'employee_job_id', 'id');
    }
}
