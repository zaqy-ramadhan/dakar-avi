@php
    $employeeEducation = $employeeEducation ?? collect();
@endphp

<div id="education-container">
    @if ($employeeEducation->count() > 0)
        @php $count = 0; @endphp
        @foreach ($employeeEducation as $index => $education)
            @php $count++; @endphp
            <div class="education-entry" id="education_{{ $count }}">
                <div class="row mb-3">
                    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
                        <label class="form-label">LEVEL PENDIDIKAN<span class="text-danger">*</span></label>
                        <select name="education_level[]" class="form-control">
                            <option value="SD"
                                {{ old("education_level.$index", $education->education_level) == 'SD' ? 'selected' : '' }}>
                                SD</option>
                            <option value="SMP"
                                {{ old("education_level.$index", $education->education_level) == 'SMP' ? 'selected' : '' }}>
                                SMP</option>
                            <option value="SMA"
                                {{ old("education_level.$index", $education->education_level) == 'SMA' ? 'selected' : '' }}>
                                SMA</option>
                            <option value="D3"
                                {{ old("education_level.$index", $education->education_level) == 'D3' ? 'selected' : '' }}>
                                D3</option>
                            <option value="S1"
                                {{ old("education_level.$index", $education->education_level) == 'S1' ? 'selected' : '' }}>
                                S1</option>
                            <option value="S2"
                                {{ old("education_level.$index", $education->education_level) == 'S2' ? 'selected' : '' }}>
                                S2</option>
                            <option value="S3"
                                {{ old("education_level.$index", $education->education_level) == 'S3' ? 'selected' : '' }}>
                                S3</option>
                        </select>
                        <small class="text-muted">Wajib diisi</small>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
                        <label class="form-label">NAMA LEMBAGA PENDIDIKAN<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="education_institution[]" required
                            value="{{ old("education_institution.$index", $education->education_institution) }}">
                        <small class="text-muted">Wajib diisi</small>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
                        <label class="form-label">KOTA/TEMPAT<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="education_city[]" required
                            value="{{ old("education_city.$index", $education->education_city) }}">
                        <small class="text-muted">Wajib diisi</small>
                    </div>
                </div>
                <div class="row mb-3 border-bottom border-black">
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label class="form-label">JURUSAN</label>
                        <input type="text" class="form-control" name="education_major[]"
                            value="{{ old("education_major.$index", $education->education_major) }}">
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label class="form-label">IPK</label>
                        <input type="number" step="0.01" class="form-control" name="education_gpa[]"
                            value="{{ old("education_gpa.$index", $education->education_gpa) }}">
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label class="form-label">TAHUN MASUK<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="education_start_year[]" required
                            value="{{ old("education_start_year.$index", $education->education_start_year) }}">
                        <small class="text-muted">Wajib diisi</small>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label class="form-label">TAHUN LULUS<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="education_end_year[]" required
                            value="{{ old("education_end_year.$index", $education->education_end_year) }}">
                        <small class="text-muted">Wajib diisi</small>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <!-- Jika tidak ada data, tampilkan satu entri kosong -->
        <div class="education-entry " id="education_1">
            <div class="row mb-3">
                <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
                    <label class="form-label">LEVEL PENDIDIKAN<span class="text-danger">*</span></label>
                    <select name="education_level[]" class="form-control">
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMA">SMA</option>
                        <option value="D3">D3</option>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                    </select>
                    <small class="text-muted">Wajib diisi</small>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
                    <label class="form-label">NAMA LEMBAGA PENDIDIKAN<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="education_institution[]">
                    <small class="text-muted">Wajib diisi</small>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
                    <label class="form-label">KOTA/TEMPAT<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="education_city[]">
                    <small class="text-muted">Wajib diisi</small>
                </div>
            </div>
            <div class="row mb-3 border-bottom border-black">
                <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label class="form-label">JURUSAN</label>
                    <input type="text" class="form-control" name="education_major[]">
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label class="form-label">IPK</label>
                    <input type="number" step="0.01" class="form-control" name="education_gpa[]">
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label class="form-label">TAHUN MASUK<span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="education_start_year[]">
                    <small class="text-muted">Wajib diisi</small>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label class="form-label">TAHUN LULUS<span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="education_end_year[]">
                    <small class="text-muted">Wajib diisi</small>
                </div>
            </div>
        </div>
    @endif
</div>
<button type="button" id="add-education" class="btn btn-primary"
    @if ($user->id != Auth::user()->id) hidden @endif>Tambah Pendidikan</button>
<button type="button" id="remove-education" class="btn btn-danger" style="display: none;"
    @if (Auth::user()->id != $user->id) hidden @endif>Hapus Pendidikan</button>

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

        function autosaveEducation() {
            console.log('⏳ Autosaving...');
            const token = '{{ csrf_token() }}';

            let data = {
                _token: token,
                education_level: [],
                education_institution: [],
                education_city: [],
                education_major: [],
                education_gpa: [],
                education_start_year: [],
                education_end_year: [],
            };

            $('.education-entry').each(function() {
                data.education_level.push($(this).find('select[name="education_level[]"]').val());
                data.education_institution.push($(this).find('input[name="education_institution[]"]').val());
                data.education_city.push($(this).find('input[name="education_city[]"]').val());
                data.education_major.push($(this).find('input[name="education_major[]"]').val());
                data.education_gpa.push($(this).find('input[name="education_gpa[]"]').val());
                data.education_start_year.push($(this).find('input[name="education_start_year[]"]').val());
                data.education_end_year.push($(this).find('input[name="education_end_year[]"]').val());
            });

            $.ajax({
                url: "{{ route('autosave.education', $user->id) }}",
                method: 'POST',
                data: data,
                success: function(res) {
                    console.log('✅ Autosave success');
                },
                error: function(xhr) {
                    console.error('❌ Autosave failed');
                }
            });
        }

        $(document).ready(function() {
            const debounced = debounce(autosaveEducation, 1000);

            $(document).on('input change', 'input[name^="education_"], select[name^="education_"]', function() {
                console.log('🟡 Perubahan terdeteksi di:', this.name);
                debounced();
            });
        });
    </script>
@endpush
