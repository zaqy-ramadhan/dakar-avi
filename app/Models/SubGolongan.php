<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubGolongan extends Model
{
    protected $table = 'dakar_sub_golongan';

    public $timestamps = false;

    protected $fillable = [
        'sub_golongan_name',
    ];

    public function employeeJob()
    {
        return $this->hasMany(EmployeeJob::class);
    }
}
