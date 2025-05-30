@php
    $employeeBank = $employeeBank ?? null;
@endphp
<div class="row mb-3">
    <div class="col-sm-4 col-md-4 col-lg-4 mb-3">
        <label for="bank_name" class="form-label">Nama Bank<span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="bank_name" name="bank_name"
            @if ($user->getRole() === 'karyawan') value = "Bank Permata" readonly
             @else
        value="{{ old('bank_name', $employeeBank->bank_name ?? '') }}"> @endif
            <small class="text-muted">
                @if($user->getRole() !== 'karyawan')
                Wajib diisi
                @else
                Wajib menggunakan Bank Permata
                @endif
            </small>
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
@push('scripts')
    <script>
        function debounce(func, delay) {
            let timer;
            return function() {
                const context = this,
                    args = arguments;
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(context, args), delay);
            };
        }

        // Autosave function for bank details
        function autosaveBank() {
            $.ajax({
                url: "{{ route('autosave.bank', $user->id) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    bank_name: $('#bank_name').val(),
                    account_name: $('#account_name').val(),
                    account_number: $('#account_number').val()
                },
                success: function(response) {
                    console.log('Bank details autosaved.');
                },
                error: function(xhr) {
                    console.error('Failed to autosave bank details');
                }
            });
        }

        // Debounce the autosave function
        const debouncedAutosaveBank = debounce(autosaveBank, 1000);

        // Attach event listeners to input fields
        $('#bank_name, #account_name, #account_number').on('input', debouncedAutosaveBank);
    </script>
    
@endpush