@extends('layouts.admin')

@section('content')
    <div class="card" style="border-radius: 20px">
        <div class="card-header">
            <p class="fs-8 fw-bold">Employee Data</p>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-2">
                <div class="col-md-2">
                    <select name="department" class="form-select">
                        <option value="">Select Department</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="gender" class="form-select">
                        <option value="">Gender</option>
                        <option value="0">Male</option>
                        <option value="1">Female</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="education" class="form-select">
                        <option value="">Education</option>
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMA">SMA</option>
                        <option value="D3">D3</option>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                    </select>
                </div>
                <div class="col-md-2">
                    {{-- <input type="text" name="employment_status" class="form-control" placeholder="Employment Status"> --}}
                    <select name="employment_status" class="form-select" id="">
                        <option value="">Select Employment Status</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->role_name }}">{{ Str::ucfirst($role->role_name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="golongan" class="form-select" id="">
                        <option value="">Select Golongan</option>
                        @foreach ($golongan as $sub)
                            <option value="{{ $sub->golongan_name }}">{{ $sub->golongan_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="job_status" class="form-select" id="">
                        <option value="">Select Job Status</option>
                        @foreach ($jobStatus as $status)
                            <option value="{{ $status->job_status_name }}">{{ Str::ucfirst($status->job_status_name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="job_type" class="form-select" id="">
                        <option value="">Select Job Type</option>
                        @foreach ($jobType as $type)
                            <option value="{{ $type->job_type_name }}">{{ $type->job_type_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="status" class="form-control">
                        <option value="">Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from"  title="Date Start"
                        value="{{ \Carbon\Carbon::now()->format('Y-m') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" title="Date End"
                        value="{{ \Carbon\Carbon::now()->format('Y-m') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter fs-4"></i> Filter</button>
                </div>
                <div class="col-md-1">
                    <button type="button" id="resetFilters" class="btn btn-secondary w-100"><i
                            class="ti ti-reload fs-4"></i> Reset</button>
                </div>
                <div class="col-auto d-flex w-auto">
                    <button type="button" id="exportExcel" class="btn btn-success w-fit"><i
                            class="ti ti-file-spreadsheet fs-4"></i>Export Excel</button>
                </div>
                <div class="col-auto d-flex w-auto">
                    <button type="button" id="exportExcelMin" class="btn btn-primary w-fit"><i
                            class="ti ti-file-spreadsheet fs-4"></i>Export Excel Min</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card" style="border-radius: 20px; overflow-x: auto; width: 100%;">
        <table id="datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NPK</th>
                    <th>Full Name</th>
                    <th>Age</th>
                    <th>LOS</th>
                    <th>Gender</th>
                    <th>Education</th>
                    <th>Join Date</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Duration</th>
                    <th>Department</th>
                    {{-- <th>Position</th> --}}
                    <th>Employment Status</th>
                    <th>Job Status</th>
                    <th>Job Type</th>
                    <th>Gol</th>
                    <th>Status</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('employee-detail') }}",
                    data: function(d) {
                        d.department = $('select[name=department]').val();
                        d.gender = $('select[name=gender]').val();
                        d.employment_status = $('select[name=employment_status]').val();
                        d.golongan = $('select[name=golongan]').val();
                        d.job_status = $('select[name=job_status]').val();
                        d.job_type = $('select[name=job_type]').val();
                        d.status = $('select[name=status]').val();
                        d.education = $('select[name=education]').val();
                        d.date_from = $('input[name=date_from]').val();
                        d.date_to = $('input[name=date_to]').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'npk',
                        name: 'npk',
                    },
                    {
                        data: 'fullname',
                        name: 'fullname'
                    },
                    {
                        data: 'age',
                        name: 'age'
                    },
                    {
                        data: 'LOS',
                        name: 'LOS'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'education',
                        name: 'education'
                    },
                    {
                        data: 'join_date',
                        name: 'join_date'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'duration',
                        name: 'duration'
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    // {
                    //     data: 'position',
                    //     name: 'position'
                    // },
                    {
                        data: 'employment_status',
                        name: 'employment_status'
                    },
                    {
                        data: 'job_status',
                        name: 'job_status'
                    },
                    {
                        data: 'job_type',
                        name: 'job_type'
                    },
                    {
                        data: 'gol',
                        name: 'gol'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active'
                    }
                ],
                columnDefs: [
                    {
                        targets: 7,
                        data: 'join_date',
                        type: 'num',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return row.join_date_display;
                            }
                            return data;
                        }
                    },
                    {
                        targets: 8,
                        data: 'start_date',
                        type: 'num',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return row.start_date_display;
                            }
                            return data;
                        }
                    },
                    {
                        targets: 9,
                        data: 'end_date',
                        type: 'num',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return row.end_date_display;
                            }
                            return data;
                        }
                    }
                ]
            });

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

            $('#resetFilters').on('click', function() {
                $('#filterForm')[0].reset();
                table.ajax.reload();
            });

            // Add export Excel
            $('#exportExcel').on('click', function() {
                var params = $('#filterForm').serialize();
                if (params.length > 0) {
                    params += '&export=excel';
                } else {
                    params = 'export=excel';
                }
                window.location.href = "{{ route('employee-detail') }}" + "?" + params;
            });

            $('#exportExcelMin').on('click', function() {
                var params = $('#filterForm').serialize();
                if (params.length > 0) {
                    params += '&export_min=excel';
                } else {
                    params = 'export_min=excel';
                }
                window.location.href = "{{ route('employee-detail') }}" + "?" + params;
            });

            function initSelect2(element) {
                element.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                });
            }

            initSelect2($('select[name=department]'))
            initSelect2($('select[name=gender]'))
            initSelect2($('select[name=status]'))
            initSelect2($('select[name=employment_status]'))
            initSelect2($('select[name=job_status]'))
            initSelect2($('select[name=golongan]'))
            initSelect2($('select[name=job_type]'))
            initSelect2($('select[name=education]'))
        })
    </script>
@endpush
