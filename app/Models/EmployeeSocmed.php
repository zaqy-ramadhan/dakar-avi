<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSocmed extends Model
{
    protected $table = 'dakar_employee_socmed';

    protected $fillable = [
        'user_id',
        'type',
        'account',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
