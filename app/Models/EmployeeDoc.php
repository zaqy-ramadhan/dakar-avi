<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDoc extends Model
{
    use HasFactory;

    protected $table = 'dakar_employee_docs';

    protected $fillable = [
        'user_id',
        'doc_type',
        'doc_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
