@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card mb-3" style="border-radius: 20px">
        <div class="card-header">
            <p class="fs-8 fw-bold">Departments</p>
        </div>
        <div class="card-body">
            <button class="btn btn-primary float-end" id="addDepartmentBtn">Create Department</button>
        </div>
    </div>
    
    <table id="datatable" class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Department Name</th>
                <th>Division</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="departmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="departmentForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add/Edit Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="department_id">
                    <div class="mb-3">
                        <label>Department Name</label>
                        <input type="text" id="department_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Division</label>
                        <select id="division_id" class="form-control" required>
                            <option value="">Select Division</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->division_name }}</option>
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
        ajax: "{{ route('departments.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
            { data: 'department_name', name: 'department_name' },
            { data: 'division.division_name', name: 'division.division_name' },
            { data: 'actions', name: 'actions', searchable: false, orderable: false }
        ]
    });

    // Open Modal for Adding Department
    $('#addDepartmentBtn').click(function() {
        $('#department_id').val('');
        $('#department_name').val('');
        $('#division_id').val('');
        $('#departmentModal').modal('show');
        $('#modalTitle').text('Create Department');
    });

    // Submit Form (Add/Edit)
    $('#departmentForm').submit(function(e) {
        e.preventDefault();
        let id = $('#department_id').val();
        let url = id ? "{{ url('/admin/departments') }}/" + id : "{{ route('departments.store') }}";
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: { 
                department_name: $('#department_name').val(),
                division_id: $('#division_id').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                $('#departmentModal').modal('hide');
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
        $.get("{{ url('/admin/departments') }}/" + id, function(data) {
            $('#department_id').val(data.id);
            $('#department_name').val(data.department_name);
            $('#division_id').val(data.division_id);
            $('#departmentModal').modal('show');
            $('#modalTitle').text('Edit Department');
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
                    url: "{{ url('/admin/departments') }}/" + id,
                    method: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
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