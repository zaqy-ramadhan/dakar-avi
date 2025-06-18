@extends('layouts.admin')

@section('content')
<div class="container">
    <h4 class="mb-4">Staff Movement Record</h4>

    <div class="row mb-3">
        <div class="col-md-4">
            <input type="month" class="form-control" id="filter-month" value="{{ now()->format('Y-m') }}">
        </div>
    </div>

    <ul class="nav nav-tabs" id="movementTabs" role="tablist">
        @foreach ($categories as $index => $note)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                        id="tab-{{ $index }}"
                        data-bs-toggle="tab"
                        data-bs-target="#table-{{ $index }}"
                        type="button"
                        role="tab">
                    {{ $note }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content mt-3">
        @foreach ($categories as $index => $note)
            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="table-{{ $index }}" role="tabpanel">
                <div class="card" style="border-radius: 20px; overflow-x: auto; width: 100%;">
                    <table class="table table-bordered movement-table" data-note="{{ $note }}" id="datatable-{{ $index }}">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NPK</th>
                                <th>Nama Lengkap</th>
                                <th>Posisi</th>
                                <th>Departemen</th>
                                <th>Tanggal Mulai</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    function loadTable(tableId, note, date) {
        $('#' + tableId).DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: '{{ route("staff-movement.data") }}',
                data: {
                    note: note,
                    date: date
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'npk', name: 'npk' },
                { data: 'fullname', name: 'fullname' },
                { data: 'position', name: 'position' },
                { data: 'department', name: 'department' },
                { data: 'start_date', name: 'start_date' },
            ]
        });
    }

    $(function () {
        const defaultDate = $('#filter-month').val() + '-01';

        $('.movement-table').each(function () {
            const tableId = $(this).attr('id');
            const note = $(this).data('note');
            loadTable(tableId, note, defaultDate);
        });

        $('#filter-month').on('change', function () {
            const selectedDate = $(this).val() + '-01';
            $('.movement-table').each(function () {
                const tableId = $(this).attr('id');
                const note = $(this).data('note');
                loadTable(tableId, note, selectedDate);
            });
        });
    });
</script>
@endpush
