@extends('layouts.admin')

@section('content')
    <div class="container card p-4">
        <h2>{{ isset($rule) ? 'Edit Inventory Rule' : 'Tambah Inventory Rule' }}</h2>

        <form action="{{ isset($rule) ? route('inventory-rules.update', $rule->id) : route('inventory-rules.store') }}"
            method="POST">
            @csrf
            @if (isset($rule))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label class="form-label">Role:</label>
                <select name="dakar_role_id" class="form-select">
                    <option value="">- None -</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ isset($rule) && $rule->dakar_role_id == $role->id ? 'selected' : '' }}>
                            {{ $role->role_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- <div class="mb-3">
            <label class="form-label">Department:</label>
            <select name="department_id" class="form-select">
                <option value="">- None -</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" {{ isset($rule) && $rule->department_id == $department->id ? 'selected' : '' }}>
                        {{ $department->department_name }}
                    </option>
                @endforeach
            </select>
        </div> --}}

            <div class="mb-3">
                <label class="form-label">Department:</label>
                <div id="items-container-dep">
                    <div class="d-flex align-items-center mb-2 dep-group">
                        <select name="department_id[]" class="form-select">
                            <option value="">- None -</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ isset($rule) && $rule->department_id == $department->id ? 'selected' : '' }}>
                                    {{ $department->department_name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-dep"><i
                            class="ti ti-trash"></i></button>
                    </div>
                </div>
                <button type="button" id="add-department" class="btn btn-success mt-2">Tambah Department</button>
            </div>

            {{-- <div class="mb-3">
            <label class="form-label">Level:</label>
            <select name="level_id" class="form-select">
                <option value="">- None -</option>
                @foreach ($levels as $level)
                    <option value="{{ $level->id }}" {{ isset($rule) && $rule->level_id == $level->id ? 'selected' : '' }}>
                        {{ $level->level_name }}
                    </option>
                @endforeach
            </select>
        </div> --}}

            {{-- <div class="mb-3">
            <label class="form-label">Job Status:</label>
            <select name="job_status" class="form-select">
                @foreach ($jobStatus as $status)
                    <option value="{{ $status->job_status_name }}" {{ isset($rule) && $rule->job_status == $status->job_status_name ? 'selected' : '' }}>
                        {{ $status->job_status_name }}
                    </option>
                @endforeach
            </select>
        </div> --}}

            <!-- Pilih Item -->
            <div class="mb-3">
                <label class="form-label">Pilih Item:</label>
                <div id="items-container">
                    <div class="d-flex align-items-center mb-2 item-group">
                        <select name="items[]" class="form-select">
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-item"><i
                                class="ti ti-trash"></i></button>
                    </div>
                </div>
                <button type="button" id="add-item" class="btn btn-success mt-2">Tambah Item</button>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Event untuk menambah select baru
            $('#add-item').click(function() {
                var newSelect = `
                <div class="d-flex align-items-center mb-2 item-group">
                    <select name="items[]" class="form-select">
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-item"><i class="ti ti-trash"></i></button>
                </div>`;
                $('#items-container').append(newSelect);
            });

            $('#add-department').click(function() {
                var newSelect = `
                <div class="d-flex align-items-center mb-2 item-group">
                    <select name="department_id[]" class="form-select">
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-item"><i class="ti ti-trash"></i></button>
                </div>`;
                $('#items-container-dep').append(newSelect);
            });

            // Event untuk menghapus select yang ditambahkan
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.item-group').remove();
            });

            $(document).on('click', '.remove-dep', function() {
                $(this).closest('.dep-group').remove();
            });
        });
    </script>
@endpush
