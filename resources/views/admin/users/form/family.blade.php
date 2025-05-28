<!-- PARTNER (Pasangan) -->
<div id="partner">
    @php
        $employeeFamily = isset($user) && method_exists($user, 'employeeFamily') ? $user->employeeFamily : collect();
        $spouse = $employeeFamily->where('type', 'pasangan')->first() ?? null;
    @endphp

    <div class="row mb-3">
        <div class="col-sm-6 col-md-6 col-lg-6 mb-3">
            <label for="spouse_name" class="form-label">Nama Suami/Istri</label>
            <input type="text" class="form-control" id="spouse_name" name="spouse_name"
                value="{{ old('spouse_name', $spouse->name ?? '') }}">
            @error('spouse_name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-sm-6 col-md-6 col-lg-6 mb-3">
            <label for="marriage_year" class="form-label">Tahun Menikah</label>
            <input type="number" class="form-control" id="married_year" name="married_year"
                value="{{ old('married_year', $employeeDetail->married_year ?? '') }}">
            @error('married_year')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
            <label for="spouse_birth_date" class="form-label">Tanggal Lahir Suami/Istri</label>
            <input type="date" class="form-control" id="spouse_birth_date" name="spouse_birth_date"
                value="{{ old('spouse_birth_date', $spouse->birth_date ?? '') }}">
            @error('spouse_birth_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
            <label for="spouse_education" class="form-label">Pendidikan Suami/Istri</label>
            <input type="text" class="form-control" id="spouse_education" name="spouse_education"
                value="{{ old('spouse_education', $spouse->education ?? '') }}">
            @error('spouse_education')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
            <label for="spouse_occupation" class="form-label">Pekerjaan Suami/Istri</label>
            <input type="text" class="form-control" id="spouse_occupation" name="spouse_occupation"
                value="{{ old('spouse_occupation', $spouse->occupation ?? '') }}">
            @error('spouse_occupation')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<!-- CHILDREN -->
@php
    $children = $employeeFamily->where('type', 'child') ?? collect();
@endphp

<div id="children">
    <div id="child-container">
        @if ($children->count() > 0)
            @php $count = 0; @endphp
            @foreach ($children as $index => $child)
                @php $count++ @endphp
                <div class="children-entry row mb-3" id="child_{{ $count }}">
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label class="form-label">Nama Anak</label>
                        <input type="text" class="form-control child-name" name="child_name[{{ $index }}]"
                            value="{{ old("child_name.$index", $child->name) }}">
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label class="form-label">Tanggal Lahir Anak</label>
                        <input type="date" class="form-control child-birth-date"
                            name="child_birth_date[{{ $index }}]"
                            value="{{ old("child_birth_date.$index", $child->birth_date) }}">
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label class="form-label">Pendidikan Anak</label>
                        <input type="text" class="form-control child-education"
                            name="child_education[{{ $index }}]"
                            value="{{ old("child_education.$index", $child->education) }}">
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label class="form-label">Pekerjaan Anak</label>
                        <input type="text" class="form-control child-occupation"
                            name="child_occupation[{{ $index }}]"
                            value="{{ old("child_occupation.$index", $child->occupation) }}">
                    </div>
                </div>
            @endforeach
        @else
            <!-- Jika tidak ada data anak, tampilkan satu entri kosong -->
            <div class="children-entry row mb-3" id="child_1">
                <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label class="form-label">Nama Anak</label>
                    <input type="text" class="form-control child-name" name="child_name[0]" value="">
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label class="form-label">Tanggal Lahir Anak</label>
                    <input type="date" class="form-control child-birth-date" name="child_birth_date[0]"
                        value="">
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label class="form-label">Pendidikan Anak</label>
                    <input type="text" class="form-control child-education" name="child_education[0]" value="">
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label class="form-label">Pekerjaan Anak</label>
                    <input type="text" class="form-control child-occupation" name="child_occupation[0]"
                        value="">
                </div>
            </div>

        @endif
    </div>
    <button type="button" id="remove-child" class="btn btn-danger mb-3 me-2" style="display: none" @if (Auth::user()->id != $user->id) hidden @endif >Hapus Data
        Anak</button>
    <button type="button" class="btn btn-primary mb-3" id="add-child" @if ($user->id != Auth::user()->id) hidden @endif>Tambah Data Anak</button>
</div>


<!-- FATHER -->
<div class="row mb-3">
    @php
        $father = $employeeFamily->where('type', 'ayah')->first() ?? null;
    @endphp
    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
        <label for="father_name" class="form-label">Nama Bapak<span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="father_name" name="father_name"
            value="{{ old('father_name', $father->name ?? '') }}" >
        <small class="text-muted">Wajib diisi</small>
        @error('father_name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
        <label for="father_birth_date" class="form-label">Tanggal Lahir (Bapak)</label>
        <input type="date" class="form-control" id="father_birth_date" name="father_birth_date"
            value="{{ old('father_birth_date', $father->birth_date ?? '') }}">
        @error('father_birth_date')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
        <label for="father_education" class="form-label">Pendidikan (Bapak)</label>
        <input type="text" class="form-control" id="father_education" name="father_education"
            value="{{ old('father_education', $father->education ?? '') }}">
        @error('father_education')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
        <label for="father_occupation" class="form-label">Pekerjaan (Bapak)<span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="father_occupation" name="father_occupation"
            value="{{ old('father_occupation', $father->occupation ?? '') }}" >
        <small class="text-muted">Wajib diisi</small>
        @error('father_occupation')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- MOTHER -->
<div class="row mb-3">
    @php
        $mother = $employeeFamily->where('type', 'ibu')->first() ?? null;
    @endphp
    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
        <label for="mother_name" class="form-label">Nama Ibu<span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="mother_name" name="mother_name"
            value="{{ old('mother_name', $mother->name ?? '') }}" >
        <small class="text-muted">Wajib diisi</small>
        @error('mother_name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
        <label for="mother_birth_date" class="form-label">Tanggal Lahir (Ibu)</label>
        <input type="date" class="form-control" id="mother_birth_date" name="mother_birth_date"
            value="{{ old('mother_birth_date', $mother->birth_date ?? '') }}">
        @error('mother_birth_date')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
        <label for="mother_education" class="form-label">Pendidikan (Ibu)</label>
        <input type="text" class="form-control" id="mother_education" name="mother_education"
            value="{{ old('mother_education', $mother->education ?? '') }}">
        @error('mother_education')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
        <label for="mother_occupation" class="form-label">Pekerjaan (Ibu) <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="mother_occupation" name="mother_occupation"
            value="{{ old('mother_occupation', $mother->occupation ?? '') }}">
        <small class="text-muted">Wajib diisi</small>
        @error('mother_occupation')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- SIBLINGS (Saudara) -->
<div class="row mb-3">
    @php
        $sibling = $employeeFamily->where('type', 'saudara')->first() ?? null;
    @endphp
    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
        <label for="siblings_name" class="form-label">Nama Kakak / Adik</label>
        <input type="text" class="form-control" id="siblings_name" name="siblings_name"
            value="{{ old('siblings_name', $sibling->name ?? '') }}">
        @error('siblings_name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
        <label for="siblings_birth_date" class="form-label">Tanggal Lahir (Kakak / Adik)</label>
        <input type="date" class="form-control" id="siblings_birth_date" name="siblings_birth_date"
            value="{{ old('siblings_birth_date', $sibling->birth_date ?? '') }}">
        @error('siblings_birth_date')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
        <label for="siblings_education" class="form-label">Pendidikan (Kakak / Adik)</label>
        <input type="text" class="form-control" id="siblings_education" name="siblings_education"
            value="{{ old('siblings_education', $sibling->education ?? '') }}">
        @error('siblings_education')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
        <label for="siblings_occupation" class="form-label">Pekerjaan (Kakak / Adik)</label>
        <input type="text" class="form-control" id="siblings_occupation" name="siblings_occupation"
            value="{{ old('siblings_occupation', $sibling->occupation ?? '') }}">
        @error('siblings_occupation')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>
