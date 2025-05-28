<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'dakar_items';

    protected $fillable = [
        'item_name',
        'type'
    ];

    public function type(){
        return $this->belongsTo(ItemType::class, 'type_id', 'id');
    }
    public function inventory(){
        return $this->hasMany(Inventory::class);
    }

    public function rules(){
        return $this->belongsToMany(InventoryRule::class, 'dakar_inventory_rule_item', 'item_id', 'rule_id');
    }

    public function employeeInventoryNumber(){
        return $this->hasMany(EmployeeInventoryNumber::class);
    }
}
