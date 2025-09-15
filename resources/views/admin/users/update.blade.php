@extends('layouts.admin')

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

@section('content')
    @if ($permissionModal ?? false)
        <div class="modal fade show" id="permissionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" style="display:block;">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel">Persetujuan Data Pribadi</h5>
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                    </div>
                    <div class="modal-body">
                        <a href="{{ route('user.data-permission-pdf', $user->id) }}" target="_blank"
                            class="btn btn-outline-primary mb-4 fs-4 ms-2">Document Preview</a>
                        <canvas id="signature-pad"></canvas>
                        <br>
                        <p class="my-4">
                            Dengan ini saya menyatakan bahwa saya telah membaca dan menyetujui isi dokumen di atas,
                            dan bersedia menandatangani secara digital sebagai bukti persetujuan data pribadi saya.
                        </p>
                        <form id="signature-form" method="POST" action="{{ route('permission.signature') }}">
                            @csrf
                            <input type="hidden" name="signature" id="signature-input">
                            <button type="button" class="btn btn-outline-danger me-1" id="clear">Hapus</button>
                            <button class="btn btn-outline-primary" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('permissionModal'), {
                backdrop: 'static', 
                keyboard: false
            });
            myModal.show();
        });
        </script>
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
    @endif
    <div class="card" style="border-radius: 20px">
        <div class="card-header">
            <p class="fs-8 fw-bold">Personal Data</p>
        </div>


        <div class="card-body">

            <!-- Tampilkan pesan sukses atau error -->
            {{-- @if (session('success'))
                <div class="alert alert-success alert-dismissable fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @elseif (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif --}}



            @include('admin.users.form.tabs')

            <form id="userDetailsForm" action="{{ route('admin.users.details.update', $user->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="tab-content" id="myTabContent">

                    <div class="tab-pane py-4 px-2 fade show active" id="personal" role="tabpanel"
                        aria-labelledby="personal-tab">

                        @include('admin.users.form.personal')

                    </div>

                    <div class="tab-pane py-4 px-2 fade" id="uniform" role="tabpanel" aria-labelledby="uniform-tab">

                        @include('admin.users.form.size')

                    </div>

                    @if (Auth::user()->getRole() != 'internship')
                        <div class=" tab-pane py-4 px-2 fade" id="family" role="tabpanel" aria-labelledby="family-tab">

                            @include('admin.users.form.family')

                        </div>
                    @endif

                    <div class="tab-pane py-4 px-2 fade" id="socmed" role="tabpanel" aria-labelledby="socmed-tab">

                        @include('admin.users.form.socmed')

                    </div>

                    <div class="tab-pane py-4 px-2 fade" id="education" role="tabpanel" aria-labelledby="education-tab">

                        @include('admin.users.form.education')

                    </div>

                    <div class=" tab-pane py-4 px-2 fade" id="training" role="tabpanel" aria-labelledby="training-tab">

                        @include('admin.users.form.training')

                    </div>

                    <div class=" tab-pane py-4 px-2 fade" id="bank" role="tabpanel" aria-labelledby="bank-tab">

                        @include('admin.users.form.bank')

                    </div>

                    <div class="tab-pane py-4 px-2 fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">

                        @include('admin.users.form.document')

                        <button type="submit" id="submitBtn" class="btn btn-primary"
                            @if ($user->id != Auth::user()->id) hidden @endif>Update Data</button>
                    </div>

                </div>
            </form>

            {{-- <div class="tab-pane py-4 px-2 fade" id="inventaris" role="tabpanel" aria-labelledby="inventaris-tab">

                @include('admin.users.form.inventoryNumber')

            </div> --}}
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            var formElement = $('#userDetailsForm');
            if (formElement.length) {
                var userData = @json($user ?? null);
                var employeeDetail = @json($employeeDetail ?? null);
                var employeeFamily = @json($employeeFamily ?? null);
                var currentUserId = @json(Auth::id());
                //  console.log(currentUserId);
                //  console.log(userData.id);

                if (userData && employeeDetail && employeeFamily) {
                    formElement.find('input, textarea').prop('readonly', true);
                    formElement.find('select').prop('disabled', true);
                    if (userData.id === currentUserId) {
                        formElement.find('input, textarea').prop('readonly', false);
                        formElement.find('select').prop('disabled', false);


                    }
                } else if (userData.id !== currentUserId) {
                    formElement.find('input, textarea').prop('readonly', true);
                    formElement.find('select').prop('disabled', true);
                }
            } else {
                console.error("Element with ID 'employeeForm' not found.");
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#userDetailsForm').on('submit', function(e) {
                e.preventDefault();

                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.users.details.update', $user->id) }}",
                    method: "POST",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message ||
                                'User details saved successfully.',
                        }).then(() => {
                            // window.location.href = "{{ route('users.details') }}";
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to save user details. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });

                        $('#submitBtn').prop('disabled', false);
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let educationCount = @json($employeeEducation->count() ?? 1);
            const maxEducation = 2;

            const educationContainer = document.getElementById("education-container");
            const addEducationButton = document.getElementById("add-education");
            const removeEducationButton = document.getElementById("remove-education");

            if (educationCount <= maxEducation) {
                removeEducationButton.style.display = "inline-block";
            }

            if (educationCount === maxEducation) {
                addEducationButton.style.display = "none";
            }

            if (educationCount <= 1) {
                removeEducationButton.style.display = "none";
            }

            addEducationButton.addEventListener("click", function() {
                if (educationCount < maxEducation) {
                    let newEducation = document.getElementById("education_1").cloneNode(true);
                    newEducation.id = "education_" + educationCount;
                    newEducation.classList.add("education-entry");

                    // Kosongkan input dalam elemen baru
                    let inputs = newEducation.querySelectorAll("input");
                    inputs.forEach(input => {
                        input.value = "";
                    });

                    educationContainer.appendChild(newEducation);
                    educationCount++;

                    // Tampilkan tombol hapus jika ada lebih dari 1 item
                    removeEducationButton.style.display = "inline-block";

                    // Sembunyikan tombol tambah jika mencapai batas
                    if (educationCount === maxEducation) addEducationButton.style.display = "none";
                }
            });

            removeEducationButton.addEventListener("click", function() {
                let educationItems = educationContainer.querySelectorAll(".education-entry");
                if (educationItems.length > 1) {
                    educationItems[educationItems.length - 1].remove();
                    educationCount--;

                    // Tampilkan tombol tambah jika di bawah batas
                    addEducationButton.style.display = "inline-block";

                    // Sembunyikan tombol hapus jika hanya 1 item tersisa
                    if (educationCount === 1) removeEducationButton.style.display = "none";
                }
            });
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const maritalStatus = document.getElementById("marital_status");
            const partnerSection = document.getElementById("partner");
            const childrenSection = document.getElementById("children");

            function toggleFamilyFields() {
                const selectedValue = maritalStatus.value;
                if (selectedValue === "Belum Menikah" || selectedValue === "Duda / Janda Tanpa Anak") {
                    partnerSection.style.display = "none";
                    childrenSection.style.display = "none";
                } else if (selectedValue === "Duda / Janda Dengan Anak") {
                    partnerSection.style.display = "none";
                    childrenSection.style.display = "block"
                } else {
                    partnerSection.style.display = "block";
                    childrenSection.style.display = "block"
                }
            }

            maritalStatus.addEventListener("change", toggleFamilyFields);
            toggleFamilyFields();
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let childCount = @json($employeeFamily->where('type', 'child')->count() ?? 1);
            // console.log(childCount);
            const maxChild = 3;

            const childContainer = document.getElementById("child-container");
            const addChildButton = document.getElementById("add-child");
            const removeChildButton = document.getElementById("remove-child");

            if (childCount <= maxChild) {
                removeChildButton.style.display = "inline-block";
            }

            if (childCount >= maxChild) {
                removeChildButton.style.display = "inline-block";
            }

            if (childCount >= maxChild) {
                addChildButton.style.display = "none";
            }

            if (childCount <= 1) {
                removeChildButton.style.display = "none";
            }

            addChildButton.addEventListener("click", function() {
                if (childCount < maxChild) {
                    let newChild = document.getElementById("child_1").cloneNode(true);
                    newChild.id = "child_" + childCount;
                    newChild.classList.add("children-entry");

                    let inputs = newChild.querySelectorAll("input");
                    inputs.forEach((input) => {
                        if (input.classList.contains("child-name")) {
                            input.name = `child_name[${childCount}]`;
                        } else if (input.classList.contains("child-birth-date")) {
                            input.name = `child_birth_date[${childCount}]`;
                        } else if (input.classList.contains("child-education")) {
                            input.name = `child_education[${childCount}]`;
                        } else if (input.classList.contains("child-occupation")) {
                            input.name = `child_occupation[${childCount}]`;
                        }
                        input.value = "";
                    });

                    childContainer.appendChild(newChild);
                    childCount++;
                    // console.log(childCount);

                    removeChildButton.style.display = "inline-block";
                    addChildButton.style.display = "inline-block";
                    if (childCount === maxChild) addChildButton.style.display = "none";
                }
            });

            removeChildButton.addEventListener("click", function() {
                let childItems = childContainer.querySelectorAll(".children-entry");
                if (childItems.length > 1) {
                    childItems[childItems.length - 1].remove();
                    childCount--;
                    // console.log(childCount);

                    addChildButton.style.display = "inline-block";
                    if (childCount === 1) removeChildButton.style.display = "none";
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let trainingCount = @json($employeeTraining->count() ?? 1);
            const maxTraining = 2;

            const trainingContainer = document.getElementById("training-container");
            const addTrainingButton = document.getElementById("add-training");
            const removeTrainingButton = document.getElementById("remove-training");

            // Tampilkan tombol hapus jika lebih dari 1 item
            removeTrainingButton.style.display = trainingCount > 0 ? "inline-block" : "none";

            // Sembunyikan tombol tambah jika sudah maksimal
            if (trainingCount === maxTraining) {
                addTrainingButton.style.display = "none";
            }

            addTrainingButton.addEventListener("click", function() {
                if (trainingCount < maxTraining) {
                    let newTraining = document.getElementById("training_1").cloneNode(true);
                    newTraining.id = "training_" + trainingCount;
                    newTraining.classList.add("training-entry");

                    // Kosongkan input dalam elemen baru
                    let inputs = newTraining.querySelectorAll("input");
                    inputs.forEach(input => {
                        if (input.classList.contains("training-name")) {
                            input.name = `training_name[${trainingCount}]`;
                        } else if (input.classList.contains("training-birth-date")) {
                            input.name = `training_birth_date[${trainingCount}]`;
                        } else if (input.classList.contains("training-education")) {
                            input.name = `training_education[${trainingCount}]`;
                        } else if (input.classList.contains("training-occupation")) {
                            input.name = `training_occupation[${trainingCount}]`;
                        }
                        input.value = ""; // Kosongkan nilai input baru
                    });

                    trainingContainer.appendChild(newTraining);
                    trainingCount++;

                    // Tampilkan tombol hapus jika ada lebih dari 1 item
                    removeTrainingButton.style.display = "inline-block";

                    // Sembunyikan tombol tambah jika mencapai batas
                    if (trainingCount === maxTraining) addTrainingButton.style.display = "none";
                }
            });

            removeTrainingButton.addEventListener("click", function() {
                let trainingItems = trainingContainer.querySelectorAll(".training-entry");
                if (trainingItems.length > 0) {
                    trainingItems[trainingItems.length - 1].remove();
                    trainingCount--;

                    // Tampilkan tombol tambah jika di bawah batas
                    addTrainingButton.style.display = "inline-block";

                    // Sembunyikan tombol hapus jika hanya 1 item tersisa
                    if (trainingCount === 0) removeTrainingButton.style.display = "none";
                }
            });
        });
    </script>
@endpush
