<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    protected $table = 'dakar_cost_centers';

    protected $guarded = [
        'id'
    ];

    protected $fillable = [
        'cost_center_name'
    ];

    public function position()
    {
        return $this->hasMany(Position::class);
    }

    public function employeeJob()
    {
        return $this->hasMany(EmployeeJob::class);
    }
}
