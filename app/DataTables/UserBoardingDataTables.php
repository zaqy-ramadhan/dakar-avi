<?php

namespace App\DataTables;

use App\Models\DakarRole;
use App\Models\EmployeeInventoryNumber;
use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Monolog\Handler\NullHandler;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserBoardingDataTables extends DataTable
{
    protected $type;

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->setRowId('id')
            ->addColumn('position_name', function ($user) {
                return optional($user->employeeJob->sortByDesc('start_date')->first())->position->position_name ?? 'No Position';
            })
            ->filterColumn('position_name', function ($query, $keyword) {
                $query->whereHas('employeeJob.position', function ($q) use ($keyword) {
                    $q->whereRaw("LOWER(position_name) LIKE ?", ["%" . strtolower($keyword) . "%"]);
                });
            })
            ->addColumn('start_date', function ($user) {
                $latestJob = $user->employeeJob->last();
                return $latestJob && $latestJob->start_date ? $latestJob->start_date->isoFormat('D MMMM YYYY') : 'No Data';
            })
            ->filterColumn('start_date', function ($query, $keyword) {
                $query->whereHas('employeeJob', function ($q) use ($keyword) {
                    $q->where('start_date', 'like', "%$keyword%");
                });
            })
            ->addColumn('end_date', function ($user) {
                $latestJob = $user->employeeJob->last();
                return $latestJob && $latestJob->end_date ? $latestJob->end_date->isoFormat('D MMMM YYYY') ?? 'No data' : 'No Data';
            })
            ->filterColumn('end_date', function ($query, $keyword) {
                $query->whereHas('employeeJob', function ($q) use ($keyword) {
                    $q->where('end_date', 'like', "%$keyword%");
                });
            })
            ->addColumn('checklist', function ($user) {
                $progress = $user->progressOnboardingAdmin()['progress'] >= 10 ?? false;
                if ($progress) {
                    return $user->progressOnboardingAdmin()['progress'] . '%';
                } else {
                    return 'N/A';
                }
            })
            ->orderColumn('checklist', function ($query, $order) {
                $query->orderBy('created_at', $order);
            })
            // ->addColumn('pre_onboarding', function ($user) {
            //     $job = $user->firstEmployeeJob;
            //     if (!$job || !$job->start_date) return 'N/A';

            //     $startDate = Carbon::parse($job->start_date)->startOfDay();
            //     $deadline = $startDate->copy()->subDay(); // H-1
            //     $completionDate = optional($user->employeeDocs)->last()?->created_at;

            //     if ($completionDate) {
            //         $completion = Carbon::parse($completionDate)->startOfDay();
            //         if ($completion->lte($deadline)) {
            //             return '<span class="badge bg-success">On Time</span>';
            //         } else {
            //             $overdueDays = $deadline->diffInDays($completion);
            //             return '<span class="badge bg-danger">Overdue ' . $overdueDays . ' hari</span>';
            //         }
            //     } else {
            //         $now = Carbon::now()->startOfDay();
            //         if ($now->lte($deadline)) {
            //             return '<span class="badge bg-warning">Deadline : ' . $deadline->format('d M Y') . '</span>';
            //         } else {
            //             $overdueDays = $deadline->diffInDays($now);
            //             return '<span class="badge bg-danger">Overdue ' . $overdueDays . ' hari</span>';
            //         }
            //     }
            // })
            // ->addColumn('onboarding', function ($user) {
            //     $job = $user->firstEmployeeJob;
            //     if (!$job || !$job->start_date) return 'N/A';

            //     $startDate = Carbon::parse($job->start_date)->startOfDay();
            //     $deadline = $startDate->copy();
            //     $isCompleted = $user->progressOnboardingAdmin()['progress'] === 68 ;
            //     $completionDate = null;
            //     if ($isCompleted) {
            //         $completionDate = optional($job?->inventory)->where('employee_job_id', $job?->id)?->last()?->created_at ?? null;
            //     }

            //     if ($completionDate) {
            //         $completion = Carbon::parse($completionDate)->startOfDay();
            //         if ($completion->lte($deadline)) {
            //             return '<span class="badge bg-success">On Time</span>';
            //         } else {
            //             $overdueDays = $deadline->diffInDays($completion);
            //             return '<span class="badge bg-danger">Overdue ' . $overdueDays . ' hari</span>';
            //         }
            //     } else {
            //         $now = Carbon::now()->startOfDay();
            //         if ($now->lte($deadline)) {
            //             return '<span class="badge bg-warning">Deadline : ' . $deadline->format('d M Y') . '</span>';
            //         } else {
            //             $overdueDays = $deadline->diffInDays($now);
            //             return '<span class="badge bg-danger">Overdue ' . $overdueDays . ' hari</span>';
            //         }
            //     }
            // })
            // ->addColumn('post_onboarding', function ($user) {
            //     $isKaryawan = $user->dakarRole->contains(function ($role) {
            //         return strtolower($role->role_name) === 'karyawan';
            //     });
            //     if (!$isKaryawan) return 'N/A';

            //     $job = $user->firstEmployeeJob;
            //     if (!$job || !$job->start_date) return 'N/A';

            //     $startDate = Carbon::parse($job->start_date)->startOfDay();
            //     $deadline = $startDate->copy()->addMonth();
            //     $completionDate = optional($user->employeeInventoryNumber)->last()?->created_at;

            //     if ($completionDate) {
            //         $completion = Carbon::parse($completionDate)->startOfDay();
            //         if ($completion->lte($deadline)) {
            //             return '<span class="badge bg-success">On Time</span>';
            //         } else {
            //             $overdueDays = $deadline->diffInDays($completion);
            //             return '<span class="badge bg-danger">Overdue ' . $overdueDays . ' hari</span>';
            //         }
            //     } else {
            //         $now = Carbon::now()->startOfDay();
            //         if ($now->lte($deadline)) {
            //             return '<span class="badge bg-warning">Deadline : ' . $deadline->format('d M Y') . '</span>';
            //         } else {
            //             $overdueDays = $deadline->diffInDays($now);
            //             return '<span class="badge bg-danger">Overdue ' . $overdueDays . ' hari</span>';
            //         }
            //     }
            // })

            ->addColumn('actions', function ($row) {
                $detailUrl = route('users.details.update', $row->id);
                $onboardingUrl = route('users.index.onboarding.detail', $row->id);
                $offboardingUrl = route('users.index.offboarding.detail', $row->id);
                $deleteUrl = route('users.destroy', $row->id);

                $currentRoute = request()->route()->getName();

                if ($currentRoute === "users.index.onboarding") {
                    $buttons = '<a title="Detail Onboarding" href="' . $onboardingUrl . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-briefcase fs-6"></i></a>';
                }

                $buttons .=
                    '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">
                    ' . csrf_field() . '
                    ' . method_field('POST') . '
                    <button type="submit" title="Delete User" class="btn btn-sm btn-outline-danger m-1" onclick="return confirm(\'Are you sure?\')"><i class="ti ti-trash fs-6"></i></button>
                </form>';

                return $buttons;
            })
            ->rawColumns(['actions', 'pre_onboarding', 'onboarding', 'post_onboarding', 'create_employee', 'employment_data', 'starter_kit', 'greatday', 'eslip', 'bpjskes', 'bpjstk']);
        ;
    }

    public function query(User $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['employeeJob.position', 'latestEmployeeJob'])
            ->select('users.*')
            ->whereDoesntHave('dakarRole', function ($q) {
                $q->whereIn('role_name', ['admin', 'admin 2', 'admin 3', 'admin 4']);
            });

        $users = $query;

        if ($status = request()->input('statusFilter')) {
            $karyawanRole = DakarRole::where('role_name', $status)->first();
            if ($karyawanRole) {
                $query->whereHas('dakarRole', function ($q) use ($karyawanRole) {
                    $q->where('dakar_role_user.dakar_role_id', $karyawanRole->id);
                });
            }
        }

        $query->whereDoesntHave('firstEmployeeJob', function ($q) {
            $q->where('employment_status', false);
        });

        if (request()->input('progressFilter') === 'true') {
            $users->whereHas('employeeDetail', function ($q) {
                $q->where('is_draft', 0);
            });

            //  $users->whereHas('employeeJob', function ($q) {
            //     $q->where('is_onboarding_completed', 0);
            // });


            $users = $users->get();
            $users = $users->filter(function ($user) {
                $progress = $user->progressOnboardingAdmin()['progress'];
                return $progress <= 100;
            });
        }

        return (new User())->newQuery()->whereIn('id', $users->pluck('id'));
    }


    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('datatable')
            ->columns($this->getColumns())
            ->responsive(true)
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('No')
                ->searchable(false)
                ->orderable(false),
            Column::make('npk')->title('NPK'),
            Column::make('fullname')->title('Name'),
            Column::make('position_name')
                ->searchable()
                ->orderable(),
            Column::make('start_date'),
            Column::make('end_date'),
            Column::make('checklist')->title('Onboarding Progress'),
            // Column::make('pre_onboarding')->title('Pre Onboarding'),
            // Column::make('onboarding')->title('Onboarding'),
            // Column::make('post_onboarding')->title('Post Onboarding'),
            Column::computed('actions')
                ->title('Actions')
        ];
    }

    // Menentukan nama file untuk ekspor
    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
