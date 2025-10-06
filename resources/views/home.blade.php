@extends('layouts.admin')
@push('styles')
    <style>
        .card {
            border-radius: 20px;
        }

        a {
            color: #6c757d;
        }

        .step-container-vertical {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .step-vertical {
            position: relative;
            align-items: flex-start;
        }

        .circle-vertical-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .circle-vertical {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .circle-vertical.active {
            background-color: #0d6efd;
            color: white;
        }

        .connector-vertical {
            width: 2px;
            height: 100%;
            background-color: #e9ecef;
            margin: 8px 0;
            flex-grow: 1;
        }

        .step-content-vertical {
            padding-bottom: 1.5rem;
        }

        .label-vertical {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        #signature-pad {
            border: 0.5px solid #000;
            width: 100%;
            max-width: 400px;
            height: 200px;
            touch-action: none;
            background-color: white;
        }
    </style>
@endpush

@section('content')
    {{-- @dd(Auth::user()->getRole()) --}}

    @if ($permissionModal ?? false)
        <div class="modal fade show" id="permissionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" style="display:block;">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel">Persetujuan Data Pribadi</h5>
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                    </div>
                    <div class="modal-body">
                        <a href="{{ route('kp') }}" target="_blank"
                            class="btn btn-outline-primary mb-4 fs-4 ms-2">Document Preview</a>
                        <canvas id="signature-pad"></canvas>
                        <br>
                        <p class="my-4">
                            Dengan ini saya menyatakan bahwa saya telah membaca dan menyetujui isi dokumen di atas,
                            dan bersedia menandatangani secara digital sebagai bukti persetujuan data pribadi saya.
                        </p>
                        <form id="signature-form" method="POST" action="{{ route('permission.signature') }}">
                            @csrf
                            <input type="hidden" name="signature" id="signature-input">
                            <button type="button" class="btn btn-outline-danger me-1" id="clear">Hapus</button>
                            <button class="btn btn-outline-primary" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('permissionModal'), {
                backdrop: 'static', 
                keyboard: false
            });
            myModal.show();
        });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
        <script>
            console.log(window.SignaturePad);
            var canvas = document.getElementById('signature-pad');

            if (canvas) {
                var signaturePad = new SignaturePad(canvas);
            } else {
                console.error("Canvas not found!");
            }

            document.getElementById('clear').addEventListener('click', function() {
                signaturePad.clear();
            });

            document.getElementById('signature-form').addEventListener('submit', function(e) {
                if (!signaturePad.isEmpty()) {
                    var signatureData = signaturePad.toSVG();
                    document.getElementById('signature-input').value = signatureData;
                } else {
                    e.preventDefault();
                    alert("Silakan tanda tangan terlebih dahulu!");
                }
            });
        </script>
    @endif

    <div class="row">
        @if (!in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3', 'admin 4']))
            <div class="col-lg-12 col-md-12 col-sm-12">
                <p class="fs-8 fw-bold">Welcome {{ Auth::user()->fullname }}</p>
                @if (Auth::user()->progressOnboardingEmployee()['progress'] == 100)
                    <div class="alert alert-success fade show" role="alert" style="border-radius: 20px">
                        {{ Auth::user()->progressOnboardingEmployee()['message'] }}
                    </div>
                @else
                    <div class="alert alert-warning fade show" role="alert" style="border-radius: 20px">
                        {{ Auth::user()->progressOnboardingEmployee()['message'] }}
                    </div>
                @endif
            </div>
            <div class="col-lg-7 col-md-12 col-sm-12">
                <div class="card" style="border-radius: 20px">
                    <div class="card-header">
                        <p class="fs-6 fw-bold">Onboarding Progress</p>
                    </div>
                    <div class="card-body">
                        @if (Auth::user()->firstEmployeeJob?->start_date)
                            <p class="fw-bolder mb-0">Your first day is on
                                {{ \Carbon\Carbon::parse(Auth::user()->firstEmployeeJob->start_date)->isoFormat('D MMMM YYYY') }}
                            </p>
                        @else
                            <p class="fw-bolder mb-0">Your first day is on -</p>
                        @endif
                        @if (Auth::user()->progressOnboardingEmployee()['progress'] > 0)
                            <div class="progress mt-4 mb-3">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ Auth::user()->progressOnboardingEmployee()['progress'] }}%;"
                                    aria-valuenow="{{ Auth::user()->progressOnboardingEmployee()['progress'] }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                    {{ Auth::user()->progressOnboardingEmployee()['progress'] }}%
                                </div>
                            </div>
                            <p class="text-muted">
                                <span>Make sure to have the following items completed before.</span>
                            </p>
                        @else
                            <p class="text-muted">{{ __('No onboarding progress data available.') }}</p>
                        @endif
                    </div>
                </div>
                <div class="card" style="border-radius: 20px">
                    <div class="card-header">
                        <p class="fs-6 fw-bold">My Actions</p>
                    </div>
                    <div class="card-body">
                        <div class="step-container-vertical">
                            <!-- Step 1 -->

                            <a href="{{ route('users.details') }}">
                                <div class="step-vertical d-flex">
                                    <div class="circle-vertical-container">
                                        <div class="circle-vertical @if ($personal_status) active @endif"><i
                                                class="ti ti-user fs-4"></i></div>
                                        <div class="connector-vertical"></div>
                                    </div>
                                    <div class="step-content-vertical ms-3">
                                        <div class="label-vertical">Fill Personal Data</div>
                                        @if ($personal_status && !empty($personal_date))
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($personal_date)->format('d M Y') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </a>

                            <!-- Step 3 -->
                            <a href="{{ route('users.index.onboarding') }}">
                                <div class="step-vertical d-flex">
                                    <div class="circle-vertical-container">
                                        <div class="circle-vertical @if ($contract_status) active @endif"><i
                                                class="ti ti-clipboard-text fs-4"></i></div>
                                        <div class="connector-vertical"></div>
                                    </div>
                                    <div class="step-content-vertical ms-3">
                                        <div class="label-vertical">Contract Signature</div>
                                        @if ($contract_status && !empty($contract_date))
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($contract_date)->format('d M Y') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('users.index.onboarding') }}">
                                <div class="step-vertical d-flex">
                                    <div class="circle-vertical-container">
                                        <div class="circle-vertical @if ($spk_status) active @endif"><i
                                                class="ti ti-clipboard-text fs-4"></i></div>
                                        <div class="connector-vertical"></div>
                                    </div>
                                    <div class="step-content-vertical ms-3">
                                        <div class="label-vertical">SPK Signature</div>
                                        @if ($spk_status && !empty($spk_date))
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($spk_date)->format('d M Y') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </a>


                            <!-- Step 4 -->
                            <a href="{{ route('users.index.onboarding') }}">
                                <div class="step-vertical d-flex">
                                    <div class="circle-vertical-container">
                                        <div class="circle-vertical @if ($inventories_status) active @endif"><i
                                                class="ti ti-checklist fs-4"></i></div>
                                        <div class="connector-vertical"></div>
                                    </div>
                                    <div class="step-content-vertical ms-3">
                                        <div class="label-vertical">Starter Kit Checklist</div>
                                        @if ($inventories_status && !empty($inventories_date))
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($inventories_date)->format('d M Y') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </a>

                            <!-- Conditional Step 5 -->
                            @if (optional($user->firstEmployeeJob)->user_dakar_role === 'karyawan')
                                <a href="{{ route('users.index.onboarding') }}">

                                    <div class="step-vertical d-flex">
                                        <div class="circle-vertical-container">
                                            <div class="circle-vertical @if ($inumber_status) active @endif"><i
                                                    class="ti ti-apps fs-4"></i></div>
                                        </div>
                                        <div class="step-content-vertical ms-3">
                                            <div class="label-vertical">Waiting for Digital Account</div>
                                            @if ($inumber_status && !empty($inumber_date))
                                                <div class="text-muted small">
                                                    {{ \Carbon\Carbon::parse($inumber_date)->format('d M Y') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-5 col-sm-12 pe-0 row d-flex justify-content-between">
                <div class="col pe-0">
                    @include('admin.users.dashboardCard')
                    <div class="col">
                        <div class="card" style="border-radius:20px">
                            <div class="card-header">
                                <p class="fs-6 fw-bold">HR Contact</p>
                            </div>
                            <div class="card-body">
                                <p><i class="ti ti-brand-whatsapp fs-4"></i> 087874911618 - ( Sadtu Risdiyati ) </p>
                                <p><i class="ti ti-brand-whatsapp fs-4"></i> 08988573497 - ( Risyad Syaifatul )</p>
                                <p><i class="ti ti-mail fs-4"></i>admin.hr@astra-visteon.com</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-3 col-12 mb-0">
                <div class="card">
                    <div class="card-header">
                        Tipe Karyawan
                    </div>
                    <div class="card-body" style="height: 300px;">
                        <canvas id="jobCategoryChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-9 col-12 mb-0">
                <div class="card">
                    <div class="card-header">
                        Jumlah Karyawan per Department
                    </div>
                    <div class="card-body" style="height: 300px;">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header d-flex align-items-center">
                        <i class="bi bi-people-fill me-2 text-primary"></i>
                        <span class="fw-semibold">{{ __('Jumlah Karyawan AVI') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="text-muted">{{ __('Karyawan AVI') }}</div>
                            <div class="fw-bold fs-5">{{ $karyawan }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header d-flex align-items-center">
                        <i class="bi bi-mortarboard-fill me-2 text-success"></i>
                        <span class="fw-semibold">{{ __('Jumlah Karyawan Pemagangan') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="text-muted">{{ __('Pemagangan') }}</div>
                            <div class="fw-bold fs-5">{{ $pemagangan }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header d-flex align-items-center">
                        <i class="bi bi-laptop-fill me-2 text-warning"></i>
                        <span class="fw-semibold">{{ __('Jumlah Karyawan Intern') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="text-muted">{{ __('Internship') }}</div>
                            <div class="fw-bold fs-5">{{ $internship }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header d-flex align-items-center">
                        <i class="bi bi-person-x-fill me-2 text-danger"></i>
                        <span class="fw-semibold">{{ __('Onboarding') }}</span>
                    </div>
                    <a href="{{ route('users.index.onboarding', ['progressFilter' => true]) }}"
                        class="card-body text-decoration-none text-dark">
                        <div class="d-flex justify-content-between">
                            <div class="text-muted">{{ __('Incomplete Onboarding') }}</div>
                            <div class="fw-bold fs-5">{{ $uncomplete }}</div>
                        </div>
                    </a>
                </div>
            </div>


            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Karyawan Habis Kontrak - {{ now()->addMonths(2)->translatedFormat('F Y') }}
                    </div>
                    <div class="card-body table-responsive">
                        {{-- <a href="{{ route('expiredContract') }}" class="btn btn-outline-primary mb-3">
                            Download Selengkapnya di Excel
                        </a> --}}
                        <table class="table text-nowrap mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NPK</th>
                                    <th>Department</th>
                                    <th>Akhir Kontrak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expiredThisMonth as $job)
                                    <tr>
                                        <td>{{ $job->user->fullname ?? '-' }}</td>
                                        <td>{{ $job->user->npk ?? '-' }}</td>
                                        <td>{{ $job->department->department_name ?? '-' }}</td>
                                        <td>{{ $job->end_date->isoFormat('D MMMM Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Karyawan Ulang Tahun - {{ now()->translatedFormat('F Y') }}
                    </div>
                    <div class="card-body table-responsive">
                        {{-- <a href="{{ route('birthday') }}" class="btn btn-outline-primary mb-3">
                            Download Selengkapnya di Excel
                        </a> --}}
                        <table class="table text-nowrap mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NPK</th>
                                    <th>Department</th>
                                    <th>Birth Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($birthdays as $birthday)
                                    <tr>
                                        <td>{{ $birthday->user->fullname ?? '-' }}</td>
                                        <td>{{ $birthday->user->npk ?? '-' }}</td>
                                        <td>{{ $birthday->user->department->department_name ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($birthday->birth_date)->isoFormat('DD MMMM YYYY') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

             <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Menunggu Tanda Tangan Kontrak
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table text-nowrap mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NPK</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($signatures as $s)
                                    <tr>
                                        <td>{{ $s->employeeJob?->user?->fullname ?? '-' }}</td>
                                        <td>{{ $s->employeeJob?->npk ?? '-' }}</td>
                                        <td>{{ $s->employeeJob?->department?->department_name ?? '-' }}</td>
                                        <td>{{ $s->employeeJob?->contract ?? '-'}}</td></td>
                                        <td>
                                            <a href="{{ route('signature.index', ['id' => $s->employee_job_id, 'type' => 'contract']) }}"
                                                class="btn btn-sm btn-outline-primary"><i
                                                    class="ti ti-clipboard-list"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

             <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Menunggu Tanda Tangan Data Kompensasi
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table text-nowrap mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NPK</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($compensations as $c)
                                    <tr>
                                        <td>{{ $c->user?->fullname ?? '-' }}</td>
                                        <td>{{ $c->npk ?? '-' }}</td>
                                        <td>{{ $c->department?->department_name ?? '-' }}</td>
                                        <td>{{ $c->contract ?? '-'}}</td></td>
                                        <td>
                                            <a href="{{ route('signature.index', ['id' => $c->id, 'type' => 'kompensasi']) }}"
                                                class="btn btn-sm btn-outline-primary"><i
                                                    class="ti ti-clipboard-list"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    @if (in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3', 'admin 4']))
        <script>
            const ctx = document.getElementById('jobCategoryChart').getContext('2d');
            const jobCategoryChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($jobType->keys()) !!},
                    datasets: [{
                        label: 'Jumlah Karyawan',
                        data: {!! json_encode($jobType->values()) !!},
                        backgroundColor: [
                            '#FFAE1F',
                            '#5D87FF',
                            '#49BEFF'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            });
        </script>

        <script>
            const labels = @json(array_keys($departments->toArray()));
            const data = @json(array_values($departments->toArray()));

            const barctx = document.getElementById('barChart').getContext('2d');
            new Chart(barctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Karyawan',
                        data: data,
                        backgroundColor: '#7599FF',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 90,
                                minRotation: 90,
                                font: {
                                    size: 10
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        </script>
    @endif
@endpush
