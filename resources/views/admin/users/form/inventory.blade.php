{{-- <div class="container card p-4"> --}}
<h2 class="mb-4">Inventaris</h2>

<form
    action="{{ $user->inventory->isNotEmpty() ? route('inventory.update', $user->id) : route('inventory.store', $user->id) }}"
    method="POST">
    @csrf
    @if ($user->inventory->isNotEmpty())
        @method('POST')
    @endif

    @if ($groupedItems->isNotEmpty())
        <div class="row row-cols-1 row-cols-md-4 g-3">
            @foreach ($groupedItems as $itemName => $items)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-checkbox text-primary" style="font-size: 2rem;"></i>
                                <div class="ms-3">
                                    <div class="fw-bold">{{ Str::ucfirst($itemName) }}</div>
                                    <div class="text-muted">{{ $items->count() }} Item diterima</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-light text-center mt-3" role="alert">
            Belum ada barang yang diterima.
        </div>
    @endif

    <div class="mb-3 mt-3">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="item-col">Item</th>
                        <th>Ukuran</th>
                        {{-- <th class="status-col">Status</th> --}}
                        <th>Job Status</th>
                        <th class="status-col">Diterima</th>
                        <th class="status-col">Dikembalikan</th>
                        <th class="date-col">Due Date</th>
                        <th class="date-col">Receive Date</th>
                        <th class="date-col">Return Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="items-container"></tbody>
            </table>
        </div>
        <button type="button" id="add-item" class="btn btn-success mt-2"
            @if (Auth::user()->getRole() !== 'admin') hidden @endif>Tambah Item</button>
    </div>

    <button type="submit" class="btn btn-primary mt-3">
        {{ $user->inventory->isNotEmpty() ? 'Update' : 'Simpan' }}
    </button>
</form>
{{-- </div> --}}

{{-- @dd($items); --}}
{{-- @dd($rule->items); --}}

