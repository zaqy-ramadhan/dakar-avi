<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $table = 'dakar_stations';

    protected $guarded = [
        'id'
    ];

    public $timestamps = false;

    protected $fillable = [
        'station_name',
        'department_id',
        'is_active'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
