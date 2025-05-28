<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Golongan extends Model
{
    protected $table = 'dakar_golongan';

    public $timestamps = false; 

    protected $fillable = [
        'golongan_name',
    ];

    public function employeeJob()
    {
        return $this->hasMany(EmployeeJob::class);
    }

    public function inventoryRule(){
        return $this->hasMany(InventoryRule::class);
    }
}
