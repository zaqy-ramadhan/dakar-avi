<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Department extends Model
{
    // /** @use HasFactory<\Database\Factories\EventFactory> */
    // use HasFactory, HasUuids;

    protected $table = 'dakar_departments';

    protected $guarded = [
        'id'
    ];

    protected $fillable = [
        'department_name',
        'division_id'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function position()
    {
        return $this->hasMany(Position::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'id');
    }

    public function section()
    {
        return $this->hasMany(Section::class);
    }

    public function employeeJob()
    {
        return $this->hasMany(EmployeeJob::class);
    }

    public function inventory(){
        return $this->hasMany(Inventory::class);
    }
}
