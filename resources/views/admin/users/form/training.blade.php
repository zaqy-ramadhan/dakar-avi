@php
    $employeeTraining = $employeeTraining ?? collect();
@endphp

<div id="training-container">
    @if ($employeeTraining->count() > 0)
        @foreach ($employeeTraining as $index => $training)
            <div class="training-entry" id="training_{{ $index + 1 }}">
                <div class="row mb-3 border-bottom border-black">
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="training_institution_{{ $index }}" class="form-label">NAMA LEMBAGA
                            TRAINING</label>
                        <input type="text" class="form-control" id="training_institution_{{ $index }}"
                            name="training_institution[]"
                            value="{{ old("training_institution.$index", $training->training_institution) }}">
                        @error("training_institution.$index")
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="training_year_{{ $index }}" class="form-label">TAHUN</label>
                        <input type="number" class="form-control" id="training_year_{{ $index }}"
                            name="training_year[]" value="{{ old("training_year.$index", $training->training_year) }}">
                        @error("training_year.$index")
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="training_duration_{{ $index }}" class="form-label">DURASI</label>
                        <input type="text" class="form-control" id="training_duration_{{ $index }}"
                            name="training_duration[]"
                            value="{{ old("training_duration.$index", $training->training_duration) }}">
                        @error("training_duration.$index")
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="training_certificate_{{ $index }}" class="form-label">NAMA IJAZAH /
                            CERTIFICATE</label>
                        <input type="text" class="form-control" id="training_certificate_{{ $index }}"
                            name="training_certificate[]"
                            value="{{ old("training_certificate.$index", $training->training_certificate) }}">
                        @error("training_certificate.$index")
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="training-entry" id="training_1">
            <div class="row mb-3 border-bottom border-black">
                <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label for="training_institution_1" class="form-label">NAMA LEMBAGA TRAINING</label>
                    <input type="text" class="form-control" id="training_institution_1" name="training_institution[]"
                        value="">
                    @error('training_institution.0')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label for="training_year_1" class="form-label">TAHUN</label>
                    <input type="number" class="form-control" id="training_year_1" name="training_year[]"
                        value="">
                    @error('training_year.0')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label for="training_duration_1" class="form-label">DURASI</label>
                    <input type="text" class="form-control" id="training_duration_1" name="training_duration[]"
                        value="">
                    @error('training_duration.0')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label for="training_certificate_1" class="form-label">NAMA IJAZAH / CERTIFICATE</label>
                    <input type="text" class="form-control" id="training_certificate_1" name="training_certificate[]"
                        value="">
                    @error('training_certificate.0')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    @endif
</div>
<button type="button" id="remove-training" class="btn btn-danger mb-3 me-2"
    @if (Auth::user()->id != $user->id) hidden @endif style="display: none;">Hapus Data
    Training</button>
<button type="button" id="add-training" class="btn btn-primary mb-3" @if ($user->id != Auth::user()->id) hidden @endif>
    Tambah Data Training
</button>

@push('scripts')
    <script>
        function debounce(func, delay) {
            let timer;
            return function() {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, arguments), delay);
            };
        }

        function autosaveTraining() {
            console.log('⏳ Autosaving training...');
            const token = '{{ csrf_token() }}';

            let data = {
                _token: token,
                training_institution: [],
                training_year: [],
                training_duration: [],
                training_certificate: [],
            };

            $('.training-entry').each(function() {
                data.training_institution.push($(this).find('input[name="training_institution[]"]').val());
                data.training_year.push($(this).find('input[name="training_year[]"]').val());
                data.training_duration.push($(this).find('input[name="training_duration[]"]').val());
                data.training_certificate.push($(this).find('input[name="training_certificate[]"]').val());
            });

            $.ajax({
                url: "{{ route('autosave.training', $user->id) }}",
                method: 'POST',
                data: data,
                success: function(res) {
                    console.log('✅ Training autosave success');
                },
                error: function(xhr) {
                    console.error('❌ Training autosave failed');
                }
            });
        }

        $(document).ready(function() {
            const debouncedTraining = debounce(autosaveTraining, 1000);

            $(document).on('input change', 'input[name^="training_"]', function() {
                console.log('🟡 Training field changed:', this.name);
                debouncedTraining();
            });
        });
    </script>
@endpush
