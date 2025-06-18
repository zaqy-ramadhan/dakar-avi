<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDetail extends Model
{
    use HasFactory;

    protected $table = 'dakar_employee_details';

    protected $fillable = [
        'user_id',
        'gender',
        'blood_type',
        'birth_place',
        'birth_date',
        'religion',
        'no_jamsostek',
        'no_npwp',
        'no_ktp',
        'no_phone_house',
        'no_phone',
        'ktp_address',
        'current_address',
        'emergency_contact',
        'tax_status',
        'marital_status',
        'married_year',
        'blue_uniform_size',
        'polo_shirt_size',
        'safety_shoes_size',
        'esd_uniform_size',
        'esd_shoes_size',
        'is_draft',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getGendersAttribute()
    {
        // dd($this->gender);
        return $this->gender === '0' ? 'Laki-laki' : ($this->gender === '1' ? 'Perempuan' : null);
    }

    public function age($date = null){
        $birthDate = $this->birth_date;
        if (!$birthDate) {
            return 'N/A';
        }
        $birth = \Carbon\Carbon::parse($birthDate);
        $now = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
        $years = (int)$birth->diffInYears($now);
        // $months = (int)$birth->diffInMonths($now) % 12;
        return $years . ' Tahun ';
    }
    
}
