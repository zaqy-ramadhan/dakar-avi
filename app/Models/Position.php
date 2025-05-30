<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table = 'dakar_positions';

    protected $guarded = [
        'id'
    ];

    public $timestamps = false; 

    protected $fillable = [
        'position_name',
        'department_id',
        'cost_center_id'
    ];

    public $timestamps = false;

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function CostCenter()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id', 'id');
    }

    public function employeeJob()
    {
        return $this->hasMany(EmployeeJob::class);
    }

    public function inventoryRule(){
        return $this->hasMany(InventoryRule::class);
    }
}
