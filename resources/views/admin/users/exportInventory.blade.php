@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">Export Inventory Karyawan</div>
        <div class="card-body">
            <form action="{{ route('inventory.export') }}" method="GET">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="id">Pilih Karyawan (Kosongkan untuk export semua):</label>
                            <select name="id" id="emp_select" class="form-control select2">
                                <option value="">-- Semua Karyawan --</option>
                                @foreach ($allUsers as $u)
                                    <option value="{{ $u->id }}">{{ $u->npk }} - {{ $u->fullname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-file-excel"></i> Export ke Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // $('.select2').select2();
            const emp_select = $("#emp_select");
            
            function initSelect2(element) {
                element.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Select an option'
                });
            }

            initSelect2(emp_select);
        });
    </script>
@endpush
