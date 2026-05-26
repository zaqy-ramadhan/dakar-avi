<?php

namespace App\DataTables;

use App\Models\DakarRole;
use App\Models\EmployeeInventoryNumber;
use App\Models\EmployeeJob;
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
                return $user->firstEmployeeJob?->position?->position_name ?? 'No Position';
            })

            ->filterColumn('position_name', function ($query, $keyword) {
                $query->whereHas('firstEmployeeJob.position', function ($q) use ($keyword) {
                    $q->whereRaw("LOWER(position_name) LIKE ?", ["%" . strtolower($keyword) . "%"]);
                });
            })

            ->orderColumn('position_name', function ($query, $direction) {
                $query->orderBy(
                    EmployeeJob::select('dakar_positions.position_name')
                        ->join('dakar_positions', 'dakar_positions.id', '=', 'dakar_employee_job.position_id')
                        ->whereColumn('dakar_employee_job.user_id', 'users.id')
                        ->limit(1),
                    $direction
                );
            })

            ->addColumn('start_date', function ($user) {
                return $user->firstEmployeeJob && $user->firstEmployeeJob->start_date
                    ? $user->firstEmployeeJob->start_date->isoFormat('D MMMM YYYY')
                    : 'No Data';
            })
            ->filterColumn('start_date', function ($query, $keyword) {
                $query->whereHas('firstEmployeeJob', function ($q) use ($keyword) {
                    $q->where('start_date', 'like', "%$keyword%");
                });
            })
            ->orderColumn('start_date', function ($query, $direction) {
                $query->orderByRaw(
                    "CAST((SELECT TOP 1 start_date FROM dakar_employee_job WHERE user_id = users.id AND job_sequence = 1) AS DATE) " . $direction
                );
            })
            ->addColumn('end_date', function ($user) {
                return $user->firstEmployeeJob && $user->firstEmployeeJob->end_date
                    ? $user->firstEmployeeJob->end_date->isoFormat('D MMMM YYYY')
                    : 'No Data';
            })
            ->filterColumn('end_date', function ($query, $keyword) {
                $query->whereHas('firstEmployeeJob', function ($q) use ($keyword) {
                    $q->where('end_date', 'like', "%$keyword%");
                });
            })
            ->orderColumn('end_date', function ($query, $direction) {
                $query->orderByRaw(
                    "COALESCE(CAST((SELECT TOP 1 end_date FROM dakar_employee_job WHERE user_id = users.id AND job_sequence = 1) AS DATE), '9999-12-31') " . $direction
                );
            })

            ->addColumn('checklist', function ($user) {
                return $user->firstEmployeeJob ? $user->firstEmployeeJob->onboarding_progress . '%' : 'N/A';
            })

            ->orderColumn('checklist', function ($query, $order) {
                $query->leftJoin('dakar_employee_job', 'users.id', '=', 'dakar_employee_job.user_id')
                    ->orderBy('dakar_employee_job.onboarding_progress', $order);
            })

            ->addColumn('actions', function ($row) {
                $onboardingUrl = route('users.index.onboarding.detail', $row->id);
                $deleteUrl = route('users.destroy', $row->id);
                $currentRoute = request()->route()->getName();

                $buttons = '';

                if ($currentRoute === "users.index.onboarding") {
                    $buttons .= '<a title="Detail Onboarding" href="' . $onboardingUrl . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-briefcase fs-6"></i></a>';
                }

                $buttons .=
                    '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">
                    ' . csrf_field() . '
                    ' . method_field('POST') . '
                    <button type="submit" title="Delete User" class="btn btn-sm btn-outline-danger m-1" onclick="return confirm(\'Are you sure?\')"><i class="ti ti-trash fs-6"></i></button>
                </form>';

                return $buttons;
            })
            ->rawColumns(['actions']);
    }


    public function query(User $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->select('users.*')
            // ->with([
            //     'firstEmployeeJob' => function ($q) {
            //         $q->where('employment_status', true)
            //         ->where('is_onboarding_completed', false);
            //     }
            // ])
            ->whereDoesntHave('dakarRole', function ($q) {
                $q->whereIn('role_name', ['admin', 'admin 2', 'admin 3', 'admin 4']);
            })
            // ->where(function ($query) {
            //     $query->doesntHave('firstEmployeeJob')
            //         ->orWhereHas('firstEmployeeJob', function($q2){
            //             $q2->where('employment_status', true)
            //             ->where('is_onboarding_completed', false);
            //         });
            // })
            ->where(function ($q) {
                $q->doesntHave('employeeJob')
                ->orWhereHas('firstEmployeeJobIncomplete')
                ->orWhereHas('firstPkwtIncomplete');
            })
            ;

        if ($status = request()->input('statusFilter')) {
            $karyawanRoleId = DakarRole::where('role_name', $status)->value('id');
            if ($karyawanRoleId) {
                $query->whereHas('dakarRole', function ($q) use ($karyawanRoleId) {
                    $q->where('dakar_role_user.dakar_role_id', $karyawanRoleId);
                });
            }
        }

        // $query->where('firstEmployeeJob', function ($q) {
        //     $q->where('employment_status', false);
        // });

        if (request()->input('progressFilter') === 'true') {
            $query->whereHas('employeeDetail', function ($q) {
                $q->where('is_draft', 0);
            })->whereHas('firstEmployeeJob', function ($q) {
                $q->where('is_onboarding_completed', false);
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
