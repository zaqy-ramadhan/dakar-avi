@push('styles')
    <style>
        .employee-card {
            max-width: 500px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #e9ecef;
            border: 4px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin: -70px auto 1rem;
            position: relative;
            z-index: 2;
        }

        .profile-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-circle .initials {
            font-size: 2rem;
            font-weight: bold;
            color: #6c757d;
        }

        .info-label {
            font-weight: 500;
            color: #6c757d;
        }

        .info-value {
            font-weight: 400;
            margin-bottom: 0.5rem;
        }

        .status-badge {
            font-size: 0.85rem;
            padding: 4px 10px;
        }

        .divider {
            border-top: 1px solid #dee2e6;
            margin: 1.5rem 0;
        }

        .section-title {
            margin-bottom: 1rem;
            font-weight: 600;
        }
    </style>
@endpush

<div class="employee-card card">
    <div class="card-header text-center p-4">
        {{-- <h4>Employee Information</h4> --}}
        <p class="fs-6 fw-bold">Employee Information</p>
    </div>

    <div class="card-body pt-5 px-4">
        <div class="profile-circle">
            {{-- <div class="initials">LW</div> --}}
            {{-- <img src="profile-photo.jpg" alt="Employee Photo"> --}}
            @php
                $pasFoto = optional($user)->employeeDocs()->where('doc_type', 'Pas Foto')->first();
                $imgPath = $pasFoto
                    ? asset('storage/' . $pasFoto->doc_path)
                    : asset('assets/images/profile/person.png');
            @endphp
            <img src="{{ $imgPath }}" alt="image"
                style="border-radius: 10px; height:150px; min-height: 125px; width:150px; min-width: 125px; object-fit: cover;"
                class="img-fluid">
        </div>

        <!-- Basic Information -->
        <div class="text-center mb-4">
            <h5 class="mb-1">{{ optional($user)->fullname }}</h5>
            <p class="text-muted mb-1">{{ optional($user->latestEmployeeJob)->department->department_name ?? ' - ' }} DEPARTMENT</p>
            <p>
                <a href="{{ optional($user)->employeeSocmed->where('type', 'instagram')->first()->account ?? '' }}" target="_blank"><i class="ti ti-brand-instagram fs-6"></i></a>
                <a href="{{ optional($user)->employeeSocmed->where('type', 'facebook')->first()->account ?? '' }}" target="_blank"><i class="ti ti-brand-facebook fs-6"></i></a>
                <a href="{{ optional($user)->employeeSocmed->where('type', 'linkedin')->first()->account ?? '' }}" target="_blank"><i class="ti ti-brand-linkedin fs-6"></i></a>
                <a href="mailto:{{ optional($user)->email }}">
                    <i class="ti ti-mail fs-6"></i>
                </a>
            </p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="info-label">NPK:</div>
                <div class="info-value">{{ optional($user)->npk }}</div>


                {{-- <div class="info-label">Status:</div>
                @if (optional($user)->employeeJob?->last()->employment_status == true)
                    <span class="badge rounded-pill text-bg-success">Aktif</span>
                @elseif (optional($user)->employeeJob?->last()->employment_status == false)
                    <span class="badge rounded-pill text-bg-danger">Termination</span>
                @endif --}}
                {{-- <span class="badge bg-success status-badge"></span> --}}
            </div>
            <div class="col-md-6">
                <div class="info-label">Status:</div>
                @if ($user->latestEmployeeJob === null)
                    <span class="badge rounded-pill text-bg-dark">N/A</span>
                @elseif (optional($user->employeeJob)->last()?->employment_status == true)
                    <span class="badge rounded-pill text-bg-success">Aktif</span>
                @elseif (optional($user->latestEmployeeJob)->employment_status == false)
                    <span class="badge rounded-pill text-bg-danger">Termination</span>
                @endif
                {{-- <div class="info-label">Social Media:</div>
                <div class="info-value">
                    <span class="d-block"><i class="ti ti-brand-instagram"></i> @lindawijaya</span>
                    <span class="d-block"><i class="ti ti-mail"></i> linda.w@company.com</span>
                </div> --}}
            </div>
        </div>

        <div class="divider"></div>

        <!-- Hire Information -->
        <h5 class="section-title">Hire Information</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="info-label">Job Type:</div>
                <div class="info-value">{{ Str::ucfirst(optional($user)->getRole()) }}</div>
            </div>
            <div class="col-md-6">
                <div class="info-label">Job Status:</div>
                <span
                    class="badge rounded-pill bg-info status-badge">{{ Str::ucfirst(optional($user->latestEmployeeJob)?->job_status) }}</span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <div class="info-label">Join Date:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse(optional($user)->join_date)->isoFormat('D MMMM YYYY') }}</div>
            </div>
            <div class="col-md-6">
                <div class="info-label">Length of Service:</div>
                <div class="info-value">{{ optional($user)->LOS() }}</div>
            </div>
        </div>
    </div>

    <div class="card-footer bg-light text-center py-3">
        <small
            class="text-muted">{{ 'Last updated: ' . \Carbon\Carbon::parse(optional($user->latestEmployeeJob)?->created_at)->isoFormat('D MMMM YYYY') }}</small>
    </div>
</div>
