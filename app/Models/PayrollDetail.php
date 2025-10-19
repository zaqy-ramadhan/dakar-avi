<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    protected $table = 'dakar_payroll_detail';

    protected $fillable = [
        'payroll_id',
        'user_id',
        'npk',
        'work_days',
        'total_attend',
        'basic_salary',
        'total_salary',
        'note'
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
