@extends('layouts.admin')

@section('content')
    <div class="card" style="border-radius: 20px">
        {{-- <h2>User Details</h2> --}}
        <div class="card-header">
            <p class="fs-8 fw-bold">User Details</p>
        </div>

        <div class="card-body">

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

            @include('admin.users.form.tabs')
            <form id="userDetailsForm" action="{{ route('admin.users.details.store', $user->id) }}" method="POST"
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

                        <div class="row mb-3">
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="declaration" name="declaration"
                                        required>
                                    <label class="form-check-label" for="declaration">
                                        I DECLARE THAT ALL INFORMATION GIVEN ABOVE ARE TRUE & CORRECT. Any false/misleading
                                        of
                                        information will be the subject for reaction of my application. In the event that my
                                        application
                                        is accepted, I shall accept Employment Termination without any compensation and
                                        shall be
                                        liable
                                        for legal action in accordance to applicable regulations.
                                    </label>
                                </div>
                                @error('declaration')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-primary">Save User</button>
                    </div>
            </form>

            <div class="tab-pane py-4 px-2 fade" id="inventaris" role="tabpanel" aria-labelledby="inventaris-tab">

                @include('admin.users.form.inventoryNumber')

            </div>
        </div>
    </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#userDetailsForm').on('submit', function(e) {
                e.preventDefault();

                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.users.details.store', $user->id) }}",
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
            let educationCount = 1;
            const maxEducation = 2;

            document.getElementById("add-education").addEventListener("click", function() {
                if (educationCount < maxEducation) {
                    educationCount++;
                    let newEducation = document.getElementById("education_1").cloneNode(true);
                    newEducation.id = "education_" + educationCount;
                    document.getElementById("education-container").appendChild(newEducation);
                    document.getElementById("remove-education").style.display = "inline-block";
                    if (educationCount === maxEducation) this.style.display = "none";
                }
            });

            document.getElementById("remove-education").addEventListener("click", function() {
                if (educationCount > 1) {
                    document.getElementById("education-container").lastChild.remove();
                    educationCount--;
                    document.getElementById("add-education").style.display = "inline-block";
                    if (educationCount === 1) this.style.display = "none";
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
            let childCount = 1;
            const maxChild = 3;
            const childContainer = document.getElementById("child-container");

            document.getElementById("add-child").addEventListener("click", function() {
                if (childCount < maxChild) {
                    let newChild = document.getElementById("child_1").cloneNode(true);
                    newChild.id = "child_" + childCount;

                    // Perbarui input name dan kosongkan nilai input
                    let inputs = newChild.querySelectorAll("input");
                    inputs.forEach(input => {
                        if (input.classList.contains("child-name")) {
                            input.name = `child_name[${childCount}]`;
                        } else if (input.classList.contains("child-birth-date")) {
                            input.name = `child_birth_date[${childCount}]`;
                        } else if (input.classList.contains("child-education")) {
                            input.name = `child_education[${childCount}]`;
                        } else if (input.classList.contains("child-occupation")) {
                            input.name = `child_occupation[${childCount}]`;
                        }
                        input.value = ""; // Kosongkan nilai input baru
                    });

                    childContainer.appendChild(newChild);
                    childCount++;

                    document.getElementById("remove-child").style.display = "inline-block";
                    if (childCount === maxChild) this.style.display = "none";
                }
            });

            document.getElementById("remove-child").addEventListener("click", function() {
                if (childCount > 1) {
                    childContainer.lastChild.remove();
                    childCount--;

                    document.getElementById("add-child").style.display = "inline-block";
                    if (childCount === 1) this.style.display = "none";
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let trainingCount = 1;
            const maxtraining = 3;
            const trainingContainer = document.getElementById("training-container");

            document.getElementById("add-training").addEventListener("click", function() {
                if (trainingCount < maxtraining) {
                    let newtraining = document.getElementById("training_1").cloneNode(true);
                    newtraining.id = "training_" + trainingCount;

                    // Perbarui input name dan kosongkan nilai input
                    let inputs = newtraining.querySelectorAll("input");
                    inputs.forEach(input => {
                        if (input.classList.contains("training-name")) {
                            input.name = `training_name[${trainingCount}]`;
                        } else if (input.classList.contains("training-date")) {
                            input.name = `training_birth_date[${trainingCount}]`;
                        } else if (input.classList.contains("training-education")) {
                            input.name = `training_education[${trainingCount}]`;
                        } else if (input.classList.contains("training-occupation")) {
                            input.name = `training_occupation[${trainingCount}]`;
                        }
                        input.value = ""; // Kosongkan nilai input baru
                    });

                    trainingContainer.appendChild(newtraining);
                    trainingCount++;

                    document.getElementById("remove-training").style.display = "inline-block";
                    if (trainingCount === maxtraining) this.style.display = "none";
                }
            });

            document.getElementById("remove-training").addEventListener("click", function() {
                if (trainingCount >= 1) {
                    trainingContainer.lastChild.remove();
                    trainingCount--;

                    document.getElementById("add-training").style.display = "inline-block";
                    if (trainingCount === 1) this.style.display = "none";
                }
            });
        });
    </script>
@endpush
