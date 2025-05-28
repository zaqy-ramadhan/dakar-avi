<div class="card" style="border-radius: 20px">
    <div class="card-body my-3 py-3">
        <div class="d-flex flex-wrap flex-sm-nowrap">
            <!-- Bagian Foto Profil -->
            <div class="p-3 align-middle text-center">
                <div class="symbol symbol-125px symbol-lg-125px symbol-fixed position-relative">
                    @php
                        $pasFoto = $user->employeeDocs()->where('doc_type', 'Pas Foto')->first();
                        $imgPath = $pasFoto
                            ? asset('storage/' . $pasFoto->doc_path)
                            : asset('assets/images/profile/person.png');
                    @endphp
                    <img src="{{ $imgPath }}" alt="image"
                        style="border-radius: 10px; height: 150px; width: 120px; object-fit: cover;" class="img-fluid">
                </div>
            </div>

            <!-- Bagian Informasi Pengguna -->
            <div class="flex-grow-1 ms-4 ms-sm-5">
                <div class="d-flex align-items-center mb-2">
                    {{-- <span class="text-gray-900 fs-2 fw-bold me-3">{{ $user->fullname }}</span> --}}
                </div>
                <div class="row text-gray-700">
                    <div class="col">
                        <!-- Nama -->
                        <div class="row mb-2">
                            <div class="col-12 col-sm-3 fw-bold">Name</div>
                            <div class="col-12 col-sm-9">: {{ $user->fullname }}</div>
                        </div>
                        <!-- NPK -->
                        <div class="row mb-2">
                            <div class="col-12 col-sm-3 fw-bold">NPK</div>
                            <div class="col-12 col-sm-9">: {{ $user->npk }}</div>
                        </div>
                        <!-- Department -->
                        <div class="row mb-2">
                            <div class="col-12 col-sm-3 fw-bold">Department</div>
                            <div class="col-12 col-sm-9">:
                                {{ $user->employeeJob?->last()->department->department_name ?? 'No Department' }}
                            </div>
                        </div>
                        <!-- Tipe Karyawan -->
                        <div class="row mb-2">
                            <div class="col-12 col-sm-3 fw-bold">Tipe Karyawan</div>
                            <div class="col-12 col-sm-9">: <span
                                    class="badge rounded-pill text-bg-warning">{{ Str::ucfirst($user->getRole()) }}</span>
                            </div>
                        </div>
                        <!-- Length of Service -->
                        <div class="row mb-2">
                            <div class="col-12 col-sm-3 fw-bold">Length Of Service</div>
                            <div class="col-12 col-sm-9">: {{ $user->LOS() }}</div>
                        </div>
                        <!-- Status -->
                        <div class="row mb-2">
                            <div class="col-12 col-sm-3 fw-bold">Status</div>
                            <div class="col-12 col-sm-9">:
                                {{ Str::ucfirst($user->employeeJob?->last()->job_status ?? 'N/A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>