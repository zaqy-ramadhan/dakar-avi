<?php

namespace App\DataTables;

use App\Models\DakarRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class UserDataTables extends DataTable
{
    protected $type;

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->setRowId('id')
            ->addColumn('department_name', function ($user) {
                return optional($user->employeeJob->sortByDesc('start_date')->first())->department->department_name ?? 'No department';
            })
            ->filterColumn('department_name', function ($query, $keyword) {
                $query->whereHas('employeeJob.department', function ($q) use ($keyword) {
                    $q->whereRaw("LOWER(department_name) LIKE ?", ["%" . strtolower($keyword) . "%"]);
                });
            })
            ->orderColumn('department_name', function ($query, $order) {
                $query->leftJoin('dakar_employee_job as ej', 'users.id', '=', 'ej.user_id')
                      ->leftJoin('dakar_departments as d', 'ej.department_id', '=', 'd.id')
                      ->orderBy('d.department_name', $order);
            })            
            ->addColumn('position_name', function ($user) {
                return optional($user->employeeJob)->position->position_name ?? 'No Position';
            })
            ->filterColumn('position_name', function ($query, $keyword) {
                $query->whereHas('employeeJob.position', function ($q) use ($keyword) {
                    $q->whereRaw("LOWER(position_name) LIKE ?", ["%" . strtolower($keyword) . "%"]);
                });
            })
            ->addColumn('latest_end_date', function ($user) {
                $latestJob = $user->latestEmployeeJob;
                return optional($latestJob)->end_date ? $latestJob->end_date->format('Y-m-d') : '';
            })
            ->addColumn('type', function ($user) {
                $latestJob = $user->latestEmployeeJob;
                return optional($latestJob)->contract ?? 'N/A';
            })
            ->addColumn('is_active', function ($user) {
                $latestJob = $user->latestEmployeeJob;

                if (is_null($latestJob)) {
                    return '<span class="badge text-bg-light">N/A</span>';
                }

                $status = $latestJob->employment_status;

                if ($status == true) {
                    return '<span class="badge text-bg-success">Active</span>';
                } elseif ($status == false) {
                    return '<span class="badge text-bg-danger">Termination</span>';
                } else {
                    return '<span class="badge text-bg-light">N/A</span>';
                }
            })
            ->filterColumn('is_active', function ($query, $keyword) {
                if (strtolower($keyword) == 'active') {
                    $query->whereHas('latestEmployeeJob', function ($q) {
                        $q->where('employment_status', true);
                    });
                } elseif (strtolower($keyword) == 'inactive') {
                    $query->whereHas('latestEmployeeJob', function ($q) {
                        $q->where('employment_status', false);
                    });
                } elseif (strtolower($keyword) == 'n/a' || strtolower($keyword) == 'na') {
                    $query->whereDoesntHave('latestEmployeeJob')
                        ->orWhereHas('latestEmployeeJob', function ($q) {
                            $q->whereNull('employment_status');
                        });
                }
            })
            // ->orderColumn('is_active', function ($query, $order) {
            //     $query->leftJoin('dakar_employee_job as ej', 'users.id', '=', 'ej.user_id')
            //             ->select('users.*')
            //             ->orderByRaw("
            //                 CASE
            //                     WHEN ej.employment_status = 1 THEN 1
            //                     WHEN ej.employment_status = 0 THEN 2
            //                     ELSE 3
            //                 END $order
            //             ");
            // })
            ->addColumn('actions', function ($row) {
                $detailUrl = route('users.details.update', $row->id);
                $onboardingUrl = route('users.index.onboarding.detail', $row->id);
                $employmentUrl = route('users.index.employment.detail', $row->id);
                $offboardingUrl = route('users.index.offboarding.detail', $row->id);
                $jobDocsUrl = route('users.index.job.documents.details', $row->id);
                $currentRoute = request()->route()->getName();

                if (Auth::user()->getRole() == 'admin 4') {
                    $buttons = '<a href="' . $employmentUrl . '" class="btn btn-sm btn-outline-primary m-1" title="Employment"><i class="ti ti-script fs-6" ></i><a/>';
                } else {
                    $buttons = '
                    <a href="' . $detailUrl . '" class="btn btn-sm btn-outline-success m-1" title="User Details"><i class="ti ti-list-details fs-6"></i></a>
                    <a href="' . $employmentUrl . '" class="btn btn-sm btn-outline-primary m-1" title="Employment"><i class="ti ti-script fs-6" ></i><a/>       
                    <a href="' . $offboardingUrl . '" class="btn btn-sm btn-outline-warning m-1" title="Proceed Offboarding"><i class="ti ti-briefcase-off fs-6" ></i><a/>            
                    ';
                }

                return $buttons;
            })
            ->rawColumns(['actions', 'is_active'])
        ;
    }

    public function query(User $model): QueryBuilder
    {
        $roleId = null;
        $status = null;
        $active = null;

        $karyawanId = DakarRole::where('role_name', 'karyawan')->first()->id;

        $query = $model->newQuery()
            ->with(['department', 'employeeJob.position', 'latestEmployeeJob'])
            ->whereHas('employeeJob')
            ->select('users.*')
            ->whereDoesntHave('dakarRole', function ($q) {
                $q->whereIn('role_name', ['admin', 'admin 2', 'admin 3']);
            });
;

        if (request()->input('statusFilter')) {
            $status = request()->input('statusFilter');
            $query->whereHas('latestEmployeeJob', function ($q) use ($status) {
                $q->where('dakar_employee_job.job_status', $status);
            })->whereHas('dakarRole', function ($q) use ($karyawanId) {
                $q->where('dakar_role_user.dakar_role_id', $karyawanId);
            });
        }
        if (request()->input('activeFilter')) {
            $active = request()->input('activeFilter');
            $query->whereHas('latestEmployeeJob', function ($query) use ($active) {
                $query->where('employment_status', $active);
            });
        }
        if ($role = request()->input('roleFilter')) {
            $karyawanRole = DakarRole::where('role_name', $role)->first();
            if ($karyawanRole) {
                $query->whereHas('dakarRole', function ($q) use ($karyawanRole) {
                    $q->where('dakar_role_user.dakar_role_id', $karyawanRole->id);
                });
            }
        } elseif (request()->input('role')) {
            $rolename = request()->input('role');
            $roleId = DakarRole::where('role_name', $rolename)->first()->id ?? null;
        }

        if ($roleId) {
            $query->whereHas('dakarRole', function ($q) use ($roleId) {
                $q->where('dakar_role_user.dakar_role_id', $roleId);
            });
        }

        return $query;
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
            Column::make('email')->title('Email'),
            Column::make('position_name')
                ->title('Department')
                ->searchable()
                ->orderable(),
            Column::make('join_date'),
            Column::make('latest_end_date')
                ->title('End Date'),
            Column::make('type')
                ->title('Tipe')
                ->searchable(),
            Column::make('is_active')
                ->title('Status')
                ->searchable(),
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
