<?php

namespace App\DataTables;

use App\Models\DakarRole;
use App\Models\Offboarding;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserOffboardingDataTables extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->setRowId('id')
            // Menggunakan relasi yang sudah di-load di query() agar lebih cepat
            ->addColumn('department_name', function ($user) {
                return $user->latestEmployeeJob->department->department_name ?? 'No department';
            })
            ->filterColumn('department_name', function ($query, $keyword) {
                $query->whereHas('latestEmployeeJob.department', function ($q) use ($keyword) {
                    $q->where('department_name', 'LIKE', "%{$keyword}%");
                });
            })
            ->addColumn('resign_date', function ($user) {
                return $user->offboarding->resign_date ?? '-';
            })
            // Perbaikan Order Column menggunakan Subquery agar bisa di-sort oleh Database
            ->orderColumn('resign_date', function ($query, $direction) {
                $query->orderByRaw(
                    "COALESCE(CAST((SELECT TOP 1 resign_date FROM dakar_offboarding WHERE user_id = users.id ORDER BY resign_date DESC) AS DATE), '9999-12-31') " . $direction
                );
            })
            ->addColumn('reason', function ($user) {
                return $user->offboarding->reason ?? '-';
            })
            ->orderColumn('reason', function ($query, $direction) {
                $query->orderBy(
                    Offboarding::select('reason')
                        ->whereColumn('user_id', 'users.id')
                        ->latest('reason')
                        ->limit(1),
                    $direction
                );
            })
            ->filterColumn('reason', function ($query, $keyword) {
                $query->whereHas('offboarding', function ($q) use ($keyword) {
                    $q->where('reason', 'LIKE', "%{$keyword}%");
                });
            })
            ->addColumn('actions', function ($row) {
                $offboardingUrl = route('users.index.offboarding.detail', $row->id);
                return '<a title="Detail Offboarding" href="' . $offboardingUrl . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-briefcase-off fs-6"></i></a>';
            })
            ->rawColumns(['actions']);
    }

    public function query(User $model): QueryBuilder
    {
        // Gunakan where berkelompok (nested where) untuk logika OR agar tidak merusak filter lainnya
        return $model->newQuery()
            ->with(['latestEmployeeJob.department', 'latestEmployeeJob.position', 'offboarding'])
            ->select('users.*')
            ->whereDoesntHave('dakarRole', function ($q) {
                $q->whereIn('role_name', ['admin', 'admin 2', 'admin 3']);
            })
            ->where(function ($q) {
                $q->whereHas('offboarding')
                  ->orWhereHas('latestEmployeeJob', function ($sub) {
                      $sub->where('employment_status', false);
                  });
            })
            ->when(request('statusFilter'), function ($q, $status) {
                $q->whereHas('dakarRole', function ($roleQuery) use ($status) {
                    $roleQuery->where('role_name', $status);
                });
            });
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('datatable')
            ->columns($this->getColumns())
            ->minifiedAjax() // Penting agar filter input terkirim via Ajax
            ->orderBy(4, 'desc') // Default sort ke Termination Date
            ->selectStyleSingle()
            ->parameters([
                'responsive' => true,
                'autoWidth' => false
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('No')->width(30),
            Column::make('npk')->title('NPK'),
            Column::make('fullname')->title('Name'),
            Column::make('department_name')->title('Department'),
            Column::make('resign_date')->title('Termination Date'),
            Column::make('reason')->title('Termination Reason'),
            Column::computed('actions')->title('Actions')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }
}