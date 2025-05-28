<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobStatus extends Model
{
    protected $table = 'dakar_job_status';

    public $timestamps = false; 

    protected $guarded = [
        'id'
    ];

    protected $fillable = [
        'job_status_name'
    ];
}
