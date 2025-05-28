@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card mb-3" style="border-radius: 20px">
            <div class="card-header">
                <p class="fs-8 fw-bold" id="entityTitle">User Details</p>
            </div>
            <div class="card-body">
                <button class="btn btn-primary float-end" id="addEntityBtn">Create {{ $modelName }}</button>
            </div>
        </div>


        <table id="datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th id="entityColumnTitle">Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="entityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="entityForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add/Edit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="entity_id">
                        <div class="mb-3">
                            <label id="entityLabel">Name</label>
                            <input type="text" id="entity_name" class="form-control" required>
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
            let entityName = "{{ $entityName }}";
            let modelName = "{{ $modelName }}"
            let fieldName = "{{ $fieldName ?? 'name' }}";
            let entityIndexRoute = "{{ route('entity.index') }}".replace('entity', entityName);
            let entityStoreRoute = "{{ route('entity.store') }}".replace('entity', entityName);
            let entityUpdateRoute = "{{ route('entity.update', ':id') }}".replace('entity', entityName);
            let entityShowRoute = "{{ route('entity.show', ':id') }}".replace('entity', entityName);
            let entityDestroyRoute = "{{ route('entity.destroy', ':id') }}".replace('entity', entityName);


            $('#entityTitle').text(modelName);
            $('#entityColumnTitle').text(fieldName.replace('_', ' ').toUpperCase());
            $('#entityLabel').text(fieldName.replace('_', ' ').toUpperCase());

            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: entityIndexRoute,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: fieldName,
                        name: fieldName
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        searchable: false,
                        orderable: false
                    }
                ]
            });

            $('#addEntityBtn').click(function() {
                $('#entity_id').val('');
                $('#entity_name').val('');
                $('#entityModal').modal('show');
                $('#modalTitle').text('Create ' + entityName);
            });

            $('#entityForm').submit(function(e) {
                e.preventDefault();
                let id = $('#entity_id').val();
                let url = id ? entityUpdateRoute.replace(':id', id) : entityStoreRoute;
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        [fieldName]: $('#entity_name').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#entityModal').modal('hide');
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
                $.get(entityShowRoute.replace(':id', id), function(data) {
                    $('#entity_id').val(data.id);
                    $('#entity_name').val(data[fieldName]);
                    $('#entityModal').modal('show');
                    $('#modalTitle').text('Edit ' + entityName);
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
                            url: entityDestroyRoute.replace(':id', id),
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
