<!doctype html>
<html lang="en-GB">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>A - WORK</title>
    <link rel="stylesheet" href="/assets/css/styles.min.css" />
    <link rel="stylesheet" href="/assets/css/datatables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.0/css/dataTables.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .body-wrapper .container-fluid {
            max-width: 100%;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-center">
                    <a href="/" class="text-nowrap logo-img">
                        <img src="/assets/images/logos/awork-logo.png"
                            style="height: 50px; width: auto; object-fit:cover; margin-top: 16px;" alt="" />
                    </a>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x fs-8"></i>
                    </div>
                </div>
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                    <ul id="sidebarnav">
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Home</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/" aria-expanded="false">
                                <span>
                                    <i class="ti ti-layout-dashboard"></i>
                                </span>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('password') }}" aria-expanded="false">
                                <span>
                                    <i class="ti ti-lock"></i>
                                </span>
                                <span class="hide-menu">Change Password</span>
                            </a>
                        </li>
                        <ul id="sidebarnav">

                            <li class="nav-small-cap">
                                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                                <span class="hide-menu">Employee Data</span>
                            </li>

                            @if (in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3', 'admin 4']))
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                        <span class="sidebar-icon">
                                            <i class="ti ti-users"></i>
                                        </span>
                                        <span class="hide-menu">Employee</span>
                                        <i class="ti ti-chevron-down toggle-arrow"></i>
                                    </a>
                                    <ul class="collapse first-level">
                                        <li class="sidebar-item">
                                            <a class="sidebar-link {{ Request::is('*karyawan*') ? 'active' : '' }}"
                                                href="/admin/users/view/karyawan">
                                                <span><i class="ti ti-point"></i></span>
                                                <span class="hide-menu">Karyawan AVI</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link {{ Request::is('*pemagangan*') ? 'active' : '' }}"
                                                href="/admin/users/view/pemagangan">
                                                <span><i class="ti ti-point"></i></span>
                                                <span class="hide-menu">Pemagangan</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link {{ Request::is('*internship*') ? 'active' : '' }}"
                                                href="/admin/users/view/internship">
                                                <span><i class="ti ti-point"></i></span>
                                                <span class="hide-menu">Internship</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            @if (!in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3', 'admin 4']))
                                <li class="sidebar-item">
                                    <a class="sidebar-link {{ Request::is('*form*') ? 'active' : '' }}"
                                        href="{{ route('users.details') }}" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-file"></i>
                                        </span>
                                        <span class="hide-menu">Data Diri</span>
                                    </a>
                                </li>
                            @endif
                        </ul>

                        @if (Auth::user()->getRole() != 'admin 4')

                            <ul id="sidebarnav">
                                <li class="nav-small-cap">
                                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                                    <span class="hide-menu">User Career</span>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link {{ Request::is('*onboarding*') ? 'active' : '' }}"
                                        href="/admin/onboarding" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-briefcase"></i>
                                        </span>
                                        <span class="hide-menu">Onboarding</span>
                                    </a>
                                </li>

                                @if (!in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']))
                                    <li class="sidebar-item">
                                        <a class="sidebar-link {{ Request::is('*employment*') ? 'active' : '' }}"
                                            href="{{ route('users.index.employment') }}" aria-expanded="false">
                                            <span>
                                                <i class="ti ti-script"></i>
                                            </span>
                                            <span class="hide-menu">Employment</span>
                                        </a>
                                    </li>
                                @endif

                                <li class="sidebar-item">
                                    <a class="sidebar-link {{ Request::is('*offboarding*') ? 'active' : '' }}"
                                        href="/admin/offboarding" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-briefcase-off"></i>
                                        </span>
                                        <span class="hide-menu">Offboarding</span>
                                    </a>
                                </li>
                            </ul>

                        @endif

                        @if (in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']))
                            <ul id="sidebarnav">
                                <li class="nav-small-cap">
                                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                                    <span class="hide-menu">Reporting</span>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link {{ Request::is('*reporting/employee') ? 'active' : '' }}"
                                        href="/admin/reporting/employee" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-user-circle"></i>
                                        </span>
                                        <span class="hide-menu">Employee Data</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link {{ Request::is('*expired-contract*') ? 'active' : '' }}"
                                        href="/admin/reporting/expired-contract" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-calendar-off"></i>
                                        </span>
                                        <span class="hide-menu">Expired Contract</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link {{ Request::is('*joined-employee*') ? 'active' : '' }}"
                                        href="/admin/reporting/joined-employee" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-calendar-plus"></i>
                                        </span>
                                        <span class="hide-menu">Joined Employee</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link {{ Request::is('*employee-birthday*') ? 'active' : '' }}"
                                        href="/admin/reporting/employee-birthday" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-cake"></i>
                                        </span>
                                        <span class="hide-menu">Employee Birthday</span>
                                    </a>
                                </li>
                            </ul>
                            <ul id="sidebarnav">
                                <li class="nav-small-cap">
                                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                                    <span class="hide-menu">Master Data</span>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow" href="javascript:void(0)"
                                        aria-expanded="false">
                                        <span class="sidebar-icon">
                                            <i class="ti ti-server-cog"></i>
                                        </span>
                                        <span class="hide-menu">Master Data</span>
                                        <i class="ti ti-chevron-down toggle-arrow"></i>
                                    </a>
                                    <ul class="collapse first-level">
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/divisions" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-sitemap"></i>
                                                </span>
                                                <span class="hide-menu">Division</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/departments" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-building"></i>
                                                </span>
                                                <span class="hide-menu">Department</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/sections" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-section"></i>
                                                </span>
                                                <span class="hide-menu">Section</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/positions" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-location"></i>
                                                </span>
                                                <span class="hide-menu">Position</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/level" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-stairs-up"></i>
                                                </span>
                                                <span class="hide-menu">Level</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/job_status" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-checklist"></i>
                                                </span>
                                                <span class="hide-menu">Job Status</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/cost_center" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-cash"></i>
                                                </span>
                                                <span class="hide-menu">Cost Center</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/golongan" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-users"></i>
                                                </span>
                                                <span class="hide-menu">Golongan</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/sub_golongan" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-users"></i>
                                                </span>
                                                <span class="hide-menu">Sub Golongan</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/line" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-line"></i>
                                                </span>
                                                <span class="hide-menu">Line</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/group" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-layers-linked"></i>
                                                </span>
                                                <span class="hide-menu">Group</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/job_type" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-briefcase"></i>
                                                </span>
                                                <span class="hide-menu">Job Type</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/work_hour" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-hourglass"></i>
                                                </span>
                                                <span class="hide-menu">Work Hour</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/item" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-clipboard-list"></i>
                                                </span>
                                                <span class="hide-menu">Items</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/inventory-rules"
                                                aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-building-warehouse"></i>
                                                </span>
                                                <span class="hide-menu">Inventaris Rule</span>
                                            </a>
                                        </li>
                                        {{-- <li class="sidebar-item">
                                            <a class="sidebar-link" href="/admin/disnaker" aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-chisel"></i>
                                                </span>
                                                <span class="hide-menu">Kepala Dinas Tenaga Kerja</span>
                                            </a>
                                        </li> --}}
                                    </ul>
                                </li>
                            </ul>
                        @endif

                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper" style="background-color:rgb(248, 248, 248)">
            <!--  Header Start -->
            {{-- @dd(Auth::user()->adminNotif()) --}}
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse"
                                href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-icon-hover" href="#" id="notificationDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-bell-ringing"></i>
                                    <div class="notification bg-primary rounded-circle"></div>
                                </a>
                                @if (in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3', 'admin 4']))
                                    <div class="dropdown-menu dropdown-menu-end p-0"
                                        aria-labelledby="notificationDropdown"
                                        style="min-width: 350px; right: 0; left: auto;"
                                        onclick="event.stopPropagation();">
                                        <div class="accordion" id="notificationAccordion">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="notifHeadingOne">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#notifCollapseOne"
                                                        aria-expanded="true" aria-controls="notifCollapseOne">
                                                        Completed Personal Data
                                                    </button>
                                                </h2>
                                                <div id="notifCollapseOne" class="accordion-collapse collapse show"
                                                    aria-labelledby="notifHeadingOne"
                                                    data-bs-parent="#notificationAccordion">
                                                    <div class="accordion-body p-0">
                                                        <ul class="list-group">
                                                            @foreach (Auth::user()->adminNotif()['personal_completed'] as $personal)
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <a href="{{ route('users.index.onboarding.detail', $personal->id) }}"
                                                                            class="text-decoration-none">
                                                                            {{ $personal->fullname }}
                                                                        </a>
                                                                        <div class="text-muted"
                                                                            style="font-size: 0.85em;">
                                                                            NPK : {{ $personal->npk ?? '-' }}
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="notifHeadingTwo">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#notifCollapseTwo"
                                                        aria-expanded="false" aria-controls="notifCollapseTwo">
                                                        Document Signed
                                                    </button>
                                                </h2>
                                                <div id="notifCollapseTwo" class="accordion-collapse collapse"
                                                    aria-labelledby="notifHeadingTwo"
                                                    data-bs-parent="#notificationAccordion">
                                                    <div class="accordion-body p-0">
                                                        <ul class="list-group">
                                                            @foreach (Auth::user()->adminNotif()['employment_completed'] as $personal)
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <a href="{{ route('users.index.onboarding.detail', $personal->id) }}"
                                                                            class="text-decoration-none">
                                                                            {{ $personal->fullname }}
                                                                        </a>
                                                                        <div class="text-muted"
                                                                            style="font-size: 0.85em;">
                                                                            NPK : {{ $personal->npk ?? '-' }}
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="notifHeading3">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#notifCollapse3"
                                                        aria-expanded="false" aria-controls="notifCollapse3">
                                                        Starter Kit Given
                                                    </button>
                                                </h2>
                                                <div id="notifCollapse3" class="accordion-collapse collapse"
                                                    aria-labelledby="notifHeading3"
                                                    data-bs-parent="#notificationAccordion">
                                                    <div class="accordion-body p-0">
                                                        <ul class="list-group">
                                                            @foreach (Auth::user()->adminNotif()['starterkit_given'] as $personal)
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <a href="{{ route('users.index.onboarding.detail', $personal->id) }}"
                                                                            class="text-decoration-none">
                                                                            {{ $personal->fullname }}
                                                                        </a>
                                                                        <div class="text-muted"
                                                                            style="font-size: 0.85em;">
                                                                            NPK : {{ $personal->npk ?? '-' }}
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="notifHeading4">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#notifCollapse4"
                                                        aria-expanded="false" aria-controls="notifCollapse4">
                                                        Starter Kit Received
                                                    </button>
                                                </h2>
                                                <div id="notifCollapse4" class="accordion-collapse collapse"
                                                    aria-labelledby="notifHeading4"
                                                    data-bs-parent="#notificationAccordion">
                                                    <div class="accordion-body p-0">
                                                        <ul class="list-group">
                                                            @foreach (Auth::user()->adminNotif()['starterkit_received'] as $personal)
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <a href="{{ route('users.index.onboarding.detail', $personal->id) }}"
                                                                            class="text-decoration-none">
                                                                            {{ $personal->fullname }}
                                                                        </a>
                                                                        <div class="text-muted"
                                                                            style="font-size: 0.85em;">
                                                                            NPK : {{ $personal->npk ?? '-' }}
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="dropdown-menu dropdown-menu-end p-3"
                                        aria-labelledby="notificationDropdown"
                                        style="min-width: 350px; right: 0; left: auto;"
                                        onclick="event.stopPropagation();">
                                        @if (Auth::user()->progressOnboarding() > 0)
                                            <p class="text-muted">
                                                <span>Onboarding Progress</span>
                                            </p>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ Auth::user()->progressOnboarding() }}%;"
                                                    aria-valuenow="{{ Auth::user()->progressOnboarding() }}"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    {{ Auth::user()->progressOnboarding() }}%
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-muted">{{ __('No onboarding progress data available.') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link d-flex align-items-center gap-2" href="javascript:void(0)"
                                    id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
                                    @php
                                        $pasFoto = Auth::user()->employeeDocs()->where('doc_type', 'Pas Foto')->first();
                                        $imgPath = $pasFoto
                                            ? asset('storage/' . $pasFoto->doc_path)
                                            : asset('assets/images/profile/user-1.jpg');
                                    @endphp
                                    <img src="{{ $imgPath }}" alt="" width="35" height="35"
                                        class="rounded-circle nav-icon-hover" style="object-fit:cover">
                                    <span class="d-none d-md-inline fs-4">{{ Auth::user()->fullname }}</span>
                                    <i class="ti ti-chevron-down"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="javascript:void(0)"
                                            class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">Hello, {{ Auth::user()->fullname }}</p>
                                        </a>
                                        <a class="btn btn-outline-primary mx-3 mt-2 d-block"
                                            href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!--  Header End -->
            <div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissable fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close float-end" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close float-end" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif
                @yield('content')
                <div class="py-6 px-6 text-center">
                    <p class="mb-0 fs-4">© Copyright Astra Visteon Indonesia 2025</p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/sidebarmenu.js"></script>
    <script src="/assets/js/app.min.js"></script>
    <script src="/assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="https://cdn.datatables.net/2.2.0/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>

</html>
