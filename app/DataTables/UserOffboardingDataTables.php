<?php

namespace App\DataTables;

use App\Models\DakarRole;
use App\Models\Offboarding;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class UserOffboardingDataTables extends DataTable
{
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
            ->addColumn('resign_date', function ($user) {
                return optional($user->offboarding)->resign_date ?? 'No resign date';
            })
            ->addColumn('actions', function ($row) {
                $offboardingUrl = route('users.index.offboarding.detail', $row->id);
                $buttons = '<a title="Detail Offboarding" href="' . $offboardingUrl . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-briefcase-off fs-6"></i></a>';
                return $buttons;
            })
            ->rawColumns(['actions'])
        ;
    }

    public function query(User $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['department', 'latestEmployeeJob.position', 'offboarding'])
            ->whereDoesntHave('dakarRole', function ($q) {
                $q->whereIn('role_name', ['admin', 'admin 2', 'admin 3']);
            })
            ->whereHas('latestEmployeeJob', function ($query) {
                $query->where('employment_status', false);
            })
            ->select('users.*');

        if ($status = request()->input('statusFilter')) {
            $karyawanRole = DakarRole::where('role_name', $status)->first();
            if ($karyawanRole) {
                $query->whereHas('dakarRole', function ($q) use ($karyawanRole) {
                    $q->where('dakar_role_user.dakar_role_id', $karyawanRole->id);
                });
            }
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
            // Column::make('email')->title('Email'),
            Column::make('department_name')
                ->title('Department')
                ->searchable()
                ->orderable(),
            Column::make('resign_date')
                ->title('Termination Date')
                ->searchable()
                ->orderable(),
            Column::computed('actions')
                ->title('Actions')
        ];
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
