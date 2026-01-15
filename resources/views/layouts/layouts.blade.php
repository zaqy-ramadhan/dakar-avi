<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    {{-- <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" /> --}}
    <link rel="stylesheet" href="/assets/css/styles.min.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    @yield('content')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
@if (session('success'))
    <div class="alert alert-success alert-dismissable fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@elseif (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

</html>