@push('scripts')
    <script>
        function updateHiddenStatus(row) {
            const acceptCheckbox = row.find(".accept-status");
            const returnCheckbox = row.find(".return-status");
            const hiddenStatus = row.find(".status-hidden");

            if (!acceptCheckbox.is(":checked") && !returnCheckbox.is(":checked")) {
                hiddenStatus.prop("checked", true);
            } else {
                hiddenStatus.prop("checked", false);
            }
        }


        $(document).ready(function() {
            let allItems = @json($allItems);
            let userInventory = @json($inventories ?? null);
            let userContract = @json($user->employeeJob->last()->contract ?? null);
            let userJob = @json($user->employeeJob->last() ?? null);
            let prevRole = @json($previousRole ?? true) ;

            $("tr").each(function() {
                updateHiddenStatus($(this));
            });

            $(document).on("change", ".accept-status", function() {
                const row = $(this).closest("tr");
                row.find(".return-status").prop("checked", false);
                row.find(".return-notes-wrapper").hide();

                updateHiddenStatus(row);
            });

            // $(document).on("change", ".return-status", function() {
            //     const row = $(this).closest("tr");
            //     row.find(".accept-status").prop("checked", false);
            //     updateHiddenStatus(row);
            // });

            $(document).on("change", ".return-status", function() {
                const row = $(this).closest("tr");
                const acceptCheckbox = row.find(".accept-status");
                const hiddenStatus = row.find(".status-hidden");
                const notesField = row.find(".return-notes-wrapper");

                if ($(this).is(":checked")) {
                    acceptCheckbox.prop("checked", false);
                    notesField.show();
                } else {
                    if (!acceptCheckbox.is(":checked")) {
                        hiddenStatus.prop("checked", true);
                    }
                    notesField.hide();
                    // notesField.find('input').val('');
                }

                updateHiddenStatus(row);
            });


            let sizeOptions = {
                'baju': ['-', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL'],
                'sepatu': ['-', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'],
                'lainnya': ['-']
            };

            function createItemDropdown(selectedId = null) {
                let select = `<select name="items[]" class="form-select itemSelect" style="width:auto;">
                            <option value="" disabled ${selectedId ? '' : 'selected'}>Pilih Item</option>`;
                allItems.forEach(item => {
                    let isSelected = selectedId == item.id ? 'selected' : '';
                    select +=
                        `<option value="${item.id}" data-type="${item.type}" ${isSelected}>${item.item_name}</option>`;
                });
                select += `</select>`;
                return select;
            }

            function createHiddenEmployeeJob(employeeJobs) {
                let hiddenInputs = '';
                employeeJobs.forEach(job => {
                    hiddenInputs += `<input name="employee_job_ids[]" value="${job.id}">`;
                });
                return hiddenInputs;
            }

            function createSizeDropdown(selectedType = 'lainnya', selectedSize = '-') {
                let select = `<select name="sizes[]" class="form-select sizeSelect" style="width:auto;">`;
                sizeOptions[selectedType].forEach(size => {
                    let isSelected = size == selectedSize ? 'selected' : '';
                    select += `<option value="${size}" ${isSelected}>${size}</option>`;
                });
                select += `</select>`;
                return select;
            }

            function createStatusDropdown(selectedStatus = 'Diterima') {
                return `<select name="status[]" class="form-select" style="width:auto;">
                        <option value="-" ${selectedStatus == '-' ? 'selected' : ''}>-</option>
                        <option value="Diterima" ${selectedStatus == 'Diterima' ? 'selected' : ''}>Diterima</option>
                        <option value="Dikembalikan" ${selectedStatus == 'Dikembalikan' ? 'selected' : ''}>Dikembalikan</option>
                    </select>`;
            }

            function createStatusCheckbox(selectedStatus = 'Diterima') {
                return `
                    <div class="form-check form-check-inline">
                        <input class="form-check-input accept-status" type="checkbox" name="status[]" value="Diterima"
                            ${selectedStatus === 'Diterima' ? 'checked' : ''}>
                        <label class="form-check-label">Diterima</label>
                    </div>
                `;
            }

            function createReturnCheckbox(selectedStatus = 'Dikembalikan', name = '', returnNote = '') {
                let label = ['Email AVI', 'Email Visteon', 'BPJS TK', 'BPJS Kesehatan', 'User Account Great Day',
                        'User Account E-Slip'
                    ].includes(name) ?
                    'Dinonaktifkan' :
                    'Dikembalikan';

                const isChecked = selectedStatus === 'Dikembalikan' || selectedStatus === 'Dinonaktifkan';

                return `
                     <div class="mb-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input return-status" type="checkbox" name="status[]" value="Dikembalikan"
                                ${isChecked ? 'checked' : ''}>
                            <label class="form-check-label">${label}</label>
                        </div>
                    </div>
                    <div class="return-notes-wrapper mb-2" style="display: ${isChecked ? 'block' : 'none'};">
                        <label class="form-label small text-muted">Catatan pengembalian</label>
                        <input type="text" name="return_notes[]" class="form-control" value="${returnNote ?? ''}" placeholder="Alasan pengembalian">
                    </div>
                `;
            }

            function createHiddenCheckbox(selectedStatus = '-') {
                const isChecked = !['Diterima', 'Dikembalikan', 'Dinonaktifkan'].includes(selectedStatus);

                return `
                    <input type="checkbox" class="status-hidden" name="status[]" value="" ${isChecked ? 'checked' : ''}>
                `;
            }


            function formatDate(value) {
                if (value) {
                    const date = new Date(value);

                    const options = {
                        year: 'numeric',
                        day: '2-digit',
                        month: '2-digit',
                        timeZone: 'Asia/Jakarta'
                    };
                    value = new Intl.DateTimeFormat('en-UK', options).format(date).split('/').reverse().join('-');
                }
                return value;
            }

            function createDateInput(name, value = '') {
                if (value) {
                    const date = new Date(value);

                    const options = {
                        year: 'numeric',
                        day: '2-digit',
                        month: '2-digit',
                        timeZone: 'Asia/Jakarta'
                    };
                    value = new Intl.DateTimeFormat('en-UK', options).format(date).split('/').reverse().join('-');
                }
                return `<input type="date" name="${name}[]" class="form-control" style="width: min-content" value="${value}">`;
            }
            // <td>${createStatusDropdown(selectedStatus)}</td>

            function addItem(selectedId = null, selectedSize = '-', selectedStatus = '-', dueDate = null, accDate =
                '', returnDate = '', selectedJobId = null, contract = '', name = '', returnNote = '') {
                let selectedType = selectedId ? allItems.find(item => item.id == selectedId)?.type || 'lainnya' :
                    'lainnya';

                let role = `{{ Auth::user()->getRole() }}`;

                let actionButtons = '';

                if (role === 'admin') {
                    actionButtons = `
                        <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                            <i class="ti ti-trash"></i>
                        </button>
                    `;
                }

                let newRow = `
                    <tr class="item-group">
                        <td>${createItemDropdown(selectedId)}</td>
                        <td>${createSizeDropdown(selectedType, selectedSize)}</td>
                        <td>${contract === '' ? userContract : contract}</td>
                        <td>${createStatusCheckbox(selectedStatus)}</td>
                        <td>${createReturnCheckbox(selectedStatus, name, returnNote)}</td>
                        <td hidden>${createHiddenCheckbox(selectedStatus)}</td>
                        <td>${dueDate ? formatDate(dueDate) : '-'}</td>
                        <td>${accDate ? formatDate(accDate) : '-'}</td>
                        <td>${returnDate ? formatDate(returnDate) : '-'}</td>
                        <td hidden>${createDateInput('due_date', dueDate)}</td>
                        <td hidden>${createDateInput('acc_date', accDate)}</td>
                        <td hidden>${createDateInput('return_date', returnDate)}</td>
                        <td hidden>${createHiddenEmployeeJob([{ id: selectedJobId }])}</td>      
                        <td>${actionButtons}</td>
                    </tr>
                `;

                $('#items-container').append(newRow);
            }

            $(document).on('change', '.itemSelect', function() {
                let selectedType = $(this).find(':selected').data('type');
                let sizeSelect = $(this).closest('tr').find('.sizeSelect');
                sizeSelect.replaceWith(createSizeDropdown(selectedType));
            });

            $('#add-item').click(function() {
                addItem();
            });

            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
            });
            if (userInventory.length > 0) {
                // if (last) {
                //     @if ($rule && $rule->items)
                //         @foreach ($rule->items as $item)
                //             addItem("{{ $item['id'] }}", "{{ $item['size'] }}");
                //         @endforeach
                //     @endif
                // }

                if (prevRole) {
                    @if ($rule && $rule->items)
                        @foreach ($rule->items as $item)
                            addItem("{{ $item['id'] }}", "{{ $item['size'] }}");
                        @endforeach
                    @endif
                }else{
                    userInventory.forEach(item => {
                        addItem(item.item_id, item.size, item.status, item.due_date, item.acc_date, item
                            .return_date, item.employee_job_id, item.contract, item.item_name, item
                            .return_notes);
                    });
                }
            } else {
                @if ($rule && $rule->items)
                    @foreach ($items as $item)
                        addItem("{{ $item['id'] }}", "{{ $item['size'] }}");
                    @endforeach
                @endif
            }

        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .item-pills {
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .item-pills .btn {
            margin-right: 5px;
            padding: 5px 10px;
            /* font-weight: bold; */
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            color: #333;
        }

        .item-pills .btn:hover {
            background-color: #e9ecef;
        }

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }

            .table th,
            .table td {
                white-space: nowrap;
            }
        }

        #inventory-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        #inventory-summary .card {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }
    </style>
@endpush
