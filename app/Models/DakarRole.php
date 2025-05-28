<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DakarRole extends Model
{
    use HasFactory;

    // Jika nama tabel tidak mengikuti konvensi plural, set secara eksplisit
    protected $table = 'dakar_role';

    protected $fillable = [
        'role_name',
    ];

    /**
     * Relasi many-to-many dengan User.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'dakar_role_user');
    }

    public function inventoryRule(){
        return $this->hasMany(InventoryRule::class);
    }
}
