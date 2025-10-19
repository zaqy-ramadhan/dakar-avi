<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = 'dakar_payroll';

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'note',
        'status',
        'total_salary'
    ];

    public function payrollDetail()
    {
        return $this->hasMany(PayrollDetail::class, 'payroll_id', 'id');
    }
}
