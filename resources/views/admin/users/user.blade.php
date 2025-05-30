@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card mb-3" style="border-radius: 20px">
            <div class="card-header">
                <p class="fs-8 fw-bold">{{ $page ?? '-' }}</p>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    @if (Request::is('*karyawan*'))
                        <div class="flex-grow-2 me-2" style="min-width: 200px;">
                            <select id="statusFilter" class="form-control w-100">
                                <option value="">Tampilkan semua karyawan</option>
                                @foreach ($jobStatus as $status)
                                    <option value="{{ $status->job_status_name }}">
                                        {{ Str::ucfirst($status->job_status_name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if (!Request::is('*boarding*'))
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <div class="flex-grow-2" style="min-width: 150px;">
                                <select name="activeFilter" class="form-control" id="activeFilter">
                                    <option value="">Tampilkan Semua</option>
                                    <option value="true" selected>Aktif</option>
                                    <option value="false">Nonaktif</option>
                                </select>
                            </div>

                            <a class="btn btn-primary" href="{{ route('import.index') }}">
                                <i class="ti ti-file-spreadsheet me-2"></i>Import Excel
                            </a>
                        </div>
                    @elseif (Request::is('*onboarding*') || Request::is('*offboarding*'))
                        <div class="d-flex flex-wrap align-items-center gap-2 w-100">
                            <ul class="nav nav-pills me-3 flex-wrap" id="statusTabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-status="" href="#">Tampilkan Semua</a>
                                </li>
                                @foreach ($roles as $role)
                                    <li class="nav-item">
                                        <a class="nav-link" data-status="{{ $role->role_name }}" href="#">
                                            {{ Str::ucfirst($role->role_name) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            @if (Request::is('*onboarding*'))
                                <div class="mt-2 mt-lg-0 ms-auto">
                                    <a class="btn btn-primary" href="{{ route('admin.user.create') }}">Add Karyawan</a>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if (Request::is('*onboarding*'))
                        <div class="form-check form-switch ms-auto">
                            <input class="form-check-input" type="checkbox" id="progressFilterCheckbox"
                                @if ((bool) request()->input('progressFilter')) checked @endif>
                            <label class="form-check-label" for="progressFilterCheckbox">Filter Onboarding Progress</label>
                        </div>
                    @endif
                </div>
            </div>

        </div>
        <div class="card" style="overflow-x: auto; width: 100%; border-radius: 20px">
            {!! $dataTable->table() !!}
        </div>
    </div>
    {{-- @dd(Route::currentRouteName()) --}}
@endsection

@push('scripts')
    @if (Request::is('*onboarding*'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let statusFilter = "";
                let progressFilter = {{ request()->input('progressFilter', false) ? 'true' : 'false' }};

                const table = $('#datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: {
                        url: "{{ route(request()->route()->getName()) }}",
                        data: function(d) {
                            d.statusFilter = statusFilter;
                            d.progressFilter = progressFilter;
                        }
                    },
                    order: [
                        [4, 'desc']
                    ],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            searchable: false,
                            orderable: false,
                            title: 'No.'
                        },
                        {
                            data: 'npk',
                            name: 'npk'
                        },
                        {
                            data: 'fullname',
                            name: 'fullname'
                        },
                        {
                            data: 'position_name',
                            name: 'position_name'
                        },
                        {
                            data: 'start_date',
                            name: 'start_date',
                            searchable: true,
                            orderable: true
                        },
                        {
                            data: 'end_date',
                            name: 'end_date',
                            searchable: true,
                            orderable: true
                        },
                        {
                            data: 'checklist',
                            name: 'checklist',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'actions',
                            name: 'actions'
                        }
                    ]
                });

                // Event delegation untuk menangani klik tab filter status
                $('#statusTabs').on('click', '.nav-link', function(e) {
                    e.preventDefault();

                    $('.nav-link').removeClass('active');
                    $(this).addClass('active');

                    statusFilter = $(this).data('status');
                    table.ajax.reload(null, false); // Reload data tanpa reset pagination
                });

                $('#progressFilterCheckbox').on('change', function() {
                    progressFilter = this.checked; // Update progressFilter based on checkbox state
                    table.ajax.reload(null, true); // Reload data tanpa reset pagination
                });
            });
        </script>
    @elseif(Request::is('*offboarding*'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let statusFilter = "";

                const table = $('#datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: {
                        url: "{{ route(request()->route()->getName()) }}",
                        data: function(d) {
                            d.statusFilter = statusFilter;
                        }
                    },
                    order: [
                        [4, 'desc']
                    ],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            searchable: false,
                            orderable: false,
                            title: 'No.'
                        },
                        {
                            data: 'npk',
                            name: 'npk'
                        },
                        {
                            data: 'fullname',
                            name: 'fullname'
                        },
                        {
                            data: 'department_name',
                            name: 'department_name'
                        },
                        {
                            data: 'resign_date',
                            name: 'resign_date',
                            searchable: true,
                            orderable: true
                        },
                        {
                            data: 'actions',
                            name: 'actions'
                        }
                    ]
                });

                $('#statusTabs').on('click', '.nav-link', function(e) {
                    e.preventDefault();

                    $('.nav-link').removeClass('active');
                    $(this).addClass('active');

                    statusFilter = $(this).data('status');
                    table.ajax.reload(null, false);
                });

            });
        </script>
    @else
        <script>
            $(document).ready(function() {
                var table = $('#datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route(request()->route()->getName()) }}",
                        data: function(d) {
                            d.role = @json($type ?? null);
                            d.statusFilter = $('#statusFilter').val();
                            d.activeFilter = $('#activeFilter').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            searchable: false,
                            orderable: false,
                            title: 'No.'
                        },
                        {
                            data: 'npk',
                            name: 'npk'
                        },
                        {
                            data: 'fullname',
                            name: 'fullname'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'department_name',
                            name: 'department_name',
                            searchable: true,
                            orderable: true
                        },
                        {
                            data: 'join_date'
                        },
                        {
                            data: 'latest_end_date'
                        },
                        {
                            data: 'type'
                        },
                        {
                            data: 'is_active',
                            // searchable: true,
                            // orderable: true
                        },
                        {
                            data: 'actions',
                            name: 'actions'
                        }
                    ]
                });

                $('#statusFilter').on('change', function() {
                    table.ajax.reload();
                });

                $('#activeFilter').on('change', function() {
                    // console.log('Active Filter:', $(this).val());
                    table.ajax.reload();
                });
            });
        </script>
    @endif
@endpush
