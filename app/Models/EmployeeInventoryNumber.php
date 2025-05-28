<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeInventoryNumber extends Model
{
    protected $table = 'dakar_employee_inventory_number';

    protected $fillable = [
        'user_id',
        'item_id',
        'number',
    ];

    public function user(){
        $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function item(){
        $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
