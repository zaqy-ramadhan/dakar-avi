@extends('layouts.admin')
@push('styles')
    <style>
        .card{
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
    </style>
@endpush

@section('content')
    <div class="row">
        @if (!in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3', 'admin 4']))
            <p class="fs-8 fw-bold">Welcome {{ Auth::user()->fullname }}</p>
            <div class="col-lg-5 col-md-12 col-sm-12">
                <div class="card" style="border-radius: 20px">
                    <div class="card-header">
                        <p class="fs-6 fw-bold">Onboarding Progress</p>
                    </div>
                    <div class="card-body">
                        <p class="fw-bolder mb-0">Your first day is on
                            {{ Carbon\Carbon::parse(Auth::user()->join_date)->isoFormat('D MMMM YYYY') }}</p>
                        @if (Auth::user()->progressOnboarding() > 0)
                            <div class="progress mt-4 mb-3">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ Auth::user()->progressOnboarding() }}%;"
                                    aria-valuenow="{{ Auth::user()->progressOnboarding() }}" aria-valuemin="0"
                                    aria-valuemax="100">
                                    {{ Auth::user()->progressOnboarding() }}%
                                </div>
                            </div>
                            <p class="text-muted">
                                <span>Make sure to have the following items completed before.</span>
                                {{-- <strong>{{ Auth::user()->progressOnboarding() }}%</strong>. --}}
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
                                        <div class="circle-vertical @if ($employment_status) active @endif"><i
                                                class="ti ti-clipboard-text fs-4"></i></div>
                                        <div class="connector-vertical"></div>
                                    </div>
                                    <div class="step-content-vertical ms-3">
                                        <div class="label-vertical">Document Signature</div>
                                        @if ($employment_status && !empty($employment_date))
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($employment_date)->format('d M Y') }}</div>
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
                                            <div class="label-vertical">Digital Account</div>
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
            <div class="col-md-12 col-lg-7 col-sm-12 row d-flex justify-content-between">
                <div class="col">
                    @include('admin.users.dashboardCard')
                </div>
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
        @else
            <div class="col-md-3 col-12 mb-0">
                <div class="card">
                    <div class="card-header">
                        Tipe Karyawan
                    </div>
                    <div class="card-body">
                        <canvas id="jobCategoryChart" width="400" height="400"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-9 col-12 mb-0">
                <div class="card">
                    <div class="card-header">
                        Jumlah Karyawan per Department
                    </div>
                    <div class="card-body">
                        <canvas id="barChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Karyawan Habis Kontrak - {{ now()->translatedFormat('F Y') }}
                    </div>
                    <div class="card-body table-responsive">
                        <a href="{{ route('expiredContract') }}" class="btn btn-outline-primary mb-3">
                            Download Selengkapnya di Excel
                        </a>
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

            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        {{ __('Karyawan') }}
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between my-1">
                            <div class="text-muted">{{ __('Karyawan AVI') }}</div>
                            <div class="fw-bold">{{ $karyawan->count() }}</div>
                        </div>
                        <div class="d-flex justify-content-between my-1">
                            <div class="text-muted">{{ __('Pemagangan') }}</div>
                            <div class="fw-bold">{{ $pemagangan->count() }}</div>
                        </div>
                        <div class="d-flex justify-content-between my-1">
                            <div class="text-muted">{{ __('Internship') }}</div>
                            <div class="fw-bold">{{ $internship->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        {{ __('Onboarding') }}
                    </div>
                    <a href="{{ route('users.index.onboarding', ['progressFilter' => true]) }}" class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="text-muted">{{ __('Incomplete Onboarding') }}</div>
                            <div class="fw-bold">{{ $uncomplete->count() }}</div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Karyawan Pembaruan Seragam - {{ now()->translatedFormat('F Y') }}
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table text-nowrap mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NPK</th>
                                    <th>Department</th>
                                    <th>Inventaris</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($uniformRefresh as $inventory)
                                    <tr>
                                        <td>{{ $inventory['name'] ?? '-' }}</td>
                                        <td>{{ $inventory['npk'] ?? '-' }}</td>
                                        <td>{{ $inventory['department'] ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('users.index.onboarding.detail', $inventory['id']) }}"
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
                        Karyawan Ulang Tahun - {{ now()->translatedFormat('F Y') }}
                    </div>
                    <div class="card-body table-responsive">
                        <a href="{{ route('birthday') }}" class="btn btn-outline-primary mb-3">
                            Download Selengkapnya di Excel
                        </a>
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
