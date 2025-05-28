    <div class="row">
        <div class="col-lg-3 col-md-12 col-sm-12">
            <label for="instagram" class="form-label">Instagram</label>
            <input 
                type="text" 
                class="form-control @error('instagram') is-invalid @enderror" 
                id="instagram" 
                name="instagram" 
                placeholder="Enter Instagram account link" 
                value="{{ old('instagram', $user->employeeSocmed->where('type', 'instagram')->first()->account  ?? '') }}"
            >
            @error('instagram')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-lg-3 col-md-12 col-sm-12">
            <label for="facebook" class="form-label">Facebook</label>
            <input 
                type="text" 
                class="form-control @error('facebook') is-invalid @enderror" 
                id="facebook" 
                name="facebook" 
                placeholder="Enter Facebook account link" 
                value="{{ old('facebook', $user->employeeSocmed->where('type', 'facebook')->first()->account ?? '') }}"
            >
            @error('facebook')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-lg-3 col-md-12 col-sm-12">
            <div class="mb-3">
                <label for="linkedin" class="form-label">LinkedIn</label>
                <input 
                    type="text" 
                    class="form-control @error('linkedin') is-invalid @enderror" 
                    id="linkedin" 
                    name="linkedin" 
                    placeholder="Enter LinkedIn account link" 
                    value="{{ old('linkedin', $user->employeeSocmed->where('type', 'linkedin')->first()->account ?? '') }}"
                >
                @error('linkedin')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
