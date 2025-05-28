@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <h2>{{ $page }}</h2>
        <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
            @if (Request::is('*karyawan*'))
                <select id="statusFilter" class="form-control" style="height: fit-content; width: fit-content;">
                    <option value="">Tampilkan semua karyawan</option>
                   @foreach ($jobStatus as $status )
                       <option value="{{ $status->job_status_name }}">{{ Str::ucfirst($status->job_status_name) }}</option>
                   @endforeach
                </select>
            @endif
            @if (!Request::is('*boarding*'))
                <a class="btn btn-primary mb-3 float-end" href="{{ route('admin.user.create') }}">Create User</a>
            @endif
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
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route(request()->route()->getName()) }}",
                    data: function(d) {
                        d.role = @json($type ?? null);
                        d.statusFilter = $('#statusFilter').val();
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
                        data: 'position_name',
                        name: 'position_name',
                        searchable: true,
                        orderable: true
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
                        data: 'actions',
                        name: 'actions'
                    }
                ]
            });

            $('#statusFilter').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endpush
