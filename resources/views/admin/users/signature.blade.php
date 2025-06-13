@extends('layouts.admin')

@section('content')
    {{-- @dd(redirect()->back()) --}}
    <div class="card" style="border-radius: 20px">
        <div class="card-body">
            <a href="{{ redirect()->back()->getTargetUrl() }}" class="btn btn-outline-primary mb-4 fs-4">
                <i class="ti ti-chevron-left fs-4"></i> Kembali
            </a>
                @if ($type === 'contract')
                    <a href="{{ route('kontrak.preview', $employeeJob->id) }}" target="_blank"
                        class="btn btn-outline-primary mb-4 fs-4 ms-2">Document
                        Preview</a>
                @elseif ($type === 'sksmk' || $type === 'skhk')
                    <a href="{{ route('skhk.preview', $employeeJob->id) }}" target="_blank"
                        class="btn btn-outline-primary mb-4 fs-4 ms-2">Document
                        Preview</a>
                @elseif ($type === 'kerahasiaan')
                    <a href="{{ route('kerahasiaan.preview', $employeeJob->id) }}" target="_blank"
                        class="btn btn-outline-primary mb-4 fs-4 ms-2">Document Preview</a>
                @elseif ($type === 'kompensasi')
                    <a href="{{ route('kompensasi.preview', $employeeJob->id) }}" target="_blank"
                        class="btn btn-outline-primary mb-4 fs-4 ms-2">Document Preview</a>
                @elseif ($type === 'paklaring')
                    <a href="{{ route('paklaring.preview', $employeeJob->id) }}" target="_blank"
                        class="btn btn-outline-primary mb-4 fs-4 ms-2">Document Preview</a>
                @endif
            <h2 class="mb-4">{{ strtoupper($type) }} Signature</h2>
            <canvas id="signature-pad"></canvas>
            <br>
            <form id="signature-form" method="POST" action="{{ route('signature.store', $id) }}">
                @csrf
                <input type="hidden" name="signature" id="signature-input">
                <input type="hidden" name="id" value="{{ $id }}">
                <input type="hidden" name="type" value="{{ $type }}">
                <button type="button" class="btn btn-outline-danger me-1" id="clear">Hapus</button>
                <button class="btn btn-outline-primary" type="submit">Submit</button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
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

@push('scripts')
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
@endpush
