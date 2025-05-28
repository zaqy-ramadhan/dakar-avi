@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card mb-3" style="border-radius: 20px">
            <div class="card-header">
                <p class="fs-8 fw-bold">Positions</p>
            </div>
            <div class="card-body">
                <button class="btn btn-primary float-end" id="addPositionBtn">Create Position</button>
            </div>
        </div>

        <table id="datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Position Name</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="positionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="positionForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add/Edit Position</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="position_id">
                        <div class="mb-3">
                            <label>Position Name</label>
                            <input type="text" id="position_name" class="form-control" required>
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
                ajax: "{{ route('positions.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'position_name',
                        name: 'position_name'
                    },
                    {
                        data: 'department.department_name',
                        name: 'department.department_name'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        searchable: false,
                        orderable: false
                    }
                ]
            });

            // Open Modal for Adding Position
            $('#addPositionBtn').click(function() {
                $('#position_id').val('');
                $('#position_name').val('');
                $('#department_id').val('');
                $('#positionModal').modal('show');
                $('#modalTitle').text('Create position');
            });

            // Submit Form (Add/Edit)
            $('#positionForm').submit(function(e) {
                e.preventDefault();
                let id = $('#position_id').val();
                let url = id ? "{{ url('/admin/positions') }}/" + id : "{{ route('positions.store') }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        position_name: $('#position_name').val(),
                        department_id: $('#department_id').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#positionModal').modal('hide');
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
                $.get("{{ url('/admin/positions') }}/" + id, function(data) {
                    $('#position_id').val(data.id);
                    $('#position_name').val(data.position_name);
                    $('#department_id').val(data.department_id);
                    $('#positionModal').modal('show');
                    $('#modalTitle').text('Edit Position');
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
                            url: "{{ url('/admin/positions') }}/" + id,
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
