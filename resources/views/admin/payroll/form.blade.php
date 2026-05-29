{{-- <div class="container card p-4"> --}}
@extends('layouts.admin')

@section('content')
    <div class="card" style="border-radius: 20px">
        <div class="card-header">
            <h2 class="mb-4">Payroll Pemagangan</h2>
        </div>

        <div class="card-body">
            <form id="payroll-form">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="title" class="form-label">Keterangan</label>
                        <input type="text" id="title" name="title" class="form-control"
                            placeholder="Contoh: Payroll Oktober 2025" required>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Tanggal Awal</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" id="submitPayroll" class="btn btn-primary w-100">Simpan Payroll</button>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="default_attendance" class="form-label">Default Attendance</label>
                        <input type="number" id="default_attendance" name="default_attendance" class="form-control"
                            placeholder="Isi angka, contoh: 22">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="mt-2">
                            <strong>Total Karyawan: <span id="employee-count">0</span></strong>
                    </div>
                </div>

                <!-- Tabel Detail Payroll -->
                <div class="mb-3 mt-3">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                     <th>No</th>
                                    <th>NPK</th>
                                    <th>Employee</th>
                                    <th>Position</th>
                                    <th>Work Days</th>
                                    <th>Attendance</th>
                                    <th>Basic Salary</th>
                                    <th>Total Salary</th>
                                    <th>Remark</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="items-container"></tbody>
                        </table>
                    </div>

                    <button type="button" id="add-item" class="btn btn-success mt-2"
                        @if (!in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3', 'admin 4'])) hidden @endif>
                        Tambah Item
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let employees = @json($employee ?? []);
            let selectedEmployees = [];
            let mode = "{{ $mode ?? 'create' }}"; // 'create', 'edit', 'view'
            let payroll = @json($payroll ?? null);

            $('#start_date, #end_date').on('change', function() {
                if (mode === 'view') return;

                let start_date = $('#start_date').val();
                let end_date = $('#end_date').val();

                if (start_date && end_date) {
                    fetchPemagangan(start_date, end_date)
                        .then(() => {
                            return calculateWorkdays(start_date, end_date);
                        })
                        .then(() => {
                            console.log('✅ Pemagangan & Workdays selesai diproses');
                        })
                        .catch(err => {
                            console.error('❌ Error:', err);
                        });
                } else {
                    employees = @json($employee ?? []);
                }
            });

            function fetchPemagangan(start_date, end_date) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '/get-pemagangan',
                        type: 'POST',
                        data: {
                            start_date: start_date,
                            end_date: end_date,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                employees = response.data;
                                console.log('✅ Employees updated from API:', employees);
                                refreshEmployeeDropdowns();
                                resolve();
                            } else {
                                console.warn('⚠️ Response tidak valid:', response);
                                resolve();
                            }
                        },
                        error: function(xhr) {
                            console.error('❌ Gagal ambil data pemagangan:', xhr.responseText);
                            employees = @json($employee ?? []);
                            reject(xhr);
                        }
                    });
                });
            }

            function calculateWorkdays(start_date, end_date) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: "{{ route('payroll.calculateWorkdays') }}",
                        type: "POST",
                        data: {
                            start_date: start_date,
                            end_date: end_date,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            if (res.success) {
                                let weekdays = res.workdays;
                                $('.workdays').val(weekdays);
                                $('#items-container tr').each(function() {
                                    calculateTotal($(this));
                                });
                                resolve();
                            } else {
                                reject('Response tidak valid');
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal menghitung hari kerja.'
                            });
                            reject(xhr);
                        }
                    });
                });
            }

            function refreshEmployeeDropdowns() {
                $('.employeeSelect').each(function() {
                    const selectedId = $(this).val();
                    $(this).html(createEmpDropdown(selectedId));
                });

                $('#items-container').empty();

                employees.forEach(emp => {
                    // Pass employee data termasuk position saat re-populate
                    addItem(emp.id, {
                        npk: emp.npk,
                        position: emp.position || '-',
                        basic_salary: emp.basic_salary
                    });
                    let lastRow = $('#items-container tr').last();
                    lastRow.find('.employeeSelect').val(emp.id).trigger('change');
                });
            }

            if ((mode === 'edit' || mode === 'view') && payroll && Array.isArray(payroll.payroll_detail)) {
                payroll.payroll_detail.forEach(detail => {
                    const exists = employees.some(e => String(e.id) === String(detail.user_id));
                    if (!exists) {
                        employees.push({
                            id: detail.user_id,
                            name: detail.user_name || `Nonaktif - ID ${detail.user_id}`,
                            npk: detail.npk || '-',
                            basic_salary: detail.basic_salary || 0,
                            active: false
                        });
                    }
                });
            }

            // createEmpDropdown: selalu tampilkan opsi yang belum dipilih, tapi selalu
            // sertakan option untuk selectedId supaya tidak hilang saat edit
            function createEmpDropdown(selectedId = null, disabled = false) {
                let select = `
                    <select name="user_id[]" class="form-select employeeSelect" ${disabled ? 'disabled' : ''}>
                    <option value="">Pilih Employee</option>
                `;

                employees.forEach(emp => {
                    let basicSalary = emp.basic_salary && emp.basic_salary > 0 ? emp.basic_salary : 2500000;

                    if (!selectedEmployees.includes(emp.id) || String(selectedId) === String(emp.id)) {
                        select += `
                            <option 
                                value="${emp.id}" 
                                ${String(selectedId) === String(emp.id) ? 'selected' : ''}
                                data-npk="${emp.npk}" 
                                data-basic_salary="${basicSalary}"
                                data-position="${emp.position || '-'}">
                                ${emp.name}
                            </option>
                        `;
                    }
                });

                select += `</select>`;
                return select;
            }

            // addItem: tambahkan baris. data bisa berisi prefilled fields (untuk edit/view)
            function addItem(selectedId = null, data = {}) {
                let isView = mode === 'view';
                let role = `{{ Auth::user()->getRole() }}`;
                let actionButtons = '';

                if (!isView && ['admin', 'admin 2', 'admin 3', 'admin 4'].includes(role)) {
                    actionButtons =
                        `<button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="ti ti-trash"></i></button>`;
                }
                let defaultBasic = data.basic_salary ?? 2500000;
                
                let rowNumber = $('#items-container tr').length + 1;

                let newRow = $(`
            <tr class="item-row">
                 <td class="row-number">${rowNumber}</td>
                <td><input type="text" name="npk[]" class="form-control npk" value="${data.npk ?? ''}" readonly></td>
                <td>${createEmpDropdown(selectedId, isView)}</td>
                <td><input type="text" name="position[]" class="form-control position" value="${data.position ?? ''}" readonly></td>
                <td><input type="number" name="work_days[]" class="form-control workdays" ${isView ? 'readonly' : ''} min="0" value="${data.work_days ?? ''}"></td>
                <td><input type="number" name="attendance[]" class="form-control attendance" ${isView ? 'readonly' : ''} min="0" value="${data.total_attend ?? ''}"></td>
                <td>
                    <input type="text" name="basic_salary_display[]" class="form-control basic-salary" ${isView ? 'readonly' : ''} value="${formatRupiah(defaultBasic)}" placeholder="Masukkan gaji...">
                    <input type="hidden" name="basic_salary[]" class="basic-salary-raw" value="${defaultBasic}">
                </td>
                <td>
                    <input type="text" name="total_salary_display[]" class="form-control total-salary" readonly value="${data.total_salary ? formatRupiah(data.total_salary) : ''}">
                    <input type="hidden" name="total_salary[]" class="total-salary-raw" value="${data.total_salary ?? ''}">
                </td>
                <td><input type="text" name="remark[]" class="form-control" ${isView ? 'readonly' : ''} value="${data.note ?? ''}"></td>
                <td>${actionButtons}</td>
            </tr>
        `);

                $('#items-container').append(newRow);

                // Jika ada selectedId (dari edit), masukkan ke selectedEmployees agar tidak muncul di dropdown lain
                if (selectedId) {
                    selectedEmployees.push(parseInt(selectedId));
                    refreshAllDropdowns();
                }

                // update total employee count
                updateEmployeeCount();
            }

            $('#add-item').click(function() {
                addItem();
            });

            $('#default_attendance').on('input', function() {
                if (mode === 'view') return;
                let defaultValue = parseInt($(this).val()) || 0;

                if (defaultValue > 0) {
                    $('.attendance').each(function() {
                        $(this).val(defaultValue);
                        let row = $(this).closest('tr');
                        calculateTotal(row);
                    });
                }
            });

            // refresh dropdown HTML di setiap baris berdasarkan selectedEmployees
            function refreshAllDropdowns() {
                $('.item-row').each(function() {
                    const row = $(this);
                    const currentVal = row.find('.employeeSelect').val();
                    // rebuild select HTML but keep current selection
                    const newSelectHtml = $(createEmpDropdown(currentVal, mode === 'view')).html();
                    row.find('.employeeSelect').html(newSelectHtml);
                    // restore selection (some browsers may need val reset)
                    if (currentVal) row.find('.employeeSelect').val(currentVal);
                });
            }

            function formatRupiah(x) {
                if (!x && x !== 0) return '';
                x = String(x).replace(/[^\d]/g, '');
                return x.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function parseRupiah(value) {
                if (!value) return 0;
                return parseInt(String(value).replace(/\./g, '')) || 0;
            }

            function calculateTotal(row) {
                let basicSalary = parseRupiah(row.find('.basic-salary').val()) || 2500000;
                let workDays = parseInt(row.find('.workdays').val()) || 0;
                let attendance = parseInt(row.find('.attendance').val()) || 0;
                if (workDays > 0 && attendance >= 0 && basicSalary > 0) {
                    let total = Math.round((basicSalary / workDays) * attendance);
                    row.find('.total-salary').val(formatRupiah(total));
                    row.find('.total-salary-raw').val(total);
                } else {
                    row.find('.total-salary').val('');
                    row.find('.total-salary-raw').val('');
                }
            }

            // --- event handlers ---

            // employeeSelect change: hanya untuk update fields saat user memilih manual (bukan saat fill edit)
            $(document).on('change', '.employeeSelect', function() {
                let row = $(this).closest('tr');
                let emp = $(this).find(':selected').data() || {};
                let empId = $(this).val();

                // set NPK
                row.find('.npk').val(emp.npk || '');

                // set position
                row.find('.position').val(emp.position || '-');

                // set basic salary if available; otherwise leave editable
                let basicSalary = parseInt(emp.basic_salary) || 0;
                if (basicSalary > 0) {
                    row.find('.basic-salary').val(formatRupiah(basicSalary)).prop('readonly', true);
                    row.find('.basic-salary-raw').val(basicSalary);
                } else {
                    row.find('.basic-salary').val('').prop('readonly', false);
                    row.find('.basic-salary-raw').val('');
                }

                // update selectedEmployees and refresh other dropdowns
                selectedEmployees = [];
                $('.employeeSelect').each(function() {
                    let v = $(this).val();
                    if (v) selectedEmployees.push(parseInt(v));
                });
                refreshAllDropdowns();

                // update total employee count
                updateEmployeeCount();

                calculateTotal(row);
            });

            // numeric inputs change -> recalc
            $(document).on('input', '.workdays, .attendance, .basic-salary', function() {
                let row = $(this).closest('tr');
                if ($(this).hasClass('basic-salary')) {
                    let v = $(this).val().replace(/[^\d]/g, '');
                    $(this).val(formatRupiah(v));
                    row.find('.basic-salary-raw').val(v);
                }
                calculateTotal(row);
            });

            // remove item
            $(document).on('click', '.remove-item', function() {
                let row = $(this).closest('tr');
                let empId = parseInt(row.find('.employeeSelect').val());
                if (empId) {
                    selectedEmployees = selectedEmployees.filter(id => id !== empId);
                }
                row.remove();
                refreshAllDropdowns();
                updateEmployeeCount();
            });

            // update total employee count
            function updateEmployeeCount() {
                let count = $('#items-container tr').length;
                $('#employee-count').text(count);
            }

            // --- initialization: separate flows for create vs edit/view ---

            if (mode === 'edit' || mode === 'view') {
                // if payroll present, populate rows from payroll.payroll_detail
                if (payroll && Array.isArray(payroll.payroll_detail)) {
                    console.log('Edit mode - Employees:', employees);
                    console.log('Payroll details:', payroll.payroll_detail);
                    
                    payroll.payroll_detail.forEach(detail => {
                        // Find employee dari array berdasarkan user_id (convert to string untuk safety)
                        let emp = employees.find(e => String(e.id) === String(detail.user_id));
                        let empPosition = emp ? (emp.position || '-') : '-';
                        
                        console.log(`Detail user_id: ${detail.user_id}, Found emp:`, emp, 'Position:', empPosition);
                        
                        // use field names from backend: user_id, npk, work_days, total_attend, basic_salary, total_salary, note
                        addItem(detail.user_id, {
                            npk: detail.npk,
                            position: empPosition,
                            work_days: detail.work_days,
                            total_attend: detail.total_attend,
                            basic_salary: detail.basic_salary,
                            total_salary: detail.total_salary,
                            note: detail.note
                        });

                        // after addItem we must set inputs of last row explicitly (don't trigger change)
                        let lastRow = $('#items-container tr').last();
                        // set select value (createEmpDropdown already included the selected option)
                        lastRow.find('.employeeSelect').val(detail.user_id);
                        // set other fields (some already set by addItem via data, but ensure)
                        lastRow.find('.npk').val(detail.npk || '');
                        lastRow.find('.position').val(empPosition || '-');
                        lastRow.find('.workdays').val(detail.work_days || detail.work_days === 0 ? detail
                            .work_days : '');
                        lastRow.find('.attendance').val(detail.total_attend || '');
                        lastRow.find('.basic-salary').val(detail.basic_salary ? formatRupiah(detail
                            .basic_salary) : '');
                        lastRow.find('.basic-salary-raw').val(detail.basic_salary || '');
                        lastRow.find('.total-salary').val(detail.total_salary ? formatRupiah(detail
                            .total_salary) : '');
                        lastRow.find('.total-salary-raw').val(detail.total_salary || '');
                        lastRow.find('[name="remark[]"]').val(detail.note || '');

                        // push to selectedEmployees so dropdowns will exclude it
                        if (detail.user_id) selectedEmployees.push(parseInt(detail.user_id));
                    });

                    // after populating all rows, rebuild dropdown options to exclude selected ones
                    refreshAllDropdowns();
                } else {
                    // no details -> add one empty row
                    addItem();
                }

                // fill header fields
                $('#title').val(payroll.title ?? '');
                $('#start_date').val(payroll.start_date ?? '');
                $('#end_date').val(payroll.end_date ?? '');
            } else {
                // create mode: add a single empty row (safer than auto-adding all employees)
                console.log('Create mode - Employees:', employees);
                employees.forEach(emp => {
                    // Pass employee data langsung ke addItem sehingga position muncul dari awal
                    addItem(emp.id, {
                        npk: emp.npk,
                        position: emp.position || '-',
                        basic_salary: emp.basic_salary
                    });
                    let lastRow = $('#items-container tr').last();
                    lastRow.find('.employeeSelect').val(emp.id);
                });
                console.log('Create mode - Rows populated');
            }

            if (mode === 'view') {
                $('#add-item').hide();
                $('#submitPayroll').hide();
                // disable selects and inputs entirely
                $('#items-container').find('input, select').prop('disabled', true);
            }

            // --- weekdays auto-fill (same as before) ---
            function countWeekdays(startDate, endDate) {
                let start = new Date(startDate);
                let end = new Date(endDate);
                let count = 0;
                for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                    let day = d.getDay();
                    if (day >= 1 && day <= 5) count++;
                }
                return count;
            }

            // --- submit ---
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#submitPayroll').click(function() {
                if (mode === 'view') return;
                let payload = {
                    title: $('#title').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    details: []
                };

                $('#items-container tr').each(function() {
                    let row = $(this);
                    let userId = row.find('.employeeSelect').val();
                    if (userId) {
                        payload.details.push({
                            user_id: userId,
                            user_name: row.find('.employeeSelect option:selected').text()
                                .trim(),
                            npk: row.find('.npk').val(),
                            work_days: row.find('.workdays').val(),
                            attendance: row.find('.attendance').val(),
                            basic_salary: row.find('.basic-salary-raw').val(),
                            total_salary: row.find('.total-salary-raw').val(),
                            note: row.find('[name="remark[]"]').val(),
                        });
                    }
                });

                $.ajax({
                    url: mode === 'edit' ?
                        "{{ route('payroll.update', ['id' => $payroll->id ?? 0]) }}" :
                        "{{ route('payroll.store') }}",
                    type: mode === 'edit' ? 'PUT' : 'POST',
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                    success: function(res) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Data payroll berhasil disimpan.'
                            })
                            .then(() => window.location.href = "{{ route('payroll.index') }}");
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                        });
                    }
                });
            });

        });
    </script>
@endpush
