<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryType extends Model
{
    protected $table = 'dakar_inventory_type';

    protected $fillable = [
        'inventory_type_name'
    ];

    public function inventory(){
        return $this->hasMany(Inventory::class);
    }
}
