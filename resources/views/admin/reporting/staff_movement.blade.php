@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card" style="border-radius: 20px">
            <dic class="card-body">

                @php
                    $notes = [
                        'New Employee Kontrak',
                        'New Employee Tetap',
                        'New Employee Pemagangan',
                        'New Employee Internship',
                        'Employee Contract Extension',
                        'Employee Contract Position Change',
                        'Employee Department Mutation',
                        'One Year Service',
                    ];
                @endphp

                {{-- <ul class="nav nav-tabs mb-3">
                    @foreach ($notes as $label)
                        <li class="nav-item">
                            <a class="nav-link {{ $note == $label ? 'active' : '' }}"
                                href="{{ route('staff-movement.index', ['note' => $label, 'date' => request('date')]) }}">
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul> --}}
                <ul class="nav nav-tabs mb-3" id="noteTabs">
                    @foreach ($notes as $label)
                        <li class="nav-item">
                            <button class="nav-link {{ $note == $label ? 'active' : '' }}" data-note="{{ $label }}">
                                {{ $label }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <form method="GET" class="mb-4">
                    <input type="hidden" name="note" value="{{ $note }}">
                    <div class="input-group" style="max-width: 400px;">
                        <input type="month" name="date"
                            value="{{ request('date', \Carbon\Carbon::now()->format('Y-m')) }}" class="form-control">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('staff-movement.index', ['note' => $note]) }}" class="btn btn-secondary">Reset</a>
                        <button type="button" id="exportExcel" class="btn btn-success"><i
                                class="ti ti-file-spreadsheet fs-4"></i>Export Excel</button>
                    </div>
                </form>
            </dic>
        </div>

        <table id="datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Full Name</th>
                    <th>NPK</th>

                    @if (in_array($note, ['New Employee Kontrak', 'New Employee Pemagangan', 'New Employee Tetap']))
                        <th>Department</th>
                        <th>Section</th>
                        <th>Position</th>
                        <th>Start Date</th>
                    @elseif($note == 'New Employee Internship')
                        <th>Department</th>
                        <th>Start Date</th>
                        <th>Duration</th>
                    @elseif($note == 'Employee Contract Extension')
                        <th>Department</th>
                        <th>Section</th>
                        <th>Position</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Duration</th>
                        <th>Status</th>
                    @elseif($note == 'Employee Contract Position Change')
                        <th>Department</th>
                        <th>Section</th>
                        <th>Old Position</th>
                        <th>New Position</th>
                        <th>Start Date</th>
                    @elseif($note == 'Employee Department Mutation')
                        <th>Old Department</th>
                        <th>New Department</th>
                        <th>Section</th>
                        <th>Position</th>
                        <th>Start Date</th>
                    @elseif($note == 'One Year Service')
                        <th>Department</th>
                        <th>Section</th>
                        <th>Position</th>
                        <th>Start Date</th>
                    @endif
                </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        let currentNote = @json($note);
        let datatable;

        function loadDataTable(note, date) {
            if (datatable) {
                datatable.destroy();
                $('#datatable').empty(); // reset table
                $('#datatable').html(`
                <thead><tr><th>No</th><th>Full Name</th><th>NPK</th></tr></thead>
            `); // optionally reset header
            }

            $.ajax({
                url: "{{ route('staff-movement.data') }}",
                data: {
                    note: note,
                    date: date
                },
                success: function(response) {
                    // Optional: re-render header based on note if needed

                    // Initialize new DataTable
                    datatable = $('#datatable').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('staff-movement.data') }}",
                            data: {
                                note: note,
                                date: date
                            }
                        },
                        columns: getColumnsByNote(note)
                    });
                }
            });
        }

        function getColumnsByNote(note) {
            const base = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'fullname',
                    name: 'fullname'
                },
                {
                    data: 'npk',
                    name: 'npk'
                },
            ];

            const columnsByNote = {
                'New Employee Kontrak': [{
                    data: 'department'
                }, {
                    data: 'section'
                }, {
                    data: 'position'
                }, {
                    data: 'start_date'
                }],
                'New Employee Tetap': [{
                    data: 'department'
                }, {
                    data: 'section'
                }, {
                    data: 'position'
                }, {
                    data: 'start_date'
                }],
                'New Employee Pemagangan': [{
                    data: 'department'
                }, {
                    data: 'section'
                }, {
                    data: 'position'
                }, {
                    data: 'start_date'
                }],
                'New Employee Internship': [{
                    data: 'department'
                }, {
                    data: 'start_date'
                }, {
                    data: 'duration'
                }],
                'Employee Contract Extension': [{
                        data: 'department'
                    }, {
                        data: 'section'
                    }, {
                        data: 'position'
                    }, {
                        data: 'start_date'
                    },
                    {
                        data: 'end_date'
                    }, {
                        data: 'duration'
                    }, {
                        data: 'contract'
                    }
                ],
                'Employee Contract Position Change': [{
                        data: 'department'
                    }, {
                        data: 'section'
                    }, {
                        data: 'old_position'
                    },
                    {
                        data: 'position'
                    }, {
                        data: 'start_date'
                    }
                ],
                'Employee Department Mutation': [{
                        data: 'old_department'
                    }, {
                        data: 'department'
                    }, {
                        data: 'section'
                    },
                    {
                        data: 'position'
                    }, {
                        data: 'start_date'
                    }
                ],
                'One Year Service': [{
                    data: 'department'
                }, {
                    data: 'section'
                }, {
                    data: 'position'
                }, {
                    data: 'start_date'
                }],
            };

            return base.concat(columnsByNote[note] || []);
        }

        $(function() {
            const defaultDate = $('input[name="date"]').val();

            // Load first time
            loadDataTable(currentNote, defaultDate);

            // Tab click
            $('#noteTabs').on('click', 'button[data-note]', function() {
                const selectedNote = $(this).data('note');

                currentNote = selectedNote;

                // Update active class
                $('#noteTabs .nav-link').removeClass('active');
                $(this).addClass('active');

                // Update hidden input for export
                $('input[name="note"]').val(selectedNote);

                // Load DataTable
                loadDataTable(selectedNote, defaultDate);
            });

            // Export
            $('#exportExcel').on('click', function() {
                var date = $('input[name="date"]').val();
                var note = $('input[name="note"]').val();
                window.location.href = "{{ route('staff-movement.data') }}" +
                    `?note=${encodeURIComponent(note)}&date=${encodeURIComponent(date)}&export=excel`;
            });
        });
    </script>

    {{-- <script>
        $(function() {
            $('#exportExcel').on('click', function() {
                // Collect filter params
                var date = $('input[name="date"]').val();
                var note = $('input[name="note"]').val();
                var params = [];
                if (note) params.push('note=' + encodeURIComponent(note));
                if (date) params.push('date=' + encodeURIComponent(date));
                params.push('export=excel');
                window.location.href = "{{ route('staff-movement.data') }}" + "?" + params.join('&');
            });
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('staff-movement.data', ['note' => $note]) }}",
                    data: function(d) {
                        d.date = "{{ request('date') }}";
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'fullname',
                        name: 'fullname'
                    },
                    {
                        data: 'npk',
                        name: 'npk'
                    },

                    @if (in_array($note, ['New Employee Kontrak', 'New Employee Pemagangan', 'New Employee Tetap']))
                        {
                            data: 'department',
                            name: 'department'
                        }, {
                            data: 'section',
                            name: 'section'
                        }, {
                            data: 'position',
                            name: 'position'
                        }, {
                            data: 'start_date',
                            name: 'start_date'
                        },
                    @elseif ($note == 'New Employee Internship') {
                            data: 'department',
                            name: 'department'
                        }, {
                            data: 'start_date',
                            name: 'start_date'
                        }, {
                            data: 'duration',
                            name: 'duration'
                        },
                    @elseif ($note == 'Employee Contract Extension') {
                            data: 'department',
                            name: 'department'
                        }, {
                            data: 'section',
                            name: 'section'
                        }, {
                            data: 'position',
                            name: 'position'
                        }, {
                            data: 'start_date',
                            name: 'start_date'
                        }, {
                            data: 'end_date',
                            name: 'end_date'
                        }, {
                            data: 'duration',
                            name: 'duration'
                        }, {
                            data: 'contract',
                            name: 'contract'
                        },
                    @elseif ($note == 'Employee Contract Position Change') {
                            data: 'department',
                            name: 'department'
                        }, {
                            data: 'section',
                            name: 'section'
                        }, {
                            data: 'old_position',
                            name: 'old_position'
                        }, {
                            data: 'position',
                            name: 'position'
                        }, {
                            data: 'start_date',
                            name: 'start_date'
                        },
                    @elseif ($note == 'Employee Department Mutation') {
                            data: 'old_department',
                            name: 'old_department'
                        }, {
                            data: 'department',
                            name: 'department'
                        }, {
                            data: 'section',
                            name: 'section'
                        }, {
                            data: 'position',
                            name: 'position'
                        }, {
                            data: 'start_date',
                            name: 'start_date'
                        },
                    @elseif ($note == 'One Year Service') {
                            data: 'department',
                            name: 'department'
                        }, {
                            data: 'section',
                            name: 'section'
                        }, {
                            data: 'position',
                            name: 'position'
                        }, {
                            data: 'start_date',
                            name: 'start_date'
                        },
                    @endif
                ],
            });
        });
    </script> --}}
@endpush
