@extends('layouts.admin')

@section('content')
    <h2 class="mb-4">User Job Employment Documents</h2>

    @include('admin.users.profileCard')

    <div class="card" style="overflow-x: auto; width: 100%;">
        <table id="datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Level</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Duration</th>
                    <th>Job Type</th>
                    <th>Job Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
    {{-- {{ $dataTable->scripts(attributes: ['type' => 'module']) }} --}}
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('job-docs.details', $user->id) }}",
                },
                order: [
                    [1, 'desc']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false,
                        title: 'No.'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        visible: false
                    },
                    {
                        data: 'level',
                        name: 'level',
                        title: 'Level',
                        searchable: true,
                        orderable: true,
                    },
                    {
                        data: 'department',
                        name: 'department',
                        title: 'Department',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'position',
                        name: 'position',
                        title: 'Position',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                        title: 'Start Date',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'end_date',
                        name: 'end_date',
                        title: 'End Date',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'contract_duration',
                        name: 'contract_duration',
                        title: 'Contract Duration',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'job_type',
                        name: 'job_type',
                        title: 'Job Type',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'job_status',
                        name: 'job_status',
                        title: 'Job Status',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        title: 'Actions',
                        searchable: false,
                        orderable: false
                    }
                ]
            });
        });
    </script>
@endpush
