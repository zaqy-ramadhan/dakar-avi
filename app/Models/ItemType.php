<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    protected $table = 'dakar_item_type';

    protected $fillable = [
        'type_name'
    ];

    public function item(){
        return $this->hasMany(Item::class);
    }

    public function size(){
        return $this->hasMany(ItemSize::class);
    }
}
