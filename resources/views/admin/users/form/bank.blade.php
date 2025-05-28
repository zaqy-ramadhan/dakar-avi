@php
    $employeeBank = $employeeBank ?? null;
@endphp

<div class="row mb-3">
    <div class="col-sm-4 col-md-4 col-lg-4 mb-3">
        <label for="bank_name" class="form-label">Nama Bank<span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="bank_name" name="bank_name"  
               value="{{ old('bank_name', $employeeBank->bank_name ?? '') }}">
        <small class="text-muted">Wajib diisi</small>
        @error('bank_name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-4 col-md-4 col-lg-4 mb-3">
        <label for="account_name" class="form-label">Nama di Rekening<span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="account_name" name="account_name"  
               value="{{ old('account_name', $employeeBank->account_name ?? '') }}">
        <small class="text-muted">Wajib diisi</small>
        @error('account_name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-4 col-md-4 col-lg-4 mb-3">
        <label for="account_number" class="form-label">Nomor Rekening<span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="account_number" name="account_number"  
               value="{{ old('account_number', $employeeBank->account_number ?? '') }}">
        <small class="text-muted">Wajib diisi</small>
        @error('account_number')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>
