<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryRule extends Model
{
    protected $table = 'dakar_inventory_rule';

    protected $fillable = [
        'dakar_role_id',
        // 'department_id',
        // 'level_id',
        // 'job_status'
    ];

    public function dakarRole(){
        return $this->belongsTo(DakarRole::class, 'dakar_role_id', 'id');
    }

    // public function department(){
    //     return $this->belongsTo(Department::class, 'department_id', 'id');
    // }


    // public function level(){
    //     return $this->belongsTo(Level::class, 'level_id', 'id');
    // }

    public function items(){
        return $this->belongsToMany(Item::class, 'dakar_inventory_rule_item', 'rule_id', 'item_id');
    }

    public function department(){
        return $this->belongsToMany(Department::class, 'dakar_inventory_rule_department', 'rule_id', 'department_id');
    }
    
}
