<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayDate extends Model
{
    protected $table = 'dakar_holiday_date';

    protected $fillable = [
        'date',
        'keterangan',
        'status',
        'is_active'
    ];
}
