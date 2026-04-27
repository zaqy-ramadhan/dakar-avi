@extends('layouts.layouts')

@section('content')
    <div
        class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100">
                <div class="col-md-8 col-lg-6 col-xxl-3">
                    <div class="card mb-0">
                        <div class="card-body">
                            <a href="{{ url('/') }}" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                <img src="../assets/images/logos/awork-logo.png" width="180" alt="Logo">
                            </a>
                            <p class="text-center">AVI Workforce Onboarding & Record Keeper</p>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="npk" class="form-label">NPK</label>
                                    <input id="npk" type="text"
                                        class="form-control @error('npk') is-invalid @enderror" value="{{ old('npk') }}"
                                        required autocomplete="email" autofocus>
                                    <input type="hidden" name="npk" id="npk_encrypted">
                                    @error('npk')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                               <div class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" 
                                            required
                                            placeholder="Masukkan password"
                                            autocomplete="current-password">
                                        
                                        <button class="btn btn-outline-primary" type="button" id="btnTogglePassword" style="border-left: none;">
                                            <i class="ti ti-eye-off" id="iconEye"></i>
                                        </button>

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                    {{-- Input hidden untuk penampung hasil enkripsi --}}
                                    <input type="hidden" name="password" id="password_encrypted">
                                </div>
                                <div class="d-grid gap-2 mb-3">
                                    <a href="{{ route('password.request') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-semibold">
                                        <i class="ti ti-lock-question me-2"></i>
                                        Lupa Password?
                                    </a>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">
                                    Sign In
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jsencrypt@3.3.1/bin/jsencrypt.min.js"></script>

    <script>
        document.querySelector("form").addEventListener("submit", function(e) {
            const npk = document.querySelector("#npk").value;
            const password = document.querySelector("#password").value;
            const pub_key = @json($public_key);

            const encrypt = new JSEncrypt();
            encrypt.setPublicKey(pub_key);

            document.querySelector("#npk_encrypted").value = encrypt.encrypt(npk)
            document.querySelector("#password_encrypted").value = encrypt.encrypt(password)

            document.querySelector("#npk").disabled = true;
            document.querySelector("#password").disabled = true;
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const btnToggle = document.getElementById('btnTogglePassword');
        const iconEye = document.getElementById('iconEye');

        btnToggle.addEventListener('click', function () {
            // Cek apakah sekarang sedang disensor (type="password")
            if (passwordInput.type === 'password') {
                // Buka sensor
                passwordInput.type = 'text';
                
                // Ubah ikon jadi mata terbuka
                iconEye.classList.remove('ti-eye-off');
                iconEye.classList.add('ti-eye');
            } else {
                // Tutup sensor (kembali ke titik-titik)
                passwordInput.type = 'password';
                
                // Ubah ikon jadi mata coret
                iconEye.classList.remove('ti-eye');
                iconEye.classList.add('ti-eye-off');
            }
        });
    });
</script>
@endsection
