<div id="personal">
<div class="row mb-3">
    <div class="col-sm-3">
        <label for="npk" class="form-label">NPK</label>
        <input type="number" class="form-control" id="npk" name="npk" value="{{ old('npk', $user->npk ?? '') }}"
            readonly>
        @error('npk')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- @dd($employeeDetail->is_draft) --}}

<div class="row mb-3">
    <div class="col-sm-6 col-md-9 col-lg-7 mb-3">
        <label for="fullname" class="form-label">Nama Lengkap<span class="text-danger">* </span></label>
        <input type="text" class="form-control" id="fullname" name="fullname"
            value="{{ old('fullname', $user->fullname ?? '') }}">
        <small class="text-muted">Wajib diisi</small>
        @error('fullname')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-sm-6 col-md-5 col-lg-3 mb-3">
        <label for="gender" class="form-label">Jenis Kelamin<span class="text-danger">* </span></label>
        <select name="gender" class="form-control" id="gender">
            <option value="0" {{ old('gender', $employeeDetail->gender ?? '') == '0' ? 'selected' : '' }}>Laki-laki
            </option>
            <option value="1" {{ old('gender', $employeeDetail->gender ?? '') == '1' ? 'selected' : '' }}>Perempuan
            </option>
        </select>
        <small class="text-muted">Wajib diisi</small>
        @error('gender')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-sm-6 col-md-4 col-lg-2 mb-3">
        <label for="blood_type" class="form-label">Golongan Darah<span class="text-danger">*</span></label>
        <select name="blood_type" class="form-control" id="blood_type">
            <option value="A" {{ old('blood_type', $employeeDetail->blood_type ?? '') == 'A' ? 'selected' : '' }}>A
            </option>
            <option value="B" {{ old('blood_type', $employeeDetail->blood_type ?? '') == 'B' ? 'selected' : '' }}>B
            </option>
            <option value="AB" {{ old('blood_type', $employeeDetail->blood_type ?? '') == 'AB' ? 'selected' : '' }}>
                AB</option>
            <option value="O" {{ old('blood_type', $employeeDetail->blood_type ?? '') == 'O' ? 'selected' : '' }}>
                O</option>
        </select>
        <small class="text-muted">Wajib diisi</small>
        @error('blood_type')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="birth_place" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="birthplace" name="birth_place"
            value="{{ old('birth_place', $employeeDetail->birth_place ?? '') }}">
        <small class="text-muted">Wajib diisi</small>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="birth_date" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
        <input type="date" class="form-control" id="birthdate" name="birth_date"
            value="{{ old('birth_date', $employeeDetail->birth_date ?? '') }}">
        <small class="text-muted">Wajib diisi</small>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="religion" class="form-label">Agama<span class="text-danger">*</span></label>
        <select name="religion" class="form-control" id="religion">
            <option value="">-- Pilih Agama --</option>
            <option value="Islam" {{ old('religion', $employeeDetail->religion ?? '') == 'Islam' ? 'selected' : '' }}>
                Islam</option>
            <option value="Kristen"
                {{ old('religion', $employeeDetail->religion ?? '') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
            <option value="Katolik"
                {{ old('religion', $employeeDetail->religion ?? '') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
            <option value="Hindu" {{ old('religion', $employeeDetail->religion ?? '') == 'Hindu' ? 'selected' : '' }}>
                Hindu</option>
            <option value="Buddha"
                {{ old('religion', $employeeDetail->religion ?? '') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
            <option value="Konghucu"
                {{ old('religion', $employeeDetail->religion ?? '') == 'Konghucu' ? 'selected' : '' }}>Konghucu
            </option>
            <option value="Lainnya"
                {{ old('religion', $employeeDetail->religion ?? '') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>
        @error('religion')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        <small class="text-muted">Wajib diisi</small>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="no_jamsostek" class="form-label">Nomor Peserta JAMSOSTEK</label>
        <input type="text" class="form-control" id="jamsostek" name="no_jamsostek"
            value="{{ old('no_jamsostek', $employeeDetail->no_jamsostek ?? '') }}">
    </div>
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="no_npwp" class="form-label">No NPWP</label>
        <input type="text" class="form-control" id="npwp" name="no_npwp"
            value="{{ old('no_npwp', $employeeDetail->no_npwp ?? '') }}">
    </div>
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="no_ktp" class="form-label">No. KTP<span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="ktp" name="no_ktp"
            value="{{ old('no_ktp', $employeeDetail->no_ktp ?? '') }}">
        <small class="text-muted">Wajib diisi</small>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="phone_home" class="form-label">Nomor Telepon Rumah</label>
        <input type="text" class="form-control" id="phone_home" name="phone_home"
            value="{{ old('phone_home', $employeeDetail->no_phone_house ?? '') }}">
    </div>
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="phone_mobile" class="form-label">Nomor Handphone <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="phone_mobile" name="phone_mobile"
            value="{{ old('phone_mobile', $employeeDetail->no_phone ?? '') }}">
        <small class="text-muted">Wajib diisi</small>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="email" class="form-label">Alamat Email (Personal)<span class="text-danger">*</span></label>
        <input type="email" class="form-control" id="email" name="email"
            value="{{ old('email', $user->email ?? '') }}">
        <small class="text-muted">Wajib diisi</small>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-6 col-md-6 col-lg-6 mb-3">
        <label for="address_ktp" class="form-label">Alamat Tinggal (KTP)<span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="address_ktp" name="address_ktp"
            value="{{ old('address_ktp', $employeeDetail->ktp_address ?? '') }}">
        <small class="text-muted">Wajib diisi</small>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 mb-3">
        <label for="address_current" class="form-label">Alamat Tinggal (Saat Ini)<span
                class="text-danger">*</span></label>
        <input type="text" class="form-control" id="address_current" name="address_current"
            value="{{ old('address_current', $employeeDetail->current_address ?? '') }}">
        <small class="text-muted">Wajib diisi</small>
    </div>
</div>

<div class="col mb-3">
    <label for="emergency_contact" class="form-label">Kontak Darurat (Nama dan Nomor Telepon)<span
            class="text-danger">*</span></label>
    <input type="text" class="form-control" id="emergency_contact" name="emergency_contact"
        value="{{ old('emergency_contact', $employeeDetail->emergency_contact ?? '') }}">
    <small class="text-muted">Wajib diisi</small>
</div>

<div class="row mb-3">
    <div class="col-sm-6 col-md-6 col-lg-6 mb-3">
        <label for="tax_status" class="form-label">Status Pajak <span class="text-danger">*</span></label>
        <select name="tax_status" class="form-select" id="tax_status">
            <option value="TK"
                {{ old('tax_status', $employeeDetail->tax_status ?? '') == 'TK' ? 'selected' : '' }}>TK/0 tidak kawin
                dan tidak ada tanggungan.</option>
            <option value="K0"
                {{ old('tax_status', $employeeDetail->tax_status ?? '') == 'K0' ? 'selected' : '' }}>K/0 kawin dan
                tidak ada tanggungan.</option>
            <option value="K1"
                {{ old('tax_status', $employeeDetail->tax_status ?? '') == 'K1' ? 'selected' : '' }}>K/1 kawin dan 1
                tanggungan</option>
            <option value="K2"
                {{ old('tax_status', $employeeDetail->tax_status ?? '') == 'K2' ? 'selected' : '' }}>K/2 kawin dan 2
                tanggungan</option>
            <option value="K3"
                {{ old('tax_status', $employeeDetail->tax_status ?? '') == 'K3' ? 'selected' : '' }}>K/3 kawin dan 3
                tanggungan</option>
            <option value="K10"
                {{ old('tax_status', $employeeDetail->tax_status ?? '') == 'K10' ? 'selected' : '' }}>K/I/0 penghasilan
                suami dan istri digabung dan tidak ada tanggungan</option>
            <option value="K11"
                {{ old('tax_status', $employeeDetail->tax_status ?? '') == 'K11' ? 'selected' : '' }}>K/I/1 penghasilan
                suami dan istri digabung dan 1 tanggungan</option>
            <option value="K12"
                {{ old('tax_status', $employeeDetail->tax_status ?? '') == 'K12' ? 'selected' : '' }}>K/I/2 penghasilan
                suami dan istri digabung dan 2 tanggungan</option>
            <option value="K13"
                {{ old('tax_status', $employeeDetail->tax_status ?? '') == 'K13' ? 'selected' : '' }}>K/I/3 penghasilan
                suami dan istri digabung dan 3 tanggungan</option>

        </select>
        <small class="text-muted">Wajib diisi</small>
        @error('tax_status')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 mb-3">
        <label for="marital_status" class="form-label">Status Pernikahan</label>
        <select name="marital_status" class="form-select" id="marital_status">
            <option value="Belum Menikah"
                {{ old('marital_status', $employeeDetail->marital_status ?? '') == 'Belum Menikah' ? 'selected' : '' }}>
                Belum Menikah</option>
            <option value="Menikah"
                {{ old('marital_status', $employeeDetail->marital_status ?? '') == 'Menikah' ? 'selected' : '' }}>
                Menikah</option>
            <option value="Duda / Janda Dengan Anak"
                {{ old('marital_status', $employeeDetail->marital_status ?? '') == 'Duda / Janda Dengan Anak' ? 'selected' : '' }}>
                Duda / Janda Dengan Anak</option>
            <option value="Duda / Janda Tanpa Anak"
                {{ old('marital_status', $employeeDetail->marital_status ?? '') == 'Duda / Janda Tanpa Anak' ? 'selected' : '' }}>
                Duda / Janda Tanpa Anak</option>
        </select>
        @error('marital_status')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>
</div>


@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('userDetailsForm');

        // Ambil semua input, textarea, dan select di dalam form
        const inputs = form.querySelectorAll('#personal input, #personal select, #personal textarea, #uniform select');

        // Fungsi untuk mengirim AJAX autosave
        const autosave = () => {
            const formData = new FormData(form);

            fetch('/users/autosave-personal/{{ $user->id }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Autosave Personal Data.');
            })
            .catch(error => {
                console.error('Autosave error:', error);
            });
        };

        // Pasang event listener untuk input dan select
        inputs.forEach(input => {
            input.addEventListener('input', autosave);
            input.addEventListener('change', autosave);
        });
    });
</script>

@endpush
