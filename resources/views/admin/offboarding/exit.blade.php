@extends('layouts.layouts')

@section('content')
    <div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="card shadow-lg border-0 rounded-4 p-4 text-center" style="max-width: 600px; width: 100%;">
            <div class="mb-4">
                <img src="{{ asset('images/thank_you.svg') }}" alt="Thank You" class="img-fluid" style="max-height: 200px;">
            </div>
            <h2 class="fw-bold text-success mb-3">Terima Kasih!</h2>
            <p class="text-muted fs-5 mb-4">Kami telah menerima isian <strong>Exit Interview</strong> Anda.</p>
            <p class="text-muted">Masukan Anda sangat berarti untuk meningkatkan lingkungan kerja dan proses offboarding di perusahaan.</p>

            <div class="mt-4">
                <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill px-4 py-2">
                    <i class="ti ti-home me-2"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
@endsection
