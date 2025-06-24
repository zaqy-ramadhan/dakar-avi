<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disnaker extends Model
{
    protected $table = 'dakar_disnaker';

    public $timestamps = false;

    protected $fillable = [
        "nama",
        "nip",
    ];

}
