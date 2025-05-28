<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'dakar_inventory';

    protected $fillable = [
        'user_id',
        'item_id',
        // 'inventory_type_id',
        'due_date',
        'acc_date',
        'return_date',
        'employee_job_id',
        // 'quantity',
        'size',
        'status',
        'return_notes'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function item(){
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function getItemNameAttribute() {
        return $this->item->item_name;
    }

    public function inventoryType(){
        return $this->belongsTo(InventoryType::class, 'inventory_type_id', 'id');
    }

    public function employeeJob(){
        return $this->belongsTo(EmployeeJob::class, 'employee_job_id', 'id');
    }
}
