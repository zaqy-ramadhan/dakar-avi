@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card mb-3" style="border-radius: 20px">
            <div class="card-header">
                <p class="fs-8 fw-bold">Sections</p>
            </div>
            <div class="card-body">
                <button class="btn btn-primary float-end" id="addSectionBtn">Create Section</button>
            </div>
        </div>

        <table id="datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Section Name</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sectionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="sectionForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add/Edit Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="section_id">
                        <div class="mb-3">
                            <label>Section Name</label>
                            <input type="text" id="section_name" class="form-control" required>
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
                ajax: "{{ route('sections.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'section_name',
                        name: 'section_name'
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

            // Open Modal for Adding Section
            $('#addSectionBtn').click(function() {
                $('#section_id').val('');
                $('#section_name').val('');
                $('#department_id').val('');
                $('#sectionModal').modal('show');
                $('#modalTitle').text('Create section');
            });

            // Submit Form (Add/Edit)
            $('#sectionForm').submit(function(e) {
                e.preventDefault();
                let id = $('#section_id').val();
                let url = id ? "{{ url('/admin/sections') }}/" + id : "{{ route('sections.store') }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        section_name: $('#section_name').val(),
                        department_id: $('#department_id').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#sectionModal').modal('hide');
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
                $.get("{{ url('/admin/sections') }}/" + id, function(data) {
                    $('#section_id').val(data.id);
                    $('#section_name').val(data.section_name);
                    $('#department_id').val(data.department_id);
                    $('#sectionModal').modal('show');
                    $('#modalTitle').text('Edit Section');
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
                            url: "{{ url('/admin/sections') }}/" + id,
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
