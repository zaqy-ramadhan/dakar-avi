@extends('layouts.layouts')

@section('content')
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100 px-2">
                <div class="col-md-8 col-lg-6 col-xxl-3">
                    <div class="card mb-0 shadow-lg border-0">
                        <div class="card-body p-5">
                            <!-- Logo & Header -->
                            <a href="{{ url('/') }}" class="text-nowrap logo-img text-center d-block pb-3 w-100">
                                <img src="../assets/images/logos/awork-logo.png" width="180" alt="Logo">
                            </a>
                            <p class="text-center text-muted small mb-4">AVI Workforce Onboarding & Record Keeper</p>

                            <!-- Title -->
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="ti ti-lock-question" style="font-size: 2.5rem; color: #0d6efd;"></i>
                                </div>
                                <h4 class="mb-2 fw-bold">Lupa Password?</h4>
                                <p class="text-muted">Masukkan email Anda untuk menerima link reset password</p>
                            </div>

                            <!-- Status Message -->
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show border-0" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-check-circle me-2"></i>
                                        <div>
                                            <strong>Berhasil!</strong>
                                            <p class="mb-0">{{ session('status') }}</p>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Form -->
                            <form method="POST" action="{{ route('password.email') }}" class="needs-validation">
                                @csrf

                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold">Email Address</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="ti ti-mail"></i>
                                        </span>
                                        <input id="email" type="email"
                                            class="form-control form-control-lg border-0 bg-light @error('email') is-invalid @enderror"
                                            name="email"
                                            value="{{ old('email') }}"
                                            required autocomplete="email" autofocus
                                            placeholder="nama@example.com">
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block mt-2">
                                            <i class="ti ti-alert-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold mb-3 rounded-2" style="padding: 0.75rem;">
                                    <i class="ti ti-mail-forward me-2"></i>
                                    Kirim Link Reset
                                </button>

                                <hr class="my-4 opacity-50">

                                <div class="text-center">
                                    <p class="text-muted mb-2">Sudah ingat password?</p>
                                    <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">
                                        <i class="ti ti-arrow-left me-1"></i>
                                        Kembali ke Login
                                    </a>
                                </div>
                            </form>

                            <!-- Info Box -->
                            <div class="alert alert-info border-0 mt-4 mb-0" style="background-color: #e7f3ff;">
                                <div class="d-flex">
                                    <i class="ti ti-info-circle me-2 text-info mt-1"></i>
                                    <small class="text-info">
                                        Link reset password akan dikirim ke email Anda dalam beberapa menit. Pastikan email yang Anda masukkan sudah terdaftar.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
