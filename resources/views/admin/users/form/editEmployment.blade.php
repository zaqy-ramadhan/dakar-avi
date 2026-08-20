@extends('layouts.admin')

@section('content')
    @include('admin.users.profileCard')
    {{-- @dd(request('prev')) --}}
    <div class="card" style="border-radius:20px">
        <div class="card-header">
            <p class="fs-6 fw-bold">Edit Employment</p>
        </div>
        <div class="card-body">
            <a href="{{ request('prev') ? route(request('prev'), $job->user_id) : url()->previous() }}"
                class="btn btn-outline-primary fs-4 mb-4">
                <i class="ti ti-chevron-left fs-4"></i> Kembali
            </a>
            <form class="mb-4" id="jobEmploymentForm{{ $job->id }}"
                action="{{ route('employee-jobs.update', $job->id) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @csrf

                <div class="row mb-3">
                    <div class="col-sm-6 col-md-3 col-lg-3 mb-3">
                        <label for="division_id" class="form-label">Division</label>
                        <select type="text" class="form-control" id="division_id" name="division_id"
                            @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                            <option value="">Select Division</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}" @if (optional($job)->division_id == $division->id) selected @endif>
                                    {{ $division->division_name }}</option>
                            @endforeach
                        </select>
                        @error('division_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 col-md-3 col-lg-3 mb-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select type="text" class="form-control" id="department_id" name="department_id"
                            @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                            <option value="">Select Department</option>
                        </select>
                        @error('department_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 col-md-3 col-lg-3 mb-3">
                        <label for="section_id" class="form-label">Section</label>
                        <select name="section_id" id="section_id" class="form-select"
                            @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                            <option value="">Select Section</option>
                        </select>
                        @error('section_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 col-md-3 col-lg-3 mb-3">
                        <label for="position_id" class="form-label">Position</label>
                        <select name="position_id" id="position_id" class="form-select"
                            @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                            <option value="">Select Position</option>
                        </select>
                        @error('position_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 col-md-3 col-lg-3 mb-3">
                        <label for="station_id" class="form-label">Station</label>
                        <select name="station_id" id="station_id" class="form-select"
                            @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                            <option value="">Select Station</option>
                        </select>
                        @error('station_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="internship_fields" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                            <label for="" class="form-label">Cost Center</label>
                            <select name="cost_center_id" id="cost_center_id" class="form-select"
                                @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                                <option value="">Select Cost Center</option>
                                @foreach ($costCenters as $costCenter)
                                    <option value="{{ $costCenter->id }}" @if (optional($job)->cost_center_id == $costCenter->id) selected @endif>
                                        {{ $costCenter->cost_center_name }}</option>
                                @endforeach
                            </select>
                            @error('cost_center_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                            <label for="" class="form-label">Level</label>
                            <select name="level_id" id="level_id" class="form-select"
                                @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                                <option value="">Select Level</option>
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}" @if (optional($job)->role_level_id == $level->id) selected @endif>
                                        {{ $level->level_name }}</option>
                                @endforeach
                            </select>
                            @error('level_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-3 col-md-4 col-lg-4 mb-3">
                            <label for="" class="form-label">Job Type</label>
                            <select name="job_type_id" id="job_type_id" class="form-select"
                                @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                                <option value="">Select Job Type</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}"
                                        @if (optional($job)->job_type_id == $type->id) selected @endif>
                                        {{ $type->job_type_name }}</option>
                                @endforeach
                            </select>
                            @error('job_type_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3 col-md-2 col-lg-2 mb-3">
                            <label for="" class="form-label">Group</label>
                            <select name="group_id" id="group_id" class="form-select"
                                @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                                <option value="">Select Group</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}"
                                        @if (optional($job)->group_id == $group->id) selected @endif>
                                        {{ $group->group_name }}</option>
                                @endforeach
                            </select>
                            @error('group_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-3 col-md-2 col-lg-2 mb-3">
                            <label for="" class="form-label">Line</label>
                            <select name="line_id" id="line_id" class="form-select"
                                @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                                <option value="">Select Line</option>
                                @foreach ($lines as $line)
                                    <option value="{{ $line->id }}"
                                        @if (optional($job)->line_id == $line->id) selected @endif>
                                        {{ $line->line_name }}</option>
                                @endforeach
                            </select>
                            @error('line_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-3 col-md-2 col-lg-2 mb-3">
                            <label for="" class="form-label">Gol</label>
                            <select name="golongan_id" id="golongan_id" class="form-select"
                                @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                                <option value="">Gol</option>
                                @foreach ($golongans as $golongan)
                                    <option value="{{ $golongan->id }}"
                                        @if (optional($job)->golongan_id == $golongan->id) selected @endif>
                                        {{ $golongan->golongan_name }}</option>
                                @endforeach
                            </select>
                            @error('golongan_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-3 col-md-2 col-lg-2 mb-3">
                            <label for="" class="form-label">Sub Gol</label>
                            <select name="sub_golongan_id" id="sub_golongan_id" class="form-select"
                                @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                                <option value="">Sub Gol</option>
                                @foreach ($sub_golongans as $sub_golongan)
                                    <option value="{{ $sub_golongan->id }}"
                                        @if (optional($job)->sub_golongan_id == $sub_golongan->id) selected @endif>
                                        {{ $sub_golongan->sub_golongan_name }}</option>
                                @endforeach
                            </select>
                            @error('sub_golongan_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                            <label for="doc_sk" class="form-label">Dokumen / SK </label>
                            <input type="file" class="form-control" id="doc_sk" name="doc_sk">
                            @if ($doc)
                                <p>File yang ada: <a href="{{ asset('storage/' . $doc->path) }}" target="_blank">Lihat File</a></p>
                            @endif
                            @error('doc_sk')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                        <label for="" class="form-label">Start date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ \Carbon\Carbon::parse($job->start_date ?? null)->format('Y-m-d') }}"
                            @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                        @error('start_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                        <label for="end_date" class="form-label">End date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ $job->end_date ? \Carbon\Carbon::parse($job->end_date)->format('Y-m-d') : '' }}"
                            @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                        @error('end_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                        <label for="resign_date" class="form-label">Out date</label>
                        <input type="date" class="form-control" id="resign_date" name="resign_date"
                            value="{{ $job->resign_date ? \Carbon\Carbon::parse($job->resign_date)->format('Y-m-d') : '' }}"
                            @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                        @error('resign_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                        <label for="job_status" class="form-label">Job status</label>
                        <select name="job_status" id="job_status" class="form-select"
                            @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                            @foreach ($jobStatus as $status)
                                <option data-select="{{ $status->job_status_name }}"
                                    value="{{ $status->job_status_name }}"
                                    @if (optional($job)->job_status == $status->job_status_name) selected @endif>
                                    {{ Str::ucfirst($status->job_status_name) }}</option>
                            @endforeach
                        </select>
                        @error('job_status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                        <label for="employment_status" class="form-label">Employment Status</label>
                        <select name="employment_status" id="employment_status" class="form-select"
                            @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                            @foreach ($roles as $role)
                                <option data-select = "{{ $role->role_name }}" value="{{ $role->id }}"
                                    @if (optional($job)->user_dakar_role == $role->role_name) selected @endif>
                                    {{ Str::ucfirst($role->role_name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('employment_status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                        <label for="work_hour" class="form-label">Work Hour Code</label>
                        <select name="work_hour" id="work_hour" class="form-select"
                            @if (Request::is('*onboarding*') && $user->firstEmployeeJob != null) disabled @endif>
                            <option value="">Select Work Hour</option>
                            @foreach ($workHour as $code)
                                <option data-select = "{{ $code->work_hour }}" value="{{ $code->id }}"
                                    @if (optional($job)->work_hour_code_id == $code->id) selected @endif>
                                    {{ Str::ucfirst($code->work_hour) }}
                                </option>
                            @endforeach
                        </select>
                        @error('work_hour')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                        <label for="npk" class="form-label">Update NPK</label>
                        <input type="text" class="form-control" id="npk" name="npk"
                            value="{{ $job->npk }}">
                        @error('npk')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="1" @if ($job->employment_status == 1) selected @endif>Active</option>
                            <option value="0" @if ($job->employment_status == 0) selected @endif>Inactive</option>
                        </select>
                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                            <label for="" class="form-label">Employment Note</label>
                            <select class="form-select" name="employee_transfer" id="">
                                <option @if (optional($user->employeeJob?->last())->employee_transfer == 'baru') selected @endif value="baru">Baru</option>
                                <option @if (optional($user->employeeJob?->last())->employee_transfer == 'ekstensi') selected @endif value="ekstensi">Ekstensi</option>
                                <option @if (optional($user->employeeJob?->last())->employee_transfer == 'mutasi') selected @endif value="mutasi">Mutasi</option>
                                <option @if (optional($user->employeeJob?->last())->employee_transfer == 'promosi') selected @endif value="promosi">Promosi</option>
                                <option @if (optional($user->employeeJob?->last())->employee_transfer == 'kartap') selected @endif value="kartap">Kartap</option>
                            </select>
                    </div>

                    <div id="permanent_fields" style="display: block;" class="col-sm-6 col-md-4 col-lg-4 mb-3">
                        <label for="permanent_docs" class="form-label">Dokumen / SK </label>
                        <input type="file" class="form-control" id="permanent_docs" name="permanent_docs">
                        {{-- @dd($job->jobDoc?->where('type', 'permanent_docs')->first()) --}}
                        @if ($job->jobDoc?->where('type', 'permanent_docs')->first())
                            <p>File yang ada: <a
                                    href="{{ asset('storage/' . $job->jobDoc?->where('type', 'permanent_docs')?->first()?->path) }}"
                                    target="_blank">Lihat
                                    File</a></p>
                        @endif
                        @error('permanent_docs')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 col-md-4 col-lg-4 mb-3 d-flex align-items-end">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" role="switch" name="flag_wfh" id="flag_wfh" value="1"
                                @if (optional($job)->flag_wfh) checked @endif>
                            <label class="form-check-label" for="flag_wfh">
                                Flag WFH
                            </label>
                        </div>
                    </div>

                </div>

                @if ($user->firstEmployeeJob === null && Request::is('*onboarding*'))
                    <button type="submit" class="btn btn-primary">Submit</button>
                @endif

                @if (!Request::is('*boarding*'))
                    <button type="submit" class="btn btn-primary">Submit</button>
                @endif
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const employeeStatus = document.getElementById("employment_status");
            const jobStatus = document.getElementById("job_status");
            const internshipFields = document.getElementById("internship_fields");
            const permanentFields = document.getElementById("permanent_fields");

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

            function toggleTetapFields() {
                const selectedJobStatus = jobStatus.options[jobStatus.selectedIndex];
                const selectedJobStatusValue = selectedJobStatus.getAttribute("data-select");

                if (selectedJobStatusValue === "tetap") {
                    console.log(selectedJobStatusValue);
                    permanentFields.style.display = "block";
                } else {
                    console.log(selectedJobStatusValue);
                    permanentFields.style.display = "none";
                }
            }


            employeeStatus.addEventListener("change", toggleInternFields);
            toggleInternFields();

            jobStatus.addEventListener("change", toggleTetapFields);
            toggleTetapFields();
        });
    </script>
    <script>
        $(document).ready(function() {
            const divisionSelect = $("#division_id");
            const departmentSelect = $("#department_id");
            const sectionSelect = $("#section_id");
            const stationSelect = $("#station_id");
            const positionSelect = $("#position_id");

            // Data dari backend
            const positionsData = @json($positions);
            const sectionsData = @json($sections);
            const stationsData = @json($stations);
            const departmentsData = @json($departments);
            const divisionsData = @json($divisions);

            // Ambil data lama dari Blade
            const oldDivisionId =
                "{{ optional($job)->division_id }}";
            const oldDepartmentId =
                "{{ optional($job)->department_id }}";
            const oldPositionId = "{{ optional($job)->position_id }}";
            const oldSectionId = "{{ optional($job)->section_id }}";
            const oldStationId = "{{ optional($job)->station_id }}";

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
            initSelect2(stationSelect);

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

            // Load data station berdasarkan department yang dipilih
            function loadStations(departmentId, selectedStation = null) {
                stationSelect.empty().append(new Option('Select Station', '', true, true));

                stationsData.forEach(station => {
                    if (station.department_id == departmentId) {
                        const option = new Option(station.station_name, station.id);
                        stationSelect.append(option);
                        if (selectedStation && station.id == selectedStation) {
                            $(option).prop("selected", true);
                        }
                    }
                });

                stationSelect.trigger('change');
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
                loadSections(departmentId);
                loadStations(departmentId);
            });

            // Jika ada data lama, isi otomatis
            if (oldDivisionId) {
                divisionSelect.val(oldDivisionId).trigger('change');
                loadDepartments(oldDivisionId, oldDepartmentId);
            }

            if (oldDepartmentId) {
                loadPositions(oldDepartmentId, oldPositionId);
                loadSections(oldDepartmentId, oldSectionId);
                loadStations(oldDepartmentId, oldStationId);
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // const positionSelect = $("#position_id");
            const CostCenterSelect = $("#cost_center_id");
            const levelSelect = $("#level_id");

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
        });
    </script>
@endpush
