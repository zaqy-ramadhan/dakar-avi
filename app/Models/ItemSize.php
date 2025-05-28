<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSize extends Model
{
    protected $table = 'dakar_item_size';

    protected $fillable = [
        'size_name',
        'type_id'
    ];

    public function type(){
        return $this->belongsTo(ItemType::class, 'type_id', 'id');
    }
}
