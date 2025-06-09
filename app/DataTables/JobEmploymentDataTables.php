<?php

namespace App\DataTables;

use App\Models\EmployeeJob;
use GuzzleHttp\Psr7\Query;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class JobEmploymentDataTables extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('level', function ($job) {
                return $job->level->level_name ?? 'N/A';
            })
            ->filterColumn('level', function ($query, $keyword) {
                $query->whereHas('level', function ($q) use ($keyword) {
                    $q->where('level_name', 'LIKE', "%{$keyword}%");
                });
            })
            ->addColumn('department', function ($job) {
                return $job->department->department_name ?? 'N/A';
            })
            ->filterColumn('department', function ($query, $keyword) {
                $query->whereHas('department', function ($q) use ($keyword) {
                    $q->where('department_name', 'LIKE', "%{$keyword}%");
                });
            })
            ->addColumn('section', function ($job) {
                return $job->section->section_name ?? 'N/A';
            })
            ->filterColumn('section', function ($query, $keyword) {
                $query->whereHas('section', function ($q) use ($keyword) {
                    $q->where('section_name', 'LIKE', "%{$keyword}%");
                });
            })
            ->addColumn('position', function ($job) {
                return $job->position->position_name ?? 'N/A';
            })
            ->filterColumn('position', function ($query, $keyword) {
                $query->whereHas('position', function ($q) use ($keyword) {
                    $q->where('position_name', 'LIKE', "%{$keyword}%");
                });
            })
            ->addColumn('start_date', function ($job) {
                return $job->start_date ? $job->start_date->format('d M Y') : 'N/A';
            })
            ->filterColumn('start_date', function ($query, $keyword) {
                $query->whereRaw("start_date LIKE ?", ["%{$keyword}%"]);
            })
            ->addColumn('resign_date', function ($job) {
                return $job->resign_date ? \Carbon\Carbon::parse($job->resign_date)->format('d M Y') : 'N/A';
            })
            ->filterColumn('resign_date', function ($query, $keyword) {
                $query->whereRaw("resign_date LIKE ?", ["%{$keyword}%"]);
            })
            ->addColumn('end_date', function ($job) {
                return $job->end_date ? $job->end_date->format('d M Y') : 'N/A';
            })
            ->filterColumn('end_date', function ($query, $keyword) {
                $query->whereRaw("end_date LIKE ?", ["%{$keyword}%"]);
            })
            ->addColumn('contract_duration', function ($job) {
                return $job->duration() ?? 'N/A';
            })
            ->addColumn('job_type', function ($job) {
                return $job->jobType->job_type_name ?? 'N/A';
            })
            ->filterColumn('job_type', function ($query, $keyword) {
                $query->whereHas('jobType', function ($q) use ($keyword) {
                    $q->where('job_type_name', 'LIKE', "%{$keyword}%");
                });
            })
            // ->addColumn('golongan', function ($job) {
            //     return $job->golongan->golongan_name ?? 'N/A';
            // })
            // ->filterColumn('golongan', function ($query, $keyword) {
            //     $query->whereHas('golongan', function ($q) use ($keyword) {
            //         $q->where('golongan_name', 'LIKE', "%{$keyword}%");
            //     });
            // })
            ->addColumn('sub_golongan', function ($job) {
                return $job->subGolongan->sub_golongan_name ?? 'N/A';
            })
            ->filterColumn('sub_golongan', function ($query, $keyword) {
                $query->whereHas('subGolongan', function ($q) use ($keyword) {
                    $q->where('sub_golongan_name', 'LIKE', "%{$keyword}%");
                });
            })
            ->addColumn('group', function ($job) {
                return $job->group->group_name ?? 'N/A';
            })
            ->filterColumn('group', function ($query, $keyword) {
                $query->whereHas('group', function ($q) use ($keyword) {
                    $q->where('group_name', 'LIKE', "%{$keyword}%");
                });
            })
            ->addColumn('line', function ($job) {
                return $job->line->line_name ?? 'N/A';
            })
            ->filterColumn('line', function ($query, $keyword) {
                $query->whereHas('line', function ($q) use ($keyword) {
                    $q->where('line_name', 'LIKE', "%{$keyword}%");
                });
            })
            ->addColumn('job_status', function ($job) {
                return Str::ucfirst($job->contract) ?? 'N/A';
            })
            ->filterColumn('job_status', function ($query, $keyword) {
                $query->where('job_status', 'LIKE', "%{$keyword}%");
            })
            ->addColumn('is_active', function ($job) {

                $status = $job->employment_status;

                if ($status == true) {
                    return '<span class="badge text-bg-success">Active</span>';
                } elseif ($status == false) {
                    return '<span class="badge text-bg-danger">Inactive</span>';
                } else {
                    return '<span class="badge text-bg-light">N/A</span>';
                }
            })
            ->addColumn('actions', function ($job) {
                $currentRoute = request()->input('route') ?? Route::currentRouteName();

                $previousJob = EmployeeJob::where('user_id', $job->user_id)
                    ->where('user_dakar_role', 'karyawan')
                    ->where('id', '<', $job->id)
                    ->orderBy('id', 'desc')
                    ->first();

                $skhkButton = $previousJob || ($job->user_dakar_role === 'karyawan' && $job->employment_status == false)
                    ? '<a title="SKSMK" href="' . route("user.skhk-pdf", $previousJob->id ?? $job->id) . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-hourglass-off fs-4"></i> SKSMK</a>'
                    : '';

                $sertifButton = in_array($job->user_dakar_role, ['pemagangan', 'internship']) && $job->employment_status == false ?
                    '<a title="Sertif ' . ucfirst($job->user_dakar_role) . '" target="_blank" href="' . route('sertif.pdf', $job->id) . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-certificate fs-4"></i> Sertifikat</a>'
                    : '';

                // Access control for wage & contract button
                $subGolongan = $job->subGolongan->sub_golongan_name ?? '';
                $userRole = Auth::user()->getRole();

                $showKontrakButton = false;
                $showWageButton = false;
                $showKompensasiButton = false;

                if (Auth::user()->id == $job->user_id) {
                    $showKontrakButton = true;
                } else {
                    if (in_array($subGolongan, ['4 A'])) {
                        if (in_array($userRole, ['admin', 'admin 2'])) {
                            $showWageButton = true;
                            $showKontrakButton = true;
                            $showKompensasiButton = true;
                        }
                    } elseif (in_array($subGolongan, [
                        '4 B',
                        '4 C',
                        '4 D',
                        '4 E',
                        '4 F',
                        '5 A',
                        '5 B',
                        '5 C',
                        '5 D',
                        '6 A',
                        '6 B',
                        '6 C',
                        '6 D'
                    ])) {
                        if ($userRole === 'admin') {
                            $showWageButton = true;
                            $showKontrakButton = true;
                            $showKompensasiButton = true;
                        }
                    } else {
                        if (in_array($userRole, ['admin', 'admin 2', 'admin 3'])) {
                            $showWageButton = true;
                            $showKontrakButton = true;
                            $showKompensasiButton = true;
                        }
                    }
                }

                $wageButton = '';
                if ($showWageButton) {
                    $wageButton = '<a title="Gaji & Tunjangan" href="' . route("job.wage.allowance", $job->id) . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-wallet fs-4"></i> Gaji & Tunjangan</a>';
                }

                $kontrakButton = '';
                if ($showKontrakButton) {
                    $kontrakButton = '<a title="Kontrak" href="' . route("user.kontrak-pdf", $job->id) . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-script fs-4"></i> Kontrak</a>';
                }

                $kompensasiButton = '';
                if ($showKompensasiButton) {
                    $kompensasiButton = '<a title="Kompensasi" href="' . route("user.kompensasi-pdf", $job->id) . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-presentation-analytics fs-4"></i> Kompensasi</a>';
                }

                $kerahasiaanButton =  $job->id === $job->user->firstEmployeeJob->id
                        ? '
                        <a title="Pernyataan Kerahasiaan" href="' . route("user.kerahasiaan-pdf", $job->id) . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-lock fs-4"></i> SPK</a>
                        '
                        : '';

                // dd($job->jobDoc, $job->jobDoc->isNotEmpty(), $job->employment_status);
                if ($job->jobDoc->isNotEmpty() || $job->employment_status == false) {
                    $deleteButton = '';
                } else {
                    $deleteButton = '
                        <a type="button" href="' . route('employee-jobs.edit', ["id" => $job->id, "prev" => $currentRoute]) . '" class="btn btn-sm btn-outline-warning m-1" title="Edit Job"><i class="ti ti-edit fs-4"></i> Edit</a>
                        <form action="' . route('job.destroy', $job->id) . '" method="POST">
                        ' . csrf_field() . '
                        ' . method_field('POST') . '
                        <button type="submit" class="btn btn-sm btn-outline-danger m-1" onclick="return confirm(\'Are you sure?\')"><i class="ti ti-trash fs-4"></i> Delete</button>
                        </form>';
                }

                $offboardButton = $job->employment_status == true
                    ? '<button type="button" class="btn btn-sm btn-outline-warning m-1" data-bs-toggle="modal" data-bs-target="#offboardingModal' . $job->id . '" title="Add Out Date"><i class="ti ti-briefcase-off fs-4"></i> Out Date</button>
                        
                        <!-- Offboarding Modal -->
                        <div class="modal fade" id="offboardingModal' . $job->id . '" tabindex="-1" aria-labelledby="offboardingModalLabel' . $job->id . '" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="offboardingModalLabel' . $job->id . '">Add Out Date</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <form class="mb-4"
                                  action=" ' . (route('employeeJob.resign', $job->id)) . ' "
                                  method="post">
                                  ' . csrf_field() . '
                                  <div class="col-sm-12 mb-3">
                                    <input type="date" class="form-control" id="resign_date_' . $job->id . '" name="resign_date"
                                      value="' . (optional(optional($job->user->offboarding)->resign_date)->format('Y-m-d') ?? '') . '"
                                      ' . (Auth::user()->getRole() != 'admin' ? 'readonly' : '') . '>
                                  </div>
                                  <button type="submit" class="btn btn-primary"
                                    ' . (Auth::user()->getRole() != 'admin' ? 'hidden' : '') . '>Submit</button>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>'
                    : '';



                if (in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3'])) {
                    if ($currentRoute === 'job-docs.details') {
                        if ($job->user_dakar_role === 'karyawan') {
                            return '
                                <div class="d-flex">
                                ' . $wageButton . '    
                                ' . $kontrakButton . '
                                ' . $kompensasiButton . '
                                    <a title="Paklaring" href="' . route("user.paklaring-pdf", $job->id) . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-circle-off fs-4"></i>Paklaring</a>
                                    ' . $skhkButton . '
                                </div>
                            ';
                        } else {
                            return '
                                <div class="d-flex">
                                    ' . $kontrakButton . '
                                </div>
                            ';
                        }
                    } elseif ($currentRoute === 'users.index.onboarding.detail') {
                            return '
                                <div class="d-flex">
                                    ' . $kontrakButton . '
                                    ' . $kompensasiButton . '
                                    ' . $kerahasiaanButton . '
                                    ' . $deleteButton . '
                                </div>
                            ';
                    } elseif ($currentRoute === 'users.index.offboarding.detail') {
                        return '
                            <div class="d-flex">
                                <a title="Paklaring" href="' . route("user.paklaring-pdf", $job->id) . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-circle-off fs-4"></i> Paklaring</a>
                                <a title="SKSMK" href="' . route("user.skhk-pdf", $job->id) . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-hourglass-off fs-4"></i> SKSMK</a>
                            </div>
                        ';
                    } elseif ($currentRoute === 'users.index.employment.detail') {
                        if ($job->user_dakar_role === 'karyawan') {
                            return '
                                <div class="d-flex">
                                    ' . $wageButton . '
                                    ' . $kontrakButton . '
                                    ' . $kerahasiaanButton . '
                                    ' . $kompensasiButton . '                                    
                                    ' . $skhkButton . '
                                    ' . $offboardButton . '
                                    ' . $deleteButton . '
                                </div>
                            ';
                        } else {
                            return '
                                <div class="d-flex">
                                    ' . $wageButton . '                                    
                                    ' . $kontrakButton . '
                                    ' . $kerahasiaanButton . '
                                    ' . $sertifButton . '
                                    ' . $offboardButton . '
                                    ' . $deleteButton . '
                                </div>
                            ';
                        }
                    }
                } elseif (Auth::user()->getRole() === 'admin 4') {
                    return '';
                } else {

                    if ($currentRoute === 'users.index.offboarding') {
                        return '
                        <div class="d-flex">
                            <a title="Paklaring" href="' . route("user.paklaring-pdf", $job->id) . '" class="btn btn-sm btn-outline-primary m-1"><i class="ti ti-circle-off fs-4"></i>Paklaring</a>
                            ' . $skhkButton . '
                        </div>';
                    }

                    // if ($job->id === $job->user->firstEmployeeJob->id) {
                    //     return '
                    //     <div class="d-flex">
                    //         ' . $kontrakButton . '
                    //         ' . $kerahasiaanButton . '
                    //         ' . $skhkButton . '
                    //         ' . $sertifButton .  '
                    //     </div>
                    // ';
                    // } 
                    else {
                        return '
                        <div class="d-flex">
                            ' . $kontrakButton . '
                            ' . $kerahasiaanButton . '
                            ' . $skhkButton . '
                            ' . $sertifButton .  '
                        </div>
                    ';
                    }
                }
            })

            ->rawColumns(['actions', 'is_active'])
            ->setRowId('id');
    }

    public function query(EmployeeJob $model): QueryBuilder
    {
        $userId = request()->route('id');

        // dd(request()->input('route'));

        $query = $model->newQuery()
            ->with(['user.firstEmployeeJob', 'position', 'level', 'jobType', 'line', 'golongan', 'subGolongan', 'group', 'jobDoc'])
            ->where('user_id', $userId)
            ->select('dakar_employee_job.*');

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('datatable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'desc');
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('No')
                ->searchable(false)
                ->orderable(false),
            Column::make('id'),
            Column::make('level')->title('Level'),
            Column::make('department')->title('Department'), // Kolom departemen
            Column::make('section')->title('Section'), // Kolom section
            Column::make('position')->title('Position'), // Kolom posisi
            Column::make('start_date')->title('Start Date')->searchable(true)->orderable(true), // Kolom start date
            Column::make('end_date')->title('End Date'), // Kolom end date
            Column::make('resign_date')->title('Out Date'), // Kolom end date
            Column::make('contract_duration')->title('Contract Duration'), // Kolom durasi kontrak
            Column::make('job_type')->title('Job Type'), // Kolom tipe pekerjaan
            // Column::make('golongan')->title('Golongan'), // Kolom golongan
            Column::make('sub_golongan')->title('Sub Golongan'), // Kolom golongan
            Column::make('group')->title('Group'),
            Column::make('line')->title('Line'),
            Column::make('job_status')->title('Job Status'),
            Column::make('is_active'),
            Column::computed('actions')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->orderable(false),
        ];
    }

    // Menentukan nama file untuk ekspor
    protected function filename(): string
    {
        return 'JobEmployment_' . date('YmdHis');
    }
}
