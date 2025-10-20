@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card mb-3" style="border-radius: 20px">
            <div class="card-header">
                <p class="fs-8 fw-bold">Holiday Date</p>
            </div>
            <div class="card-body">
                <button class="btn btn-primary float-end" id="addHolidayBtn">Create Holiday Date</button>
            </div>
        </div>

        <table id="datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="holidayModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="holidayForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add/Edit Holiday Date</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="holiday_id">
                        <div class="mb-3">
                            <label>Date</label>
                            <input type="date" id="date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <input type="text" id="keterangan" class="form-control" required>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active">
                            <label class="form-check-label" for="is_active">Active</label>
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
                ajax: "{{ route('holidays.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        render: function(data) {
                            return data == 1 ? '<span class="badge bg-success">Active</span>' :
                                '<span class="badge bg-danger">Inactive</span>';
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

            // ✅ Add Holiday
            $('#addHolidayBtn').click(function() {
                $('#holiday_id').val('');
                $('#holidayForm')[0].reset();
                $('#modalTitle').text('Create Holiday Date');
                $('#holidayModal').modal('show');
            });

            // ✅ Save (Add or Update)
            $('#holidayForm').submit(function(e) {
                e.preventDefault();
                let id = $('#holiday_id').val();
                let url = id ? "{{ route('holidays.update', ':id') }}".replace(':id', id) :
                    "{{ route('holidays.store') }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        date: $('#date').val(),
                        keterangan: $('#keterangan').val(),
                        is_active: $('#is_active').is(':checked') ? 1 : 0,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#holidayModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success', response.success, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message, 'error');
                    }
                });
            });

            // ✅ Edit Holiday
            $('#datatable').on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.get("{{ route('holidays.show', ':id') }}".replace(':id', id), function(data) {
                    $('#holiday_id').val(data.id);
                    $('#date').val(data.date);
                    $('#keterangan').val(data.keterangan);
                    $('#is_active').prop('checked', data.is_active == 1);
                    $('#modalTitle').text('Edit Holiday Date');
                    $('#holidayModal').modal('show');
                });
            });

            // ✅ Delete Holiday
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
                            url: "{{ route('holidays.destroy', ':id') }}".replace(':id',
                                id),
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
