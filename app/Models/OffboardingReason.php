<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffboardingReason extends Model
{
    protected $table = 'dakar_offboarding_reason';

    public $timestamps = false;

    protected $fillable = [
        "reason",
    ];
}
