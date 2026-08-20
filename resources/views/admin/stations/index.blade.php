@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card mb-3" style="border-radius: 20px">
            <div class="card-header">
                <p class="fs-8 fw-bold">Stations</p>
            </div>
            <div class="card-body">
                <button class="btn btn-primary float-end" id="addStationBtn">Create Station</button>
            </div>
        </div>

        <table id="datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Station Name</th>
                    <th>Department</th>
                    <th>Is Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="stationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="stationForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add/Edit Station</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="station_id">
                        <div class="mb-3">
                            <label>Station Name</label>
                            <input type="text" id="station_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Department</label>
                            <select id="department_id" class="form-control" required>
                                <option value="">Select Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="">Is Active</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('stations.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'station_name',
                        name: 'station_name'
                    },
                    {
                        data: 'department.department_name',
                        name: 'department.department_name'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        render: function(data, type, row) {
                            var val = data === true || data === 'true' || data === 1 || data === '1';
                            if (type === 'display') {
                                return val
                                    ? '<span class="badge bg-success">Active</span>'
                                    : '<span class="badge bg-danger">Inactive</span>';
                            }
                            return val ? 'Active' : 'Inactive';
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        searchable: false,
                        orderable: false
                    }
                ]
            });

            // Open Modal for Adding Station
            $('#addStationBtn').click(function() {
                $('#station_id').val('');
                $('#station_name').val('');
                $('#department_id').val('');
                $('#is_active').prop('checked', false);
                $('#stationModal').modal('show');
                $('#modalTitle').text('Create station');
            });

            // Submit Form (Add/Edit)
            $('#stationForm').submit(function(e) {
                e.preventDefault();
                let id = $('#station_id').val();
                let url = id ? "{{ url('/admin/stations') }}/" + id : "{{ route('stations.store') }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        station_name: $('#station_name').val(),
                        department_id: $('#department_id').val(),
                        is_active: $('#is_active').is(':checked') ? 1 : 0,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#stationModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success', response.success, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message, 'error');
                    }
                });
            });

            // Edit Button Click
            $('#datatable').on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.get("{{ url('/admin/stations') }}/" + id, function(data) {
                    $('#station_id').val(data.id);
                    $('#station_name').val(data.station_name);
                    $('#department_id').val(data.department_id);
                    $('#is_active').prop('checked', data.is_active);
                    $('#stationModal').modal('show');
                    $('#modalTitle').text('Edit Station');
                });
            });

            // Delete Button Click
            $('#datatable').on('click', '.delete-btn', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "This action cannot be undone!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('/admin/stations') }}/" + id,
                            method: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire('Deleted!', response.success, 'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON.message, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
