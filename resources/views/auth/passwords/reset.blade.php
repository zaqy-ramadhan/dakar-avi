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
                                    <i class="ti ti-lock-check" style="font-size: 2.5rem; color: #0d6efd;"></i>
                                </div>
                                <h4 class="mb-2 fw-bold">Buat Password Baru</h4>
                                <p class="text-muted">Masukkan password baru Anda untuk mengamankan akun</p>
                            </div>

                            <!-- Form -->
                            <form method="POST" action="{{ route('password.update') }}" class="needs-validation">
                                @csrf

                                <input type="hidden" name="token" value="{{ $token }}">

                                <!-- Email Field (Read-only) -->
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold">Email Address</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="ti ti-mail"></i>
                                        </span>
                                        <input id="email" type="email"
                                            class="form-control form-control-lg border-0 bg-light @error('email') is-invalid @enderror"
                                            name="email"
                                            value="{{ $email ?? old('email') }}"
                                            required autocomplete="email" autofocus>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block mt-2">
                                            <i class="ti ti-alert-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Password Field -->
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-semibold">Password Baru</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="ti ti-lock"></i>
                                        </span>
                                        <input id="password" type="password"
                                            class="form-control form-control-lg border-0 bg-light @error('password') is-invalid @enderror"
                                            name="password"
                                            required autocomplete="new-password"
                                            placeholder="Minimal 8 karakter">
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block mt-2">
                                            <i class="ti ti-alert-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="text-muted d-block mt-2">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Password harus terdiri dari minimal 8 karakter, kombinasi huruf besar dan kecil, angka, dan simbol.
                                    </small>
                                </div>

                                <!-- Confirm Password Field -->
                                <div class="mb-4">
                                    <label for="password-confirm" class="form-label fw-semibold">Konfirmasi Password</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="ti ti-lock-check"></i>
                                        </span>
                                        <input id="password-confirm" type="password"
                                            class="form-control form-control-lg border-0 bg-light @error('password_confirmation') is-invalid @enderror"
                                            name="password_confirmation" required autocomplete="new-password"
                                            placeholder="Ulangi password baru">
                                    </div>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback d-block mt-2">
                                            <i class="ti ti-alert-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold mb-3 rounded-2" style="padding: 0.75rem;">
                                    <i class="ti ti-check me-2"></i>
                                    Reset Password
                                </button>

                                <hr class="my-4 opacity-50">

                                <div class="text-center">
                                    <p class="text-muted mb-2">Ingat password Anda?</p>
                                    <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">
                                        <i class="ti ti-arrow-left me-1"></i>
                                        Kembali ke Login
                                    </a>
                                </div>
                            </form>

                            <!-- Security Tips -->
                            <div class="alert alert-warning border-0 mt-4 mb-0" style="background-color: #fff8e1;">
                                <div class="d-flex">
                                    <i class="ti ti-shield-check me-2 text-warning mt-1"></i>
                                    <small class="text-warning">
                                        <strong>Tips Keamanan:</strong> Jangan bagikan password Anda dengan siapapun. Gunakan kombinasi karakter yang kuat untuk melindungi akun Anda.
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
