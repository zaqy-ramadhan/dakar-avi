<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offboarding extends Model
{
    protected $table = 'dakar_offboarding';

    protected $fillable = [
        'user_id',
        'resign_date',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
