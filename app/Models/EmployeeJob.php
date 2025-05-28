<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeJob extends Model
{
    use HasFactory;

    // Nama tabel yang terkait dengan model
    protected $table = 'dakar_employee_job';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'resign_date',
        'position_id',
        'section_id',
        'division_id',
        'department_id',
        'cost_center_id',
        'role_level_id',
        'job_type_id',
        'golongan_id',
        'sub_golongan_id',
        'group_id',
        'line_id',
        'job_status',
        'user_dakar_role',
        'is_onboarding_completed',
        'employment_status',
        'work_hour_code_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }

    public function jobWageAllowance()
    {
        return $this->hasMany(JobWageAllowance::class);
    }

    public function jobDoc()
    {
        return $this->hasMany(JobDoc::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function getContractAttribute()
    {
        $jobs = EmployeeJob::with('user')->where('user_id', $this->user_id)
            ->orderBy('id')
            ->get();

        $contractJobs = $jobs
            ->where('job_status', 'kontrak')
            ->where('user_dakar_role', 'karyawan')
            ->values();

        $index = $contractJobs->search(fn($job) => $job->id === $this->id);

        if ($index !== false) {
            return 'PKWT-' . ($index + 1);
        } elseif ($this->user_dakar_role === 'pemagangan') {
            return 'Pemagangan';
        } elseif ($this->user_dakar_role === 'internship') {
            return 'Internship';
        } elseif ($this->user_dakar_role === 'karyawan') {
            return 'Tetap';
        } else {
            return 'N/A';
        }
    }


    public function duration()
    {
        if ($this->start_date && $this->end_date) {
            $start = \Carbon\Carbon::parse($this->start_date);
            $end = \Carbon\Carbon::parse($this->end_date);
            $years = (int)$start->diffInYears($end);
            $months = (int)$start->diffInMonths($end) % 12;
            return $years . ' tahun ' . $months . ' bulan';
        }
        return 'N/A';
    }

    public function monthDuration()
    {
        if ($this->start_date && $this->end_date) {
            $start = \Carbon\Carbon::parse($this->start_date);
            $end = \Carbon\Carbon::parse($this->end_date);
            $months = (int)$start->diffInMonths($end);
            return $months . ' bulan';
        }
        return '';
    }

    public function inventoryRule()
    {
        $roleId = DakarRole::where('role_name', $this->user_dakar_role)->first()->id;

        $ruleQuery = InventoryRule::where('dakar_role_id', $roleId);

        if ($this->department_id) {
            $ruleQuery->where('department_id', $this->department_id);
        }
        if ($this->role_level_id) {
            $ruleQuery->where('level_id', $this->role_level_id);
        }
        if ($this->job_status) {
            $ruleQuery->where('job_status', $this->job_status);
        }

        $inventory = $ruleQuery->first();

        // Handle jika tidak ditemukan
        if (!$inventory) {
            return collect();
        }

        $inventoryRule = $inventory->items()->get();

        return $inventoryRule;
    }


    public function inventoryCount()
    {
        if ($this->inventory()->count() == 0) {
            if ($this->inventoryRule()->count() == 0) {
                $inventoryCount = 0;
            } else {
                $inventoryCount = $this->inventoryRule()->count();
            }
        } else {
            $inventoryCount = $this->inventory()->count();
        }
        return $inventoryCount;
    }

    public function inventoryActual()
    {
        if ($this->inventory()->count() == 0) {
            $inventoryActual = 0;
        } else {
            $inventoryActual = $this->inventory()->where('status', 'Diterima')->count();
        }
        return $inventoryActual;
    }

    public function onboardingProgress()
    {
        if ($this->is_onboarding_completed == true) {
            return 100;
        }
        $inventoryCount = $this->inventoryCount();
        $jobDocCount =  $this->jobDoc()->count() != 0 ? $this->jobDoc()->count() : 2;
        $totalCount = $inventoryCount + $jobDocCount;

        $actualInventory = $this->inventoryActual();
        $actualJobDoc = $this->jobDoc() ? $this->jobDoc()->where('employee_job_id', $this->id)->where('first_party_signature', '!=', null)->count() : 0;
        $actualCount = $actualInventory + $actualJobDoc;

        $progress = (int)($actualCount  / $totalCount * 100);

        if ($progress == 100) {
            if ($this->is_onboarding_completed == false) {
                $this->is_onboarding_completed = true;
                $this->save();
            }
        }

        // $user = User::with('employeeJob.jobDoc', 'inventory.employeeJob', 'dakarRole', 'employeeDetail', 'firstEmployeeJob')->findOrFail($this->user_id);

        // $progress = 0;
        // $personal_status = $user->employeeDetail && $user->employeeEducations && $user->employeeBanks && $user->employeeDocs;
        // $personal_status ? $progress = 10 : $progress;

        // $job = $user->employeeJob->first();
        // $employment_status = $job && $job->jobDoc && $job->jobWageAllowance && $job->inventory->where('employee_job_id', $job->id);
        // $employment_status ? $progress = 35 : $progress;

        // $inventories_status = $job && $job->inventory->where('employee_job_id', $job->id)->where('status', '-')->isEmpty();
        // $inventories_status ? $progress = 50 : $progress;

        // $inumber_status = (bool) $user->employeeInventoryNumber;
        // $inumber_status ? $progress = 100 : $progress;

        return $progress;
    }

    public function scopeInProgress($query)
    {
        return $query->whereRaw('onboardingProgress() < 100');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id', 'id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'role_level_id', 'id');
    }

    public function jobType()
    {
        return $this->belongsTo(JobType::class, 'job_type_id', 'id');
    }

    public function golongan()
    {
        return $this->belongsTo(Golongan::class, 'golongan_id', 'id');
    }

    public function subGolongan()
    {
        return $this->belongsTo(SubGolongan::class, 'sub_golongan_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    public function line()
    {
        return $this->belongsTo(Line::class, 'line_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }

     public function workHour()
    {
        return $this->belongsTo(WorkHour::class, 'work_hour_code_id', 'id');
    }
}
