<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'dakar_activity_logs';
    public $timestamps = true;

    protected $fillable = [
        'actor_id',
        'employee_id',
        'note',
        'table_name',
        'table_id',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id', 'id');
    }

     public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

}
