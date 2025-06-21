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

    public function LOS($date = null)
    {
        $startDate = $this->join_date;

        if (!$startDate && $this->firstEmployeeJob) {
            $startDate = $this->firstEmployeeJob->start_date;
        }

        if ($startDate) {
            $start = \Carbon\Carbon::parse($startDate);
            $end = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
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

    public function previousEmployeeJob()
    {
        return $this->hasOne(EmployeeJob::class)
            ->where('start_date', '<', $this->latestEmployeeJob?->start_date)
            ->orderBy('start_date', 'desc');
    }

    // public function currentEmployeeJob($date = null)
    // {
    //     $date = $date
    //         ? \Carbon\Carbon::parse($date)->endOfMonth()->endOfDay()
    //         : \Carbon\Carbon::now()->endOfMonth()->endOfDay();

    //     $jobs = $this->relationLoaded('employeeJob')
    //         ? $this->employeeJob
    //         : $this->employeeJob()->get();

    //     return $jobs
    //         ->filter(function ($job) use ($date) {
    //             $start = \Carbon\Carbon::parse($job->start_date)->startOfDay(); // 00:00:00
    //             $end = $job->resign_date
    //                 ? \Carbon\Carbon::parse($job->resign_date)->endOfDay()     // 23:59:59
    //                 : ($job->end_date
    //                     ? \Carbon\Carbon::parse($job->end_date)->endOfDay()    // 23:59:59
    //                     : null);

    //             return $start->lessThanOrEqualTo($date) && (is_null($end) || $date->lessThanOrEqualTo($end));
    //         })
    //         ->sortByDesc('start_date')
    //         ->first();
    // }
    public function currentEmployeeJob($date = null)
    {
        $date = $date
            ? \Carbon\Carbon::parse($date)->endOfMonth()->endOfDay()
            : \Carbon\Carbon::now()->endOfMonth()->endOfDay();

        $jobs = $this->relationLoaded('employeeJob')
            ? $this->employeeJob
            : $this->employeeJob()->get();

        return $jobs
            ->filter(function ($job) use ($date) {
                $start = \Carbon\Carbon::parse($job->start_date)->startOfDay(); // 00:00:00
                $end = $job->resign_date
                    ? \Carbon\Carbon::parse($job->resign_date)->endOfDay()     // 23:59:59
                    : ($job->end_date
                        ? \Carbon\Carbon::parse($job->end_date)->endOfDay()
                        : null);

                // Hanya ambil jika tanggal filter >= start && (belum berakhir atau filter <= end)
                return $date->greaterThanOrEqualTo($start)
                    && (is_null($end) || $date->lessThanOrEqualTo($end));
            })
            ->sortByDesc('start_date')
            ->first();
    }

    public function latestEducation()
    {
        // return $this->hasOne(EmployeeEducation::class)
        //     ->select('*')
        //     ->where('user_id', $this->id)
        //     ->orderByRaw("
        //     CASE education_level
        //         WHEN 'S3' THEN 6
        //         WHEN 'S2' THEN 5
        //         WHEN 'S1' THEN 4
        //         WHEN 'D3' THEN 3
        //         WHEN 'SMA' THEN 2
        //         WHEN 'SMP' THEN 1
        //         WHEN 'SD' THEN 0
        //         ELSE 0
        //     END DESC
        // ")
        //     ->limit(1);
        $order = ['S2' => 5, 'S1' => 4, 'D3' => 3, 'SMA' => 2, 'SMP' => 1];

        return $this->employeeEducations->sortByDesc(function ($edu) use ($order) {
            return $order[$edu->education_level] ?? 0;
        })->first();
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
        $employment_status = $job && $job->jobDoc->isNotEmpty() && $job->jobWageAllowance->isNotEmpty();
        if ($employment_status) {
            $progress = 35;
        } else {
            return $progress;
        }

        $given = $job->inventory->where('employee_job_id', $job->id)->isNotEmpty();
        if ($given) {
            $progress = 50;
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
        if (optional($user->firstEmployeeJob)->user_dakar_role != 'karyawan') {
            if ($inventories_status) {
                $progress = 100;
            } else {
                return $progress;
            }
        } else {
            if ($inventories_status) {
                $progress = 75;
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

    public function progressOnbaordingEmployee()
    {
        $user = $this->load('employeeJob.jobDoc', 'inventory.employeeJob', 'dakarRole', 'employeeDetail', 'firstEmployeeJob', 'employeeJob.inventory.item');

        $progress = 0;
        $message = '🚀 Complete your personal data and supporting documents to start the onboarding process.';

        $personal_status = ($user->employeeDetail ? $user->employeeDetail->is_draft == 0 : false) && $user->employeeEducations && $user->employeeBanks && $user->employeeDocs;
        if ($personal_status) {
            $progress = 25;
            $message = '🚀 Your Contract will land soon. Stay tuned!';
        } else {
            return [
                'progress' => $progress,
                'message' => $message,
            ];
        }

        $job = $user->firstEmployeeJob;
        $employment_status = false;

        if ($job && $job->jobDoc->isNotEmpty()) {
            $contractDoc = $job->jobDoc->firstWhere('type', 'contract');
            if ($contractDoc && $contractDoc->second_party_signature) {
                $progress = 50;
                $message = '🚀 Your Confidentiality Agreement will land soon. Stay tuned!';
            } else {
                return [
                    'progress' => $progress,
                    'message' => $message,
                ];
            }
        } else {
            return [
                'progress' => $progress,
                'message' => $message,
            ];
        }

        $spkDoc = $job->jobDoc->firstWhere('type', 'kerahasiaan');
        if ($spkDoc && $spkDoc->second_party_signature) {
            $progress = 75;
            $message = '🚀 Your Starter Kit Checklist will land soon. Stay tuned!';
        } else {
            return [
                'progress' => $progress,
                'message' => $message,
            ];
        }

        // $totalInventory = $job->inventory->where('employee_job_id', $job->id)->count();
        $specificItems = ['bpjs kesehatan', 'bpjs tk', 'user account great day', 'user account e-slip'];
        $nonSpecificInventories = $job->inventory->filter(function ($item) use ($specificItems) {
            return !in_array(strtolower($item->item->item_name), $specificItems);
        });
        $totalInventory = $nonSpecificInventories->count();

        $acceptedCount = $nonSpecificInventories->where('employee_job_id', $job->id)
            ->where('status', 'Diterima')->count();
        if ($totalInventory > 0 && $acceptedCount === $totalInventory) {
            $progress = 100;
            $message = '🎉 Your onboarding tasks have been completed. Welcome aboard!';
        } else {
            return [
                'progress' => $progress,
                'message' => $message,
            ];
        }

        return [
            'progress' => $progress,
            'message' => $message,
        ];
    }

    public function progressOnboardingAdmin()
    {
        $user = $this->load('employeeJob.jobDoc', 'inventory.employeeJob', 'dakarRole', 'employeeDetail', 'firstEmployeeJob', 'employeeJob.inventory.item');

        $progress = 0;
        $message = '🚀 Complete the employment data to start the onboarding process.';

        $personal_status = ($user->employeeDetail ? $user->employeeDetail->is_draft == 0 : false) && $user->employeeEducations && $user->employeeBanks && $user->employeeDocs;
        if ($personal_status) {
            $progress = 10;
        }
        $hasEmployeeJob = $user->firstEmployeeJob;

        if ($personal_status && $hasEmployeeJob) {
            $progress = 17;
            $message = '🚀 Prepare or fill in the wage allowance to continue the onboarding process.';
        } else {
            return [
                'progress' => $progress,
                'message' => $message,
            ];
        }

        $wage = $user->firstEmployeeJob && $user->firstEmployeeJob->jobWageAllowance->isNotEmpty();
        if ($wage) {
            $progress = 34;
            $message = '🚀 Set Starter Kit to continue the onboarding process.';
        } else {
            return [
                'progress' => $progress,
                'message' => $message,
            ];
        }

        $job = $user->firstEmployeeJob;
        $employment_status = false;

        $totalInventory = $job->inventory->where('employee_job_id', $job->id)->count();
        if ($totalInventory > 0) {
            $message = '🚀 Sign the contract to continue the onboarding process.';
            $progress = 51;
        } else {
            return [
                'progress' => $progress,
                'message' => $message,
            ];
        }

        if ($job && $job->jobDoc->isNotEmpty()) {
            $contractDoc = $job->jobDoc->firstWhere('type', 'contract');
            if ($contractDoc && $contractDoc->second_party_signature) {
                $progress = 68;
                $message = '🚀 Sign the compensation data to continue the onboarding process.';
            } else {
                return [
                    'progress' => $progress,
                    'message' => $message,
                ];
            }
        } else {
            return [
                'progress' => $progress,
                'message' => $message,
            ];
        }

        $spkDoc = $job->jobDoc->firstWhere('type', 'kompensasi');
        if ($spkDoc && $spkDoc->second_party_signature) {
            $progress = 85;
            $message = '🎉 Set the Digital Account to continue the onboarding process.';
        } else {
            return [
                'progress' => $progress,
                'message' => $message,
            ];
        }

        $inumber_status = (bool) $user->employeeInventoryNumber->isNotEmpty();
        if ($inumber_status) {
            $progress = 100;
            $message = '🎉 Onboarding completed!';
            if ($user->firstEmployeeJob && $user->firstEmployeeJob->is_onboarding_completed == false) {
                $user->firstEmployeeJob->is_onboarding_completed = true;
                $user->firstEmployeeJob->save();
            }
        } else {
            return [
                'progress' => $progress,
                'message' => $message,
            ];
        }

        return [
            'progress' => $progress,
            'message' => $message,
        ];
    }

    public function adminNotif()
    {
        $users = User::whereHas('employeeDetail', function ($q) {
            $q->where('is_draft', 0);
        })->get(['id', 'fullname', 'npk']);

        $notif = [
            'personal_completed' => $users->filter(function ($user) {
                return $user->progressOnboardingAdmin()['progress'] === 0;
            }),
            'employment_completed' => $users->filter(function ($user) {
                return $user->progressOnboardingAdmin()['progress'] === 17;
            }),
            'wage_filled' => $users->filter(function ($user) {
                return $user->progressOnboardingAdmin()['progress'] === 34;
            }),
            'contract_signed' => $users->filter(function ($user) {
                return $user->progressOnboardingAdmin()['progress'] === 51;
            }),
            'compensation_signed' => $users->filter(function ($user) {
                return $user->progressOnboardingAdmin()['progress'] === 68;
            }),
            'starterkit_given' => $users->filter(function ($user) {
                return $user->progressOnboardingAdmin()['progress'] === 85;
            }),
            'digital_account_given' => $users->filter(function ($user) {
                return $user->progressOnboardingAdmin()['progress'] === 100;
            }),
        ];

        return $notif;
    }
}
