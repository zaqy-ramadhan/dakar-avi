@include('admin.users.profileCard')

@push('styles')
    <style>
        .step-container {
            position: relative;
        }

        .step-container::before {
            content: '';
            position: absolute;
            top: 18px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #dee2e6;
            z-index: 0;
        }

        .step {
            position: relative;
            z-index: 1;
            flex: 1;
        }

        .circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #dee2e6;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #6c757d;
            margin: 0 auto;
            z-index: 1;
        }

        .circle.active {
            background-color: #0d6efd;
            color: white;
        }

        .label {
            font-size: 0.8rem;
            margin-top: 5px;
            color: #495057;
        }
    </style>
@endpush

@if (Request::is('*onboarding*'))
    <div class="card p-4" style="border-radius: 20px">
        <div class="step-container d-flex justify-content-between align-items-center position-relative mb-4">
            <div class="step text-center">
                <div class="circle active"><i class="ti ti-plus"></i></div>
                <div class="label">Add New Employee</div>
                <div class="text-muted small">{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</div>
            </div>

            <div class="step text-center">
                <div class="circle @if ($personal_status) active @endif"><i class="ti ti-user"></i></div>
                <div class="label">
                    Personal Data
                    @if ($personal_status && !empty($personal_date))
                        <div class="text-muted small">{{ \Carbon\Carbon::parse($personal_date)->format('d M Y') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="step text-center">
                <div class="circle @if ($employment_status) active @endif"><i class="ti ti-clipboard-text"></i>
                </div>
                <div class="label">
                    Employment
                    @if ($employment_status && !empty($employment_date))
                        <div class="text-muted small">{{ \Carbon\Carbon::parse($employment_date)->format('d M Y') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="step text-center">
                <div class="circle @if ($inventories_status) active @endif"><i class="ti ti-checklist"></i>
                </div>
                <div class="label">
                    Starter Kit
                    @if ($inventories_status && !empty($inventories_date))
                        <div class="text-muted small">{{ \Carbon\Carbon::parse($inventories_date)->format('d M Y') }}
                        </div>
                    @endif
                </div>
            </div>
            @if (optional($user->firstEmployeeJob)->user_dakar_role === 'karyawan')
                <div class="step text-center">
                    <div class="circle @if ($inumber_status) active @endif"><i class="ti ti-apps"></i></div>
                    <div class="label">
                        Digital Account
                        @if ($inumber_status && !empty($inumber_date))
                            <div class="text-muted small">{{ \Carbon\Carbon::parse($inumber_date)->format('d M Y') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif



<ul class="nav nav-tabs" id="myTab" role="tablist">
    @if (Auth::user()->getRole() === 'admin 4')
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="contract-tab" data-bs-toggle="tab" data-bs-target="#contract"
                type="button" role="tab" aria-controls="contract" aria-selected="true">Employment</button>
        </li>
        <li class="nav-item" role="presentation">
            @if (optional($user->employeeJob)->isNotEmpty() && $user->employeeDetail)
                <button class="nav-link" id="checklist-tab" data-bs-toggle="tab" data-bs-target="#inventory"
                    type="button" role="tab" aria-controls="inventory" aria-selected="false">Starter Kit</button>
            @endif
        </li>
    @else
        @if (!$user->employeeDetail)
            <li class="nav-item" role="presentation">
                <button class="nav-link disabled" type="button" aria-disabled="true">
                    Karyawan Belum Mengisi Data Pribadi.
                </button>
            </li>
        @else
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="contract-tab" data-bs-toggle="tab" data-bs-target="#contract"
                    type="button" role="tab" aria-controls="contract" aria-selected="true">Employment</button>
            </li>
        @endif
        @if (Request::is('*onboarding*') && in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']))
            @if ($user->firstEmployeeJob)
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="wage-tab" data-bs-toggle="tab" data-bs-target="#wage" type="button"
                        role="tab" aria-controls="wage" aria-selected="false">Wage/Allowance</button>
                </li>
            @endif
        @endif
        <li class="nav-item" role="presentation">
            @if (optional($user->employeeJob)->isNotEmpty() && $user->employeeDetail)
                <button class="nav-link" id="checklist-tab" data-bs-toggle="tab" data-bs-target="#inventory"
                    type="button" role="tab" aria-controls="inventory" aria-selected="false">Starter Kit</button>
            @endif
        </li>
        @if (Request::is('*onboarding*'))
            @if (optional($user->firstEmployeeJob)->user_dakar_role === 'karyawan')
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="checklist-tab" data-bs-toggle="tab" data-bs-target="#ga" type="button"
                        role="tab" aria-controls="ga" aria-selected="false">Kepesertaan & Akun Digital</button>
                </li>
            @endif
        @elseif(Request::is('*employment*'))
            @if (optional($user->latestEmployeeJob)->user_dakar_role === 'karyawan')
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="checklist-tab" data-bs-toggle="tab" data-bs-target="#ga"
                        type="button" role="tab" aria-controls="ga" aria-selected="false">Kepesertaan & Akun
                        Digital</button>
                </li>
            @endif
        @endif
    @endif
</ul>

<div class="tab-pane py-4 px-2 fade show active" id="contract" role="tabpanel" aria-labelledby="contract-tab">
    @if (in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']) && $user->employeeDetail)
        @if (Request::is('*onboarding*') || Request::is('*employment*'))
            @include('admin.users.form.employment')
        @endif
    @endif

    @if (Request::is('*offboarding*'))
        @include('admin.users.form.resign')
    @endif

    {{-- @if (!Request::is('*onboarding*')) --}}
    <div class="card" style="overflow-x: auto; width: 100%;">
        <table id="datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Level</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Duration</th>
                    <th>Job Type</th>
                    <th>Gol</th>
                    <th>Sub Gol</th>
                    <th>Group</th>
                    <th>Line</th>
                    <th>Job Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
    {{-- @endif --}}

</div>


<div class="tab-pane py-4 px-2 fade" id="inventory" role="tabpanel" aria-labelledby="inventory-tab">
    @include('admin.users.form.inventory')
</div>

@if (Request::is('*onboarding*'))
    @if (optional($user->firstEmployeeJob)->user_dakar_role === 'karyawan')
        <div class="tab-pane py-4 px-2 fade" id="ga" role="tabpanel" aria-labelledby="inventory-tab">
            @include('admin.users.form.inventoryNumber')
        </div>
    @endif

    @if ($user->firstEmployeeJob && !Request::is('*employment*'))
        <div class="tab-pane py-4 px-2 fade" id="wage" role="tabpanel" aria-labelledby="inventory-tab">
            @php
                $jobEmploymentId = optional($user->firstEmployeeJob)->id;
            @endphp
            @include('admin.users.form.wage')
        </div>
    @endif
@endif

@if (Request::is('*employment*') && optional($user->latestEmployeeJob)->user_dakar_role === 'karyawan')
    <div class="tab-pane py-4 px-2 fade" id="ga" role="tabpanel" aria-labelledby="inventory-tab">
        @include('admin.users.form.inventoryNumber')
    </div>
@endif

{{-- {{ request()->route()->getName() }} --}}




@push('scripts')
    {{-- {{ $dataTable->scripts(attributes: ['type' => 'module']) }} --}}

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const employeeStatus = document.getElementById("employment_status");
            const internshipFields = document.getElementById("internship_fields");

            function toggleInternFields() {
                const selectedOption = employeeStatus.options[employeeStatus.selectedIndex];
                const selectedValue = selectedOption.getAttribute("data-select");
                // console.log(selectedValue);

                if (selectedValue === "internship") {
                    internshipFields.style.display = "none";
                } else {
                    internshipFields.style.display = "block";
                }
            }

            employeeStatus.addEventListener("change", toggleInternFields);
            toggleInternFields();
        });
    </script>

    <script>
        document.getElementById('jobEmploymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to save this job employment.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!',
                buttonsStyling: false,
                customClass: {
                    popup: 'small-swal',
                    confirmButton: 'btn btn-outline-primary mx-2',
                    cancelButton: 'btn btn-outline-danger mx-2',
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('employee-jobs.data', $user->id) }}",
                    data: function(d) {
                        d.route = "{{ request()->route()->getName() }}";
                    }
                },
                order: [
                    [1, 'desc']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false,
                        title: 'No.'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        visible: false
                    },
                    {
                        data: 'level',
                        name: 'level',
                        title: 'Level',
                        searchable: true,
                        orderable: true,
                    },
                    {
                        data: 'department',
                        name: 'department',
                        title: 'Department',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'section',
                        name: 'section',
                        title: 'Section',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'position',
                        name: 'position',
                        title: 'Position',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                        title: 'Start Date',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'end_date',
                        name: 'end_date',
                        title: 'End Date',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'resign_date',
                        name: 'resign_date',
                        title: 'Out Date',
                        searchable: true,
                        orderable: true,
                    },
                    {
                        data: 'contract_duration',
                        name: 'contract_duration',
                        title: 'Contract Duration',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'job_type',
                        name: 'job_type',
                        title: 'Job Type',
                        searchable: true,
                        orderable: true
                    },
                    // {
                    //     data: 'golongan',
                    //     name: 'golongan',
                    //     title: 'Gol',
                    //     searchable: true,
                    //     orderable: true
                    // },
                    {
                        data: 'sub_golongan',
                        name: 'sub_golongan',
                        title: 'Sub Gol',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'group',
                        name: 'group',
                        title: 'Group',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'line',
                        name: 'line',
                        title: 'Line',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'job_status',
                        name: 'job_status',
                        title: 'Job Status',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'is_active',
                        title: 'Is Active',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        title: 'Actions',
                        searchable: false,
                        orderable: false
                    }
                ]
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            const divisionSelect = $("#division_id");
            const departmentSelect = $("#department_id");
            const sectionSelect = $("#section_id");
            const positionSelect = $("#position_id");

            // Data dari backend
            const positionsData = @json($positions);
            const sectionsData = @json($sections);
            const departmentsData = @json($departments);
            const divisionsData = @json($divisions);

            // Ambil data lama dari Blade
            const oldDivisionId =
                "{{ optional($user->employeeJob->last())->division_id }}";
            const oldDepartmentId =
                "{{ optional($user->employeeJob->last())->department_id }}";
            const oldPositionId = "{{ optional($user->employeeJob->last())->position_id }}";
            const oldSectionId = "{{ optional($user->employeeJob->last())->section_id }}";

            // Inisialisasi Select2
            function initSelect2(element) {
                element.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Select an option'
                });
            }

            initSelect2(divisionSelect);
            initSelect2(departmentSelect);
            initSelect2(positionSelect);
            initSelect2(sectionSelect);

            // Load data department berdasarkan division yang dipilih
            function loadDepartments(divisionId, selectedDepartment = null) {
                departmentSelect.empty().append(new Option('Select Department', '', true, true));

                departmentsData.forEach(department => {
                    if (department.division_id == divisionId) {
                        const option = new Option(department.department_name, department.id);
                        departmentSelect.append(option);
                        if (selectedDepartment && department.id == selectedDepartment) {
                            $(option).prop("selected", true);
                        }
                    }
                });

                departmentSelect.trigger('change');
            }

            // Load data section berdasarkan department yang dipilih
            function loadSections(departmentId, selectedSection = null) {
                sectionSelect.empty().append(new Option('Select Section', '', true, true));

                sectionsData.forEach(section => {
                    if (section.department_id == departmentId) {
                        const option = new Option(section.section_name, section.id);
                        sectionSelect.append(option);
                        if (selectedSection && section.id == selectedSection) {
                            $(option).prop("selected", true);
                        }
                    }
                });

                sectionSelect.trigger('change');
            }

            // Load data position berdasarkan department yang dipilih
            function loadPositions(departmentId, selectedPosition = null) {
                positionSelect.empty().append(new Option('Select Position', '', true, true));

                positionsData.forEach(position => {
                    if (position.department_id == departmentId) {
                        const option = new Option(position.position_name, position.id);
                        positionSelect.append(option);
                        if (selectedPosition && position.id == selectedPosition) {
                            $(option).prop("selected", true);
                        }
                    }
                });

                positionSelect.trigger('change');
            }

            // Ketika division berubah, update department
            divisionSelect.on("change", function() {
                const divisionId = $(this).val();
                loadDepartments(divisionId);
            });

            // Ketika department berubah, update position
            departmentSelect.on("change", function() {
                const departmentId = $(this).val();
                loadPositions(departmentId);
                loadSections(departmentId)
            });

            // Jika ada data lama, isi otomatis
            if (oldDivisionId) {
                divisionSelect.val(oldDivisionId).trigger('change');
                loadDepartments(oldDivisionId, oldDepartmentId);
            }

            if (oldDepartmentId) {
                loadPositions(oldDepartmentId, oldPositionId);
                loadSections(oldDepartmentId, oldSectionId);
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // const positionSelect = $("#position_id");
            const CostCenterSelect = $("#cost_center_id");
            const levelSelect = $("#level_id");

            // const departmentInput = $("#department_id");
            // const divisionInput = $("#division_id");

            // const data = @json($positions);

            // data.forEach(position => {
            //     const option = new Option(
            //         position.position_name,
            //         position.id,
            //         false,
            //         false
            //     );
            //     $(option).attr("data-department", position.department ? position.department
            //         .department_name : "");
            //     $(option).attr("data-division", position.department && position.department.division ?
            //         position.department.division.division_name : "");
            //     positionSelect.append(option);
            // });

            // // Inisialisasi Select2
            // positionSelect.select2({
            //     theme: 'bootstrap-5',
            //     width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
            //         'style',
            //     placeholder: $(this).data('placeholder'),
            // });

            CostCenterSelect.select2({
                theme: 'bootstrap-5',
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
                    'style',
                placeholder: $(this).data('placeholder'),
            });

            levelSelect.select2({
                theme: 'bootstrap-5',
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
                    'style',
                placeholder: $(this).data('placeholder'),
            });

            // Event saat posisi dipilih
            // positionSelect.on("change", function() {
            //     const selectedOption = $(this).find(":selected");
            //     departmentInput.val(selectedOption.data("department") || "");
            //     divisionInput.val(selectedOption.data("division") || "");
            // });
        });
    </script>
@endpush
