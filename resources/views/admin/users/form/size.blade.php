<div class="row mb-3">
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="blue_uniform_size" class="form-label">Ukuran Seragam Biru<span class="text-danger">*</span></label>
        {{-- <input type="text" class="form-control" id="blue_uniform_size" name="blue_uniform_size"
            value="{{ old('blue_uniform_size', $employeeDetail->blue_uniform_size ?? '') }}"> --}}
            <select name="blue_uniform_size" id="blue_uniform_size" class="form-select">
                <option value="">-- Pilih Ukuran --</option>
                <option value="S" {{ old('blue_uniform_size', $employeeDetail->blue_uniform_size ?? '') == 'S' ? 'selected' : '' }}>S</option>
                <option value="M" {{ old('blue_uniform_size', $employeeDetail->blue_uniform_size ?? '') == 'M' ? 'selected' : '' }}>M</option>
                <option value="L" {{ old('blue_uniform_size', $employeeDetail->blue_uniform_size ?? '') == 'L' ? 'selected' : '' }}>L</option>
                <option value="XL" {{ old('blue_uniform_size', $employeeDetail->blue_uniform_size ?? '') == 'XL' ? 'selected' : '' }}>XL</option>
                <option value="2XL" {{ old('blue_uniform_size', $employeeDetail->blue_uniform_size ?? '') == '2XL' ? 'selected' : '' }}>2XL</option>
                <option value="3XL" {{ old('blue_uniform_size', $employeeDetail->blue_uniform_size ?? '') == '3XL' ? 'selected' : '' }}>3XL</option>
            </select>
        <small class="text-muted">Wajib diisi</small>
        <div class="mt-2">
            <a href="{{ asset('assets/sizechart/blue_uniform.png') }}" class="size-chart-link">
                <img src="{{ asset('assets/sizechart/blue_uniform.png') }}" alt="Size Chart Seragam Biru"
                    class="img-fluid" style="max-width: 100px;">
            </a>
            <small class="text-muted">Klik untuk perbesar gambar</small>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="polo_shirt_size" class="form-label">Ukuran Kaos Polo<span class="text-danger">*</span></label>
        {{-- <input type="text" class="form-control" id="polo_shirt_size" name="polo_shirt_size"
            value="{{ old('polo_shirt_size', $employeeDetail->polo_shirt_size ?? '') }}"> --}}
            <select name="polo_shirt_size" id="polo_shirt_size" class="form-select">
                <option value="">-- Pilih Ukuran --</option>
                <option value="S" {{ old('polo_shirt_size', $employeeDetail->polo_shirt_size ?? '') == 'S' ? 'selected' : '' }}>S</option>
                <option value="M" {{ old('polo_shirt_size', $employeeDetail->polo_shirt_size ?? '') == 'M' ? 'selected' : '' }}>M</option>
                <option value="L" {{ old('polo_shirt_size', $employeeDetail->polo_shirt_size ?? '') == 'L' ? 'selected' : '' }}>L</option>
                <option value="XL" {{ old('polo_shirt_size', $employeeDetail->polo_shirt_size ?? '') == 'XL' ? 'selected' : '' }}>XL</option>
                <option value="2XL" {{ old('polo_shirt_size', $employeeDetail->polo_shirt_size ?? '') == '2XL' ? 'selected' : '' }}>2XL</option>
                <option value="3XL" {{ old('polo_shirt_size', $employeeDetail->polo_shirt_size ?? '') == '3XL' ? 'selected' : '' }}>3XL</option>
                <option value="4XL" {{ old('polo_shirt_size', $employeeDetail->polo_shirt_size ?? '') == '4XL' ? 'selected' : '' }}>4XL</option>
                <option value="5XL" {{ old('polo_shirt_size', $employeeDetail->polo_shirt_size ?? '') == '5XL' ? 'selected' : '' }}>5XL</option>
            </select>
        <small class="text-muted">Wajib diisi</small>
        <div class="mt-2">
            <a href="{{ asset('assets/sizechart/polo_shirt.jpeg') }}" class="size-chart-link">
                <img src="{{ asset('assets/sizechart/polo_shirt.jpeg') }}" alt="Size Chart Seragam Biru"
                    class="img-fluid" style="max-width: 100px;">
            </a>
            <small class="text-muted">Klik untuk perbesar gambar</small>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="safety_shoes_size" class="form-label">Ukuran Sepatu Safety<span class="text-danger">*</span></label>
        <select name="safety_shoes_size" id="safety_shoes_size" class="form-select">
            <option value="">-- Pilih Ukuran --</option>
            <option value="36" {{ old('safety_shoes_size', $employeeDetail->safety_shoes_size ?? '') == '36' ? 'selected' : '' }}>36</option>
            <option value="37" {{ old('safety_shoes_size', $employeeDetail->safety_shoes_size ?? '') == '37' ? 'selected' : '' }}>37</option>
            <option value="38" {{ old('safety_shoes_size', $employeeDetail->safety_shoes_size ?? '') == '38' ? 'selected' : '' }}>38</option>
            <option value="39" {{ old('safety_shoes_size', $employeeDetail->safety_shoes_size ?? '') == '39' ? 'selected' : '' }}>39</option>
            <option value="40" {{ old('safety_shoes_size', $employeeDetail->safety_shoes_size ?? '') == '40' ? 'selected' : '' }}>40</option>
            <option value="41" {{ old('safety_shoes_size', $employeeDetail->safety_shoes_size ?? '') == '41' ? 'selected' : '' }}>41</option>
            <option value="42" {{ old('safety_shoes_size', $employeeDetail->safety_shoes_size ?? '') == '42' ? 'selected' : '' }}>42</option>
            <option value="43" {{ old('safety_shoes_size', $employeeDetail->safety_shoes_size ?? '') == '43' ? 'selected' : '' }}>43</option>
            <option value="44" {{ old('safety_shoes_size', $employeeDetail->safety_shoes_size ?? '') == '44' ? 'selected' : '' }}>44</option>
            <option value="45" {{ old('safety_shoes_size', $employeeDetail->safety_shoes_size ?? '') == '45' ? 'selected' : '' }}>45</option>
            <option value="46" {{ old('safety_shoes_size', $employeeDetail->safety_shoes_size ?? '') == '46' ? 'selected' : '' }}>46</option>
        </select>
        <small class="text-muted">Wajib diisi</small>
        <div class="mt-2">
            <a href="{{ asset('assets/sizechart/safety_shoes.jpeg') }}" class="size-chart-link">
                <img src="{{ asset('assets/sizechart/safety_shoes.jpeg') }}" alt="Size Chart Seragam Biru"
                    class="img-fluid" style="max-width: 100px;">
            </a>
            <small class="text-muted">Klik untuk perbesar gambar</small>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="esd_uniform_size" class="form-label">Ukuran Seragam ESD<span class="text-danger">*</span></label>
        {{-- <input type="text" class="form-control" id="esd_uniform_size" name="esd_uniform_size"
            value="{{ old('esd_uniform_size', $employeeDetail->esd_uniform_size ?? '') }}"> --}}
        <select name="esd_uniform_size" id="esd_uniform_size" class="form-select">
            <option value="">-- Pilih Ukuran --</option>
            <option value="S" {{ old('esd_uniform_size', $employeeDetail->esd_uniform_size ?? '') == 'S' ? 'selected' : '' }}>S</option>
            <option value="M" {{ old('esd_uniform_size', $employeeDetail->esd_uniform_size ?? '') == 'M' ? 'selected' : '' }}>M</option>
            <option value="L" {{ old('esd_uniform_size', $employeeDetail->esd_uniform_size ?? '') == 'L' ? 'selected' : '' }}>L</option>
            <option value="XL" {{ old('esd_uniform_size', $employeeDetail->esd_uniform_size ?? '') == 'XL' ? 'selected' : '' }}>XL</option>
            <option value="2XL" {{ old('esd_uniform_size', $employeeDetail->esd_uniform_size ?? '') == '2XL' ? 'selected' : '' }}>2XL</option>
            <option value="3XL" {{ old('esd_uniform_size', $employeeDetail->esd_uniform_size ?? '') == '3XL' ? 'selected' : '' }}>3XL</option>
            <option value="4XL" {{ old('esd_uniform_size', $employeeDetail->esd_uniform_size ?? '') == '4XL' ? 'selected' : '' }}>4XL</option>
            <option value="5XL" {{ old('esd_uniform_size', $employeeDetail->esd_uniform_size ?? '') == '5XL' ? 'selected' : '' }}>5XL</option>
        </select>
        <small class="text-muted">Wajib diisi</small>
        <div class="mt-2">
            <a href="{{ asset('assets/sizechart/esd_uniform.jpg') }}" class="size-chart-link">
                <img src="{{ asset('assets/sizechart/esd_uniform.jpg') }}" alt="Size Chart Seragam ESD"
                    class="img-fluid" style="max-width: 100px;">
            </a>
            <small class="text-muted">Klik untuk perbesar gambar</small>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
        <label for="esd_shoes_size" class="form-label">Ukuran Sepatu ESD<span class="text-danger">*</span></label>
        {{-- <input type="text" class="form-control" id="esd_shoes_size" name="esd_shoes_size"
            value="{{ old('esd_shoes_size', $employeeDetail->esd_shoe_size ?? '') }}"> --}}
        <select name="esd_shoes_size" id="esd_shoes_size" class="form-select">
            <option value="">-- Pilih Ukuran --</option>
            <option value="35" {{ old('esd_shoes_size', $employeeDetail->esd_shoes_size ?? '') == '35' ? 'selected' : '' }}>36</option>
            <option value="36" {{ old('esd_shoes_size', $employeeDetail->esd_shoes_size ?? '') == '36' ? 'selected' : '' }}>36</option>
            <option value="37" {{ old('esd_shoes_size', $employeeDetail->esd_shoes_size ?? '') == '37' ? 'selected' : '' }}>37</option>
            <option value="38" {{ old('esd_shoes_size', $employeeDetail->esd_shoes_size ?? '') == '38' ? 'selected' : '' }}>38</option>
            <option value="39" {{ old('esd_shoes_size', $employeeDetail->esd_shoes_size ?? '') == '39' ? 'selected' : '' }}>39</option>
            <option value="40" {{ old('esd_shoes_size', $employeeDetail->esd_shoes_size ?? '') == '40' ? 'selected' : '' }}>40</option>
            <option value="41" {{ old('esd_shoes_size', $employeeDetail->esd_shoes_size ?? '') == '41' ? 'selected' : '' }}>41</option>
            <option value="42" {{ old('esd_shoes_size', $employeeDetail->esd_shoes_size ?? '') == '42' ? 'selected' : '' }}>42</option>
            <option value="43" {{ old('esd_shoes_size', $employeeDetail->esd_shoes_size ?? '') == '43' ? 'selected' : '' }}>43</option>
            <option value="44" {{ old('esd_shoes_size', $employeeDetail->esd_shoes_size ?? '') == '44' ? 'selected' : '' }}>44</option>
            <option value="45" {{ old('esd_shoes_size', $employeeDetail->esd_shoes_size ?? '') == '45' ? 'selected' : '' }}>45</option>
            <option value="46" {{ old('esd_shoes_size', $employeeDetail->esd_shoes_size ?? '') == '46' ? 'selected' : '' }}>46</option>
        </select>
        <small class="text-muted">Wajib diisi</small>
        <div class="mt-2">
            <a href="{{ asset('assets/sizechart/esd_shoes.jpg') }}" class="size-chart-link">
                <img src="{{ asset('assets/sizechart/esd_shoes.jpg') }}" alt="Size Chart Sepatu ESD"
                    class="img-fluid" style="max-width: 100px;">
            </a>
            <small class="text-muted">Klik untuk perbesar gambar</small>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Size Chart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="" alt="Size Chart" class="img-fluid" id="modalImage">
            </div>
        </div>
    </div>
</div>

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sizeChartLinks = document.querySelectorAll('.size-chart-link');
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        const modalImage = document.getElementById('modalImage');

        sizeChartLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const imageUrl = this.getAttribute('href');
                modalImage.src = imageUrl;
                modal.show();
            });
        });
    });
</script>
    
@endpush
