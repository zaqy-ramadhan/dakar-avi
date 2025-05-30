<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'users';

    protected $fillable = [
        'npk',
        'name',
        'email',
        'username',
        'fullname',
        'join_date',
        'end_date',
        'depart_id',
        'director_id',
        'role_level_id',
        'join_date',
        'password',
        'password_hash',
        'user_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'password_hash',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'depart_id', 'id');
    }

    public function dakarRole()
    {
        return $this->belongsToMany(DakarRole::class, 'dakar_role_user');
    }

    public function getRole()
    {
        return $this->dakarRole()->pluck('role_name')->first();
    }

    public function getRoleId()
    {
        return $this->dakarRole()->pluck('id')->first();
    }

    public function LOS()
    {
        $startDate = $this->join_date;

        if (!$startDate && $this->firstEmployeeJob) {
            $startDate = $this->firstEmployeeJob->start_date;
        }

        if ($startDate) {
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::now();
            $years = (int)$start->diffInYears($end);
            $months = (int)$start->diffInMonths($end) % 12;
            return $years . ' tahun ' . $months . ' bulan';
        }

        return 'N/A';
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'role_level_id');
    }

    public function employeeJob()
    {
        return $this->hasMany(EmployeeJob::class, 'user_id', 'id');
    }

    public function employeeSocmed()
    {
        return $this->hasMany(EmployeeSocmed::class, 'user_id', 'id');
    }

    public function employeeEducations()
    {
        return $this->hasMany(EmployeeEducation::class, 'user_id', 'id');
    }

    public function employeeTrainings()
    {
        return $this->hasMany(EmployeeTraining::class, 'user_id', 'id');
    }

    public function employeeFamily()
    {
        return $this->hasMany(EmployeeFamily::class, 'user_id', 'id');
    }

    public function employeeBanks()
    {
        return $this->hasMany(EmployeeBank::class, 'user_id', 'id');
    }

    public function employeeDocs()
    {
        return $this->hasMany(EmployeeDoc::class, 'user_id', 'id');
    }

    public function employeeDetail()
    {
        return $this->hasOne(EmployeeDetail::class, 'user_id', 'id');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function employeeInventoryNumber()
    {
        return $this->hasMany(EmployeeInventoryNumber::class);
    }

    public function offboarding()
    {
        return $this->hasOne(Offboarding::class);
    }

    public function latestEmployeeJob()
    {
        return $this->hasOne(EmployeeJob::class)->latestOfMany('start_date');
    }

    public function firstEmployeeJob()
    {
        return $this->hasOne(EmployeeJob::class)->orderBy('start_date', 'asc');
    }

    public function progressOnboarding()
    {
        $user = $this->load('employeeJob.jobDoc', 'inventory.employeeJob', 'dakarRole', 'employeeDetail', 'firstEmployeeJob', 'employeeJob.inventory.item');

        $progress = 0;

        $personal_status = ($user->employeeDetail ? $user->employeeDetail->is_draft == 0 : false) && $user->employeeEducations && $user->employeeBanks && $user->employeeDocs;
        if ($personal_status) {
            $progress = 10;
        } else {
            return $progress;
        }

        $job = $user->employeeJob->first();
        $employment_status = $job && $job->jobDoc->isNotEmpty() && $job->jobWageAllowance->isNotEmpty() && $job->inventory->where('employee_job_id', $job->id)->isNotEmpty();
        if ($employment_status) {
            $progress = 35;
        } else {
            return $progress;
        }

        $specificItems = ['bpjs kesehatan', 'bpjs tk', 'user account great day', 'user account e-slip'];
        $inventories_status = false;
        if ($job && $job->inventory->isNotEmpty()) {
            $nonSpecificInventories = $job->inventory->filter(function ($item) use ($specificItems) {
                return !in_array(strtolower($item->item->item_name), $specificItems);
            });
            $inventories_status = $nonSpecificInventories->where('status', '-')->isEmpty();
        }
        if(optional($user->firstEmployeeJob)->user_dakar_role != 'karyawan') {
            if ($inventories_status) {
                $progress = 100;
            } else {
                return $progress;
            }
        } else {
            if ($inventories_status) {
                $progress = 50;
            } else {
                return $progress;
            }
        }
        
        $inumber_status = (bool) $user->employeeInventoryNumber->isNotEmpty();
        if ($inumber_status) {
            $progress = 100;
            if ($user->firstEmployeeJob && $user->firstEmployeeJob->is_onboarding_completed == false) {
                $user->firstEmployeeJob->is_onboarding_completed = true;
                $user->firstEmployeeJob->save();
            }
        } else {
            return $progress;
        }

        return $progress;
    }
}
