@php
    $employeeDoc = $employeeDoc ?? collect();
    $diploma = $employeeDoc->where('doc_type', 'Ijazah dan Transkrip')->first();
    $ktp = $employeeDoc->where('doc_type', 'KTP')->first();
    $npwp = $employeeDoc->where('doc_type', 'NPWP')->first();
    $sim = $employeeDoc->where('doc_type', 'SIM')->first();
    $familyCard = $employeeDoc->where('doc_type', 'Kartu Keluarga')->first();
    $resume = $employeeDoc->where('doc_type', 'Resume')->first();
    $childBirthCert = $employeeDoc->where('doc_type', 'Akte Kelahiran Anak')->first();
    $marriageCert = $employeeDoc->where('doc_type', 'Buku Nikah')->first();
    $photo = $employeeDoc->where('doc_type', 'Pas Foto')->first();
    $bank = $employeeDoc->where('doc_type', 'Buku Rekening')->first();

@endphp

<div class="row mb-3">
    <div class="col-sm-3">
        {{-- <label for="permission" class="form-label">Dokumen</label><br> --}}
        <a href="{{ route('user.data-permission-pdf', $user->id) }}" target="_blank"
            class="btn btn-outline-primary">Persetujuan Data Pribadi</a>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-6 col-md-6 col-lg-6 mb-3">
        <label for="diploma_file" class="form-label">Ijazah dan Transkrip Nilai<span class="text-danger">*</span></label>
        <input type="file" class="form-control" id="diploma_file" name="diploma_file">
        <small class="text-muted">Wajib diisi</small>
        @if ($diploma)
            <p>File yang ada: <a href="{{ asset('storage/' . $diploma->doc_path) }}" target="_blank">Lihat File</a></p>
        @endif
        @error('diploma_file')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 mb-3">
        <label for="ktp_file" class="form-label">KTP<span class="text-danger">*</span></label>
        <input type="file" class="form-control" id="ktp_file" name="ktp_file">
        <small class="text-muted">Wajib diisi</small>
        @if ($ktp)
            <p>File yang ada: <a href="{{ asset('storage/' . $ktp->doc_path) }}" target="_blank">Lihat File</a></p>
        @endif
        @error('ktp_file')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-6 col-md-6 col-lg-6 mb-3">
        <label for="npwp_file" class="form-label">NPWP</label>
        <input type="file" class="form-control" id="npwp_file" name="npwp_file">
        @if ($npwp)
            <p>File yang ada: <a href="{{ asset('storage/' . $npwp->doc_path) }}" target="_blank">Lihat File</a></p>
        @endif
        @error('npwp_file')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 mb-3">
        <label for="sim_file" class="form-label">SIM A/C</label>
        <input type="file" class="form-control" id="sim_file" name="sim_file">
        @if ($sim)
            <p>File yang ada: <a href="{{ asset('storage/' . $sim->doc_path) }}" target="_blank">Lihat File</a></p>
        @endif
        @error('sim_file')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-4 col-md-4 col-lg-4 mb-3">
        <label for="family_card_file" class="form-label">Kartu Keluarga<span class="text-danger">*</span></label>
        <input type="file" class="form-control" id="family_card_file" name="family_card_file">
        <small class="text-muted">Wajib diisi</small>
        @if ($familyCard)
            <p>File yang ada: <a href="{{ asset('storage/' . $familyCard->doc_path) }}" target="_blank">Lihat File</a>
            </p>
        @endif
        @error('family_card_file')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-4 col-md-4 col-lg-4 mb-3">
        <label for="resume_file" class="form-label">CV / Resume Anda<span class="text-danger">*</span></label>
        <input type="file" class="form-control" id="resume_file" name="resume_file">
        <small class="text-muted">Wajib diisi</small>
        @if ($resume)
            <p>File yang ada: <a href="{{ asset('storage/' . $resume->doc_path) }}" target="_blank">Lihat File</a></p>
        @endif
        @error('resume_file')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-4 col-md-4 col-lg-4 mb-3">
        <label for="child_birth_certificate_file" class="form-label">Akte Kelahiran Anak Anda (jika ada)</label>
        <input type="file" class="form-control" id="child_birth_certificate_file"
            name="child_birth_certificate_file">
        @if ($childBirthCert)
            <p>File yang ada: <a href="{{ asset('storage/' . $childBirthCert->doc_path) }}" target="_blank">Lihat
                    File</a></p>
        @endif
        @error('child_birth_certificate_file')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="marriage_certificate_file" class="form-label">Buku/ Surat Nikah (kalau ada)</label>
        <input type="file" class="form-control" id="marriage_certificate_file" name="marriage_certificate_file">
        @if ($marriageCert)
            <p>File yang ada: <a href="{{ asset('storage/' . $marriageCert->doc_path) }}" target="_blank">Lihat
                    File</a></p>
        @endif
        @error('marriage_certificate_file')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="photo_file" class="form-label">Foto Rekening Bank <span class="text-danger">*</span></label>
        <input type="file" class="form-control" id="bank_file" name="bank_file">
        <small class="text-muted">Wajib diisi</small>
        @if ($bank)
            <p>File yang ada: <a href="{{ asset('storage/' . $bank->doc_path) }}" target="_blank">Lihat File</a></p>
        @endif
        @error('bank_file')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="photo_file" class="form-label">Pas Foto dalam bentuk .jpeg <span
                class="text-danger">*</span></label>
        <input type="file" class="form-control" id="photo_file" name="photo_file" accept=".jpeg">
        <small class="text-muted">Wajib diisi</small>
        <p id="statusValidasi" style="color: blue; font-weight: bold;"></p>
        @if ($photo)
            <p>File yang ada: <a href="{{ asset('storage/' . $photo->doc_path) }}" target="_blank">Lihat File</a></p>
        @endif
        @error('photo_file')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        function autosaveDocument() {
            const formData = new FormData(document.getElementById('userDetailsForm'));
            $.ajax({
                url: "{{ route('autosave.document', $user->id) }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Document autosaved.');
                },
                error: function(xhr) {
                    console.error('Gagal autosave dokumen');
                }
            });
        }

        // Optional debounce function to avoid excessive AJAX
        function debounce(func, delay) {
            let timer;
            return function() {
                const context = this,
                    args = arguments;
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(context, args), delay);
            };
        }

        // Attach the autosave function to the change event of file inputs
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', debounce(autosaveDocument, 1000));
        });

        let isModelLoaded = false;
        let isFaceValid = true; // Default true if no new photo selected yet

        function getSubmitButton() {
            return document.getElementById('submitBtn') || document.getElementById('btnSubmit') || document.querySelector(
                'button[type="submit"]');
        }

        async function loadFaceModel() {
            if (!isModelLoaded) {
                await faceapi.nets.tinyFaceDetector.loadFromUri(
                    'https://justadudewhohacks.github.io/face-api.js/models');
                isModelLoaded = true;
            }
        }

        document.getElementById('photo_file').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            const statusText = document.getElementById('statusValidasi');
            const btnSubmit = getSubmitButton();

            if (!file) {
                isFaceValid = true;
                if (btnSubmit) btnSubmit.disabled = false;
                statusText.innerText = "";
                return;
            }

            statusText.innerText = "Sedang memindai foto... mohon tunggu...";
            statusText.style.color = "blue";
            if (btnSubmit) btnSubmit.disabled = true;
            isFaceValid = false;

            try {
                // 1. Ambil model deteksi AI ringan
                await loadFaceModel();

                // 2. Ubah file upload menjadi objek gambar yang bisa dibaca AI
                const image = await faceapi.bufferToImage(file);

                // 3. Cari wajah pada gambar
                const detections = await faceapi.detectAllFaces(image, new faceapi.TinyFaceDetectorOptions({
                    inputSize: 320,
                    scoreThreshold: 0.4
                }));

                // 4. Cek hasil deteksi
                if (detections.length > 0) {
                    statusText.innerText = "✓ Foto Valid! (Wajah terdeteksi)";
                    statusText.style.color = "green";
                    isFaceValid = true;
                    if (btnSubmit) btnSubmit.disabled = false; // Aktifkan tombol submit
                } else {
                    statusText.innerText =
                        "✗ Gagal! Wajah tidak terdeteksi pada foto ini. Silakan pilih foto dengan wajah yang jelas.";
                    statusText.style.color = "red";
                    isFaceValid = false;
                    e.target.value = ""; // Kosongkan file input yang tidak valid
                    if (btnSubmit) btnSubmit.disabled = true; // Kunci tombol submit
                    alert("Wajah tidak terdeteksi pada Pas Foto! File dibatalkan.");
                }
            } catch (error) {
                console.error("Face detection error:", error);
                statusText.innerText = "⚠️ Gagal memuat deteksi wajah (Pastikan koneksi internet stabil).";
                statusText.style.color = "orange";
                isFaceValid = true; // Izinkan fallback jika library CDN offline
                if (btnSubmit) btnSubmit.disabled = false;
            }
        });

        // Cegah submit form jika foto dipindai dan tidak valid
        const formElement = document.getElementById('userDetailsForm') || document.querySelector('form');
        if (formElement) {
            formElement.addEventListener('submit', function(e) {
                if (!isFaceValid) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert("Form tidak dapat dikirim karena Pas Foto tidak memiliki wajah yang valid!");
                    const btnSubmit = getSubmitButton();
                    if (btnSubmit) btnSubmit.disabled = true;
                    return false;
                }
            }, true);
        }
    </script>
@endpush
