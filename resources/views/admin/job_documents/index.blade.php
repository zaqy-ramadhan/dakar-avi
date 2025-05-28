@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2 class="mb-4">Job Documents</h2>
        <div class="d-flex align-items-center justify-content-between">
            <ul class="nav nav-pills" id="statusTabs">
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
            <div class="d-flex align-items-center">
                <div class="col mb-3 me-2">
                    <select name="activeFilter" class="form-control" id="activeFilter">
                        <option value="">Tampilkan Semua</option>
                        <option value="true">Aktif</option>
                        <option value="false">Nonaktif</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card" style="overflow-x: auto; width: 100%;">
            {!! $dataTable->table() !!}
        </div>
    </div>
@endsection

@push('scripts')
    {{-- {{ $dataTable->scripts(attributes: ['type' => 'module']) }} --}}
    <script>
        $(document).ready(function() {
            let statusFilter = "";
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route(request()->route()->getName()) }}",
                    data: function(d) {
                        d.roleFilter = statusFilter;
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

            $('#statusTabs').on('click', '.nav-link', function(e) {
                e.preventDefault();

                $('.nav-link').removeClass('active');
                $(this).addClass('active');

                statusFilter = $(this).data('status');
                table.ajax.reload(null, false);
            });

            $('#activeFilter').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endpush
