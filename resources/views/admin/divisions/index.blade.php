@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card mb-3" style="border-radius: 20px">
            <div class="card-header">
                <p class="fs-8 fw-bold">Division</p>
            </div>
            <div class="card-body">
                <button class="btn btn-primary float-end" id="addDivisionBtn">Create Division</button>
            </div>
        </div>

        <table id="datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Division Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="divisionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="divisionForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add/Edit Division</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="division_id">
                        <div class="mb-3">
                            <label>Division Name</label>
                            <input type="text" id="division_name" class="form-control" required>
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
                ajax: "{{ route('divisions.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'division_name',
                        name: 'division_name'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        searchable: false,
                        orderable: false
                    }
                ]
            });

            // Open Modal for Adding Division
            $('#addDivisionBtn').click(function() {
                $('#division_id').val('');
                $('#division_name').val('');
                $('#divisionModal').modal('show');
                $('#modalTitle').text('Create Division');
            });

            // Submit Form (Add/Edit)
            $('#divisionForm').submit(function(e) {
                e.preventDefault();
                let id = $('#division_id').val();
                let url = id ? "{{ route('divisions.update', ':id') }}".replace(':id', id) :
                    "{{ route('divisions.store') }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        division_name: $('#division_name').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#divisionModal').modal('hide');
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
                $.get("{{ route('divisions.show', ':id') }}".replace(':id', id), function(data) {
                    $('#division_id').val(data.id);
                    $('#division_name').val(data.division_name);
                    $('#divisionModal').modal('show');
                    $('#modalTitle').text('Edit Division');

                });
            });

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
                            url: "{{ route('divisions.destroy', ':id') }}".replace(':id', id),
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
