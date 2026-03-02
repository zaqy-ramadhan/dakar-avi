@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card" style="border-radius: 20px">
            <div class="card-header">
                <p class="fs-8 fw-bold">Manage Admin</p>
            </div>
            <dic class="card-body">

                <form method="GET" class="mb-4">
                    {{-- <input type="hidden" name="note" value="{{ $note }}">
                    <div class="input-group" style="max-width: fit-content;">
                        <input type="month" name="date"
                            value="{{ request('date', \Carbon\Carbon::now()->format('Y-m')) }}" class="form-control">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('staff-movement.index', ['note' => $note]) }}" class="btn btn-secondary">Reset</a>
                        <button type="button" id="exportExcel" class="btn btn-success"><i
                                class="ti ti-file-spreadsheet fs-4"></i>Export Excel</button>
                    </div> --}}
                </form>
            </dic>
        </div>

        <div class="card" style="overflow-x: auto; width: 100%; border-radius: 20px;">
            <table id="datatable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>

       <div class="modal fade" id="modalActivityLogs" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Activity Logs: <span id="activityUserName"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead class="sticky-top bg-white">
                                <tr class="fw-bold text-muted">
                                    <th>Date</th>
                                    <th>Actor</th>
                                    <th>Employee</th>
                                    <th>Note</th>
                                    <th>Table</th>
                                </tr>
                            </thead>

                            <!-- INI YANG BENAR -->
                            <tbody id="activityLogsTableBody">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        Click activity button to load logs.
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                </div>

                {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Close
                    </button>
                </div> --}}

            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
<script>
    let currentNote = @json($note ?? null);
    let datatable;

    function loadDataTable(note, date = null) {
        if (datatable) {
            datatable.destroy();
            $('#datatable').empty(); // penting supaya tidak double header
        }

        datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.data') }}",
                data: function(d) {
                    d.note = note;
                    d.date = date;
                }
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'fullname',
                    name: 'fullname',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'role',
                    name: 'role',
                    orderable: true,
                    searchable: true

                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: true,
                    searchable: true

                }
            ]
        });
    }

    $(document).ready(function() {
        loadDataTable(currentNote);
    });
</script>
<script>
    $(document).on('click', '.btn-activity', function () {

    let userId = $(this).data('id');
    let userName = $(this).data('name');

    $('#activityUserName').text(userName);

    $.ajax({
        url: '/activity-logs/' + userId,
        type: 'GET',
        success: function (response) {

            let rows = '';

            if (response.length > 0) {
                response.forEach(function (log) {
                    rows += `
                        <tr>
                            <td>${log.created_at}</td>
                            <td>${log.actor ?? 'Admin'}</td>
                            <td>${log.employee ?? 'Employee'}</td>
                            <td>${log.note ?? '-'}</td>
                            <td>
                                <span class="badge rounded-pill text-bg-success">
                                    ${log.table_name}
                                </span>
                            </td>
                        </tr>
                    `;
                });
            } else {
                rows = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            No activity logs found.
                        </td>
                    </tr>
                `;
            }

            $('#activityLogsTableBody').html(rows);
        }
    });

});
</script>
@endpush
