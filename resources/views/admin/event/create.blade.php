@extends('layouts.admin')

@push('styles')
    <!-- Quill Editor CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Dropzone CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.css">
@endpush

@section('content')
    <div class="card p-4 mt-5">
        <h2>Create New Event</h2>

        <!-- Tampilkan pesan sukses atau error -->
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

        <form id="eventForm" action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row mb-3">
                <div class="col">
                    <label for="name" class="form-label">Event Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                        required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col">
                    <label for="date" class="form-label">Event Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ old('date') }}"
                        required>
                    @error('date')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Event Address</label>
                <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}"
                    required>
                @error('address')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <div id="editor" style="height: 200px"></div>
                <input type="hidden" name="description" id="description">
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Event Image</label>
                <div id="imageDropzone" class="dropzone"></div>
                <input type="hidden" name="image" id="image" value="">
                @error('image')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="status" name="status">
                <label class="form-check-label" for="status">Active</label>
            </div>

            <button type="submit" class="btn btn-primary">Save Event</button>
        </form>
    </div>
@endsection

@push('scripts')
    <!-- Quill Editor JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <!-- Dropzone JS -->
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.js"></script>
    <script>
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        // Quill Editor
        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Write your event description...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    ['link', 'image'],
                    [{
                        list: 'ordered'
                    }, {
                        list: 'bullet'
                    }],
                ],
            },
        });

        // Menyinkronkan isi Quill Editor ke input hidden 'description'
        quill.on('text-change', function() {
            document.getElementById('description').value = quill.root.innerHTML;
            // console.log(quill.root.innerHTML);
        });

        // Dropzone setup for image upload
        var imageDropzone = new Dropzone('#imageDropzone', {
            url: '/admin/events/upload-image', // Route for image upload
            addRemoveLinks: true,
            acceptedFiles: 'image/*',
            headers: {
                'X-CSRF-TOKEN': csrfToken, // Menambahkan CSRF token ke header
            },
            maxFiles: 1,
            init: function() {
                this.on("success", function(file, response) {
                    console.log(response);
                    document.getElementById('image').value = response.imagePath;
                });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Konfirmasi sebelum submit form
        document.getElementById('eventForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Hentikan submit form

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to save this event.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!',
                buttonsStyling: false, // Matikan styling default SweetAlert
                customClass: {
                    popup: 'small-swal', // Kustomisasi ukuran
                    confirmButton: 'btn btn-outline-primary mx-2', // Warna tombol utama
                    cancelButton: 'btn btn-outline-danger mx-2', // Warna tombol cancel
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    // Lanjutkan submit form jika user mengonfirmasi
                    e.target.submit();
                }
            });
        });
    </script>
@endpush
