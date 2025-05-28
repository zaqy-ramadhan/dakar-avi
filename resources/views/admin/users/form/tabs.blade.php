<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal"
            type="button" role="tab" aria-controls="personal" aria-selected="true">Data Pribadi</button>
    </li>
    @if (Auth::user()->getRole() != 'internship')
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="family-tab" data-bs-toggle="tab" data-bs-target="#family" type="button"
            role="tab" aria-controls="family" aria-selected="false">Data Keluarga</button>
    </li>
    @endif
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="socmed-tab" data-bs-toggle="tab" data-bs-target="#socmed"
            type="button" role="tab" aria-controls="socmed" aria-selected="false">Media Sosial</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="uniform-tab" data-bs-toggle="tab" data-bs-target="#uniform"
            type="button" role="tab" aria-controls="uniform" aria-selected="false">Ukuran
            Seragam</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="education-tab" data-bs-toggle="tab" data-bs-target="#education"
            type="button" role="tab" aria-controls="education" aria-selected="false">Data
            Pendidikan</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="training-tab" data-bs-toggle="tab" data-bs-target="#training"
            type="button" role="tab" aria-controls="training" aria-selected="false">Data Training</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank" type="button"
            role="tab" aria-controls="bank" aria-selected="false">Data Rekening</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents"
            type="button" role="tab" aria-controls="documents" aria-selected="false">Dokumen</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="inventaris-tab" data-bs-toggle="tab" data-bs-target="#inventaris"
            type="button" role="tab" aria-controls="inventaris" aria-selected="false">Kepesertaan & Akun Digital</button>
    </li>

</ul>