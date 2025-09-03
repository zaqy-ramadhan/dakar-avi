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
        return $this->dakarRole()->pluck('role_name')->first() ?? 'karyawan';
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

    public function firstEmployeeJobIncomplete()
    {
        return $this->hasOne(EmployeeJob::class)->where('is_onboarding_completed', false)->where('employment_status', true)->orderBy('start_date', 'asc');
        // return $this->hasOne(EmployeeJob::class)
        // ->ofMany(
        //     ['start_date' => 'min'],
        //     function ($query) {
        //         $query->where('employment_status', true)
        //             ->where('is_onboarding_completed', false);
        //     }
        // );
    }


    public function progressOnboarding()
    {
        // $user = $this->load('employeeJob.jobDoc', 'inventory.employeeJob', 'dakarRole', 'employeeDetail', 'firstEmployeeJob', 'employeeJob.inventory.item');
        $user = $this;
        $progress = 0;

        $personal_status = $user->personal_status()['status'];
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

        $given = $user->inventory_set_status()['status'];
        if ($given) {
            $progress = 50;
        } else {
            return $progress;
        }

        $inventories_status = $user->inventory_acc_status()['status'];
        if ($inventories_status) {
            $progress = 75;
        } else {
            return $progress;
        }


        $inumber_status = $user->inumber_status()['status'];
        if ($inumber_status) {
            $progress = 100;
            if ($user->firstEmployeeJob && optional($user->firstEmployeeJob)->is_onboarding_completed == false) {
                $user->firstEmployeeJob->is_onboarding_completed = true;
                $user->firstEmployeeJob->save();
            }
        } else {
            return $progress;
        }

        return $progress;
    }

    public function progressOnboardingEmployee()
    {
        // $user = $this->load('employeeJob.jobDoc', 'inventory.employeeJob', 'dakarRole', 'employeeDetail', 'firstEmployeeJob', 'employeeJob.inventory.item');
        $user = $this;

        $progress = 0;
        $message = '🚀 Complete your personal data and supporting documents to start the onboarding process.';

        if (optional($user->firstEmployeeJob)->is_onboarding_completed === true) {
            // Lanjutkan logic
            return [
                'progress' => 100,
                'message' => '🎉 Onboarding completed!'
            ];
        }

        $personal_status = $user->personal_status()['status'];
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

        // // $totalInventory = $job->inventory->where('employee_job_id', $job->id)->count();
        // $specificItems = ['bpjs kesehatan', 'bpjs tk', 'user account great day', 'user account e-slip'];
        // $nonSpecificInventories = $job->inventory->filter(function ($item) use ($specificItems) {
        //     return !in_array(strtolower($item->item->item_name), $specificItems);
        // });
        // $totalInventory = $nonSpecificInventories->count();

        // $acceptedCount = $nonSpecificInventories->where('employee_job_id', $job->id)
        //     ->where('status', 'Diterima')->count();
        // if ($totalInventory > 0 && $acceptedCount === $totalInventory) {
        //     $progress = 100;
        //     $message = '🎉 Your onboarding tasks have been completed. Welcome aboard!';
        // } else {
        //     return [
        //         'progress' => $progress,
        //         'message' => $message,
        //     ];
        // }
        $acc_inventory = $user->inventory_acc_status()['status'];
        if ($acc_inventory) {
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

    // public function progressOnboardingAdmin()
    // {
    //     // $user = $this->load('employeeJob.jobDoc', 'inventory.employeeJob', 'dakarRole', 'employeeDetail', 'firstEmployeeJob', 'employeeJob.inventory.item');
    //     $user = $this;

    //     $progress = 0;
    //     $message = '🚀 Complete the employment data to start the onboarding process.';

    //     $personal_status = $user->personal_status()['status'];
    //     if ($personal_status) {
    //         $progress = 10;
    //         $message = '🚀 Prepare or fill the employment data to continue the onboarding process.';
    //     }

    //     $hasEmployeeJob = $user->firstEmployeeJob;

    //     if ($personal_status && $hasEmployeeJob) {
    //         $progress = 17;
    //         $message = '🚀 Prepare or fill in the wage allowance to continue the onboarding process.';
    //         $user->firstEmployeeJob->update([
    //             "onboarding_progress" => $progress
    //         ]);
    //     } else {
    //         return [
    //             'progress' => $progress,
    //             'message' => $message,
    //         ];
    //     }

    //     $wage = $user->firstEmployeeJob && $user->firstEmployeeJob->jobWageAllowance->isNotEmpty();
    //     if ($wage) {
    //         $progress = 34;
    //         $message = '🚀 Set Starter Kit to continue the onboarding process.';
    //         $user->firstEmployeeJob->update([
    //             "onboarding_progress" => $progress
    //         ]);
    //     } else {
    //         return [
    //             'progress' => $progress,
    //             'message' => $message,
    //         ];
    //     }


    //     $totalInventory = $user->inventory_set_status()['status'];
    //     if ($totalInventory) {
    //         $message = '🚀 Sign the contract to continue the onboarding process.';
    //         $progress = 51;
    //         $user->firstEmployeeJob->update([
    //             "onboarding_progress" => $progress
    //         ]);
    //     } else {
    //         return [
    //             'progress' => $progress,
    //             'message' => $message,
    //         ];
    //     }

    //     $totalInventory = $user->inventory_acc_status()['status'];
    //     if ($totalInventory) {
    //         $progress = 52;
    //         $user->firstEmployeeJob->update([
    //             "onboarding_progress" => $progress
    //         ]);
    //     } else {
    //         return [
    //             'progress' => $progress,
    //         ];
    //     }

    //     $job = $user->firstEmployeeJob;

    //     if ($job && $job->jobDoc->isNotEmpty()) {
    //         $contractDoc = $job->jobDoc->firstWhere('type', 'contract');
    //         if ($contractDoc && $contractDoc->first_party_signature) {
    //             $progress = 68;
    //             $message = '🚀 Sign the compensation data to continue the onboarding process.';
    //             $user->firstEmployeeJob->update([
    //             "onboarding_progress" => $progress
    //         ]);
    //         } else {
    //             return [
    //                 'progress' => $progress,
    //                 'message' => $message,
    //             ];
    //         }
    //     } else {
    //         return [
    //             'progress' => $progress,
    //             'message' => $message,
    //         ];
    //     }

    //     $spkDoc = $job->jobDoc->firstWhere('type', 'kompensasi');
    //     if ($spkDoc && $spkDoc->first_party_signature) {
    //         $progress = 85;
    //         $message = '🎉 Set the Digital Account to continue the onboarding process.';
    //         $user->firstEmployeeJob->update([
    //             "onboarding_progress" => $progress
    //         ]);
    //     } else {
    //         return [
    //             'progress' => $progress,
    //             'message' => $message,
    //         ];
    //     }

    //     $inumber_status = $this->inumber_status()['status'];
    //     if ($inumber_status) {
    //         $progress = 100;
    //         $message = '🎉 Onboarding completed!';
    //         if ($user->firstEmployeeJob && optional($user->firstEmployeeJob)->is_onboarding_completed == false) {
    //             $user->firstEmployeeJob->is_onboarding_completed = true;
    //             $user->firstEmployeeJob->onboarding_progress = $progress;
    //             $user->firstEmployeeJob->save();
    //         }
    //     } else {
    //         return [
    //             'progress' => $progress,
    //             'message' => $message,
    //         ];
    //     }

    //     return [
    //         'progress' => $progress,
    //         'message' => $message,
    //     ];
    // }


    public function progressOnboardingAdmin()
    {
        $user = $this;
        $job = $user->firstEmployeeJob;

        $progress = 0;
        $message = '🚀 Complete the employment data to start the onboarding process.';

        if (!$job) {
            return [
                'progress' => $progress,
                'message' => $message,
            ];
        }

        $steps = [
            [
                'check' => fn() => $user->personal_status()['status'],
                'progress' => 10,
                'message' => '🚀 Prepare or fill the employment data to continue the onboarding process.',
            ],
            [
                'check' => fn() => $job,
                'progress' => 17,
                'message' => '🚀 Prepare or fill in the wage allowance to continue the onboarding process.',
            ],
            [
                'check' => fn() => $job->jobWageAllowance->isNotEmpty(),
                'progress' => 34,
                'message' => '🚀 Set Starter Kit to continue the onboarding process.',
            ],
            [
                'check' => fn() => $user->inventory_set_status()['status'],
                'progress' => 51,
                'message' => '🚀 Sign the contract to continue the onboarding process.',
            ],
            [
                'check' => fn() => $user->inventory_acc_status()['status'],
                'progress' => 52,
                'message' => null,
            ],
            [
                'check' => function () use ($job) {
                    $contractDoc = $job->jobDoc->firstWhere('type', 'contract');
                    return $contractDoc && $contractDoc->first_party_signature;
                },
                'progress' => 68,
                'message' => '🚀 Sign the compensation data to continue the onboarding process.',
            ],
            [
                'check' => function () use ($job) {
                    $spkDoc = $job->jobDoc->firstWhere('type', 'kompensasi');
                    return $spkDoc && $spkDoc->first_party_signature;
                },
                'progress' => 85,
                'message' => '🎉 Set the Digital Account to continue the onboarding process.',
            ],
            [
                'check' => fn() => $this->inumber_status()['status'],
                'progress' => 100,
                'message' => '🎉 Onboarding completed!',
            ],
        ];

        foreach ($steps as $step) {
            if ($step['check']()) {
                $progress = $step['progress'];
                $message = $step['message'] ?? $message;

                if($job->user_dakar_role != 'karyawan'){
                    if($progress === 85){
                        $progress = 100;
                        $message = '🎉 Onboarding completed!';
                    }
                }

                // Update progress di setiap step valid
                $job->onboarding_progress = $progress;
                $job->save();

                if ($progress === 100 && !$job->is_onboarding_completed) {
                    $job->is_onboarding_completed = true;
                }
            } else {
                $job->save();
                return [
                    'progress' => $progress,
                    'message' => $message,
                ];
            }
        }

        $job->save();

        return [
            'progress' => $progress,
            'message' => $message,
        ];
    }

    public function adminNotif()
    {
        $users = User::whereHas('employeeDetail', function ($q) {
            $q->where('is_draft', false);
        })->where(function ($query) {
            $query->doesntHave('firstEmployeeJob')
                ->orWhereHas('firstEmployeeJobIncomplete');
        })
            ->get(['id', 'fullname', 'npk']);

        // Cache progress hanya sekali!
        $usersWithProgress = $users->map(function ($user) {
            $user->progress = $user->progressOnboardingAdmin()['progress'];
            return $user;
        });


        $notif = [
            'personal_completed' => $usersWithProgress->where('progress', 0),
            'employment_completed' => $usersWithProgress->where('progress', 17),
            'wage_filled' => $usersWithProgress->where('progress', 34),
            'starterkit_given' => $usersWithProgress->where('progress', 51),
            'starterkit_accepted' => $usersWithProgress->where('progress', 52),
            'contract_signed' => $usersWithProgress->where('progress', 68),
            'compensation_signed' => $usersWithProgress->where('progress', 85),
            'digital_account_given' => $usersWithProgress->where('progress', 100),
        ];

        return $notif;
    }


    public function inumber_status()
    {
        $count = $this->employeeInventoryNumber->count();
        if ($count >= 6) {
            $complete_date = $this->employeeInventoryNumber->last()?->created_at;
            return [
                'status' => true,
                'date' => $complete_date,
            ];
        } else {
            return [
                'status' => false,
                'date' => null,
            ];
        }
    }

    public function inventory_set_status()
    {
        $nonSpecific = $this->nonSpecificInventories();
        $job = $this->firstEmployeeJob;
        // dd($nonSpecific);
        if ($nonSpecific->isNotEmpty() && $job) {
            $date = $nonSpecific
                ->where('employee_job_id', $job->id)
                ->reverse()
                ->first()?->updated_at;

            return [
                'status' => $date !== null,
                'date' => $date,
            ];
        }

        return ['status' => false, 'date' => null];
    }


    public function inventory_acc_status()
    {
        $nonSpecific = $this->nonSpecificInventories();
        $job = $this->firstEmployeeJob;
        // dd($nonSpecific);
        if ($nonSpecific->isNotEmpty() && $job) {
            $active = $nonSpecific->reject(function ($item) {
                return in_array($item->status, ['Dikembalikan', 'Dinonaktifkan']);
            });

            $accepted = $active->filter(fn($item) => $item->status === 'Diterima');

            $isComplete = $active->count() === $accepted->count();

            return [
                'status' => $isComplete,
                'date' => $isComplete ? $accepted->last()?->updated_at : null,
            ];
        }

        return ['status' => false, 'date' => null];
    }

    public function personal_status()
    {
        $hasDetail = $this->employeeDetail && !$this->employeeDetail->is_draft;
        $hasEducation = $this->employeeEducations->isNotEmpty();
        $hasBank = $this->employeeBanks->isNotEmpty();
        $hasDocs = $this->employeeDocs->isNotEmpty();

        $isComplete = $hasDetail && $hasEducation && $hasBank && $hasDocs;

        return [
            'status' => $isComplete,
            'date' => $isComplete ? $this->employeeDocs->max('created_at') : null,
        ];
    }

    /**
     * Common reusable filter
     */
    protected function nonSpecificInventories()
    {
        static $specificItems = [
            'bpjs kesehatan',
            'bpjs tk',
            'user account great day',
            'user account e-slip'
        ];

        return $this->inventory->filter(function ($item) use ($specificItems) {
            return !in_array(strtolower($item->item->item_name), $specificItems);
        });
    }


    public function rule()
    {
        $rule = null;
        if ($this->dakarRole) {
            $employeeJob = optional($this->employeeJob)->last();
            if ($employeeJob) {
                $ruleQuery = InventoryRule::where('dakar_role_id', $this->getRoleId());
                if ($employeeJob->department_id) {
                    $ruleQuery->whereHas('department', function ($q) use ($employeeJob) {
                        $q->where('dakar_departments.id', $employeeJob->department_id);
                    });
                }
                $rule = $ruleQuery->first();
            }
        }
        return $rule;
    }

    public function items()
    {
        $rule = $this->rule();
        return $rule ? $rule->items->map(function ($item) {
            $size = '';
            if (strpos(strtolower($item->item_name), 'eragam esd') !== false) {
                $size = $this->employeeDetail->esd_uniform_size ?? 'Default Size';
            } elseif (strpos(strtolower($item->item_name), 'sepatu esd') !== false) {
                $size = $this->employeeDetail->esd_shoes_size ?? 'Default Size';
            } elseif (strpos(strtolower($item->item_name), 'biru') !== false) {
                $size = $this->employeeDetail->blue_uniform_size ?? 'Default Size';
            } elseif (strpos(strtolower($item->item_name), 'polo') !== false) {
                $size = $this->employeeDetail->polo_shirt_size ?? 'Default Size';
            } elseif (strpos(strtolower($item->item_name), 'safety') !== false) {
                $size = $this->employeeDetail->safety_shoes_size ?? 'Default Size';
            } else {
                $size = '-';
            }
            return [
                'id' => $item->id,
                'name' => $item->item_name,
                'size' => $size,
            ];
        }) : [];
    }
}
