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

                        <div class="row mb-2">
                            <div class="col-12 col-sm-3 fw-bold">
                                <button type="button" class="btn btn-sm btn-light-secondary" data-bs-toggle="modal" data-bs-target="#modalActivityLogs">
                                    <i class="ti ti-history"></i> Activity Logs
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalActivityLogs" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Activity Logs: {{ $user->fullname }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th>Date</th>
                                <th>Actor</th>
                                <th>Employee</th>
                                <th>Note</th>
                                <th>Table</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $activityLogs = $user->activityLogs();
                                // dd($activityLogs);
                            @endphp
                            @forelse($activityLogs as $log)
                                <tr>
                                    <td>{{ $log['created_at'] }}</td>
                                    <td>{{ $log['actor'] ?? 'Admin' }}</td>
                                    <td>{{ $log['employee'] ?? 'Employee' }}</td>
                                    <td>{{ $log['note'] ?? '-' }}</td>
                                    <td><span class="badge rounded-pill text-bg-success">{{ $log['table_name'] }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No activity logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>