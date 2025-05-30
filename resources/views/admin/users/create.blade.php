@extends('layouts.admin')

@push('styles')
    <!-- Quill Editor CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Dropzone CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.css">
@endpush

@section('content')
    <div class="card p-4 mt-5">
        <h2>Create New Karyawan</h2>

        <form id="usersForm" action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row mb-3">
                <div class="col-sm-4 col-md-4 col-lg-4 mb-3">
                    <label for="name" class="form-label">NPK</label>
                    <input type="number" class="form-control" id="name" name="npk" value="{{ old('npk') }}"
                        required>
                    @error('npk')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-sm-4 col-md-4 col-lg-4 mb-3">
                    <label for="date" class="form-label">Password</label>
                    <input type="text" class="form-control" id="password" name="password" value="Avi123!" required>
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-sm-4 col-md-4 col-lg-4 mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" class="form-control" id="role">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ Str::ucfirst($role->role_name) }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>


            {{-- <div class="row mb-3">
                <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                    <label for="division_id" class="form-label">Division</label>
                    <select type="text" class="form-control" id="division_id" name="division_id">
                        <option value="">Select Division</option>
                        @foreach ($divisions as $division)
                            <option value="{{ $division->id }}">
                                {{ $division->division_name }}</option>
                        @endforeach
                    </select>
                    @error('division_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select type="text" class="form-control" id="department_id" name="department_id">
                        <option value="">Select Department</option>
                    </select>
                    @error('department_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                    <label for="position_id" class="form-label">Posisi</label>
                    <select name="position_id" id="position_id" class="form-select">
                        <option value="">Select Position</option>
                    </select>
                    @error('position_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div> --}}

            <button type="submit" class="btn btn-primary">Save User</button>
        </form>
    </div>
@endsection

@push('scripts')
    <!-- Quill Editor JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <!-- Dropzone JS -->
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.js"></script>

    <script>
        document.getElementById('usersForm').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to save this event.",
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
            const divisionSelect = $("#division_id");
            const departmentSelect = $("#department_id");
            const positionSelect = $("#position_id");

            // Data dari backend
            const positionsData = @json($positions);
            const departmentsData = @json($departments);
            const divisionsData = @json($divisions);

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
            });

        });
    </script>
@endpush
