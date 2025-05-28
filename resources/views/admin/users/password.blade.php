    @extends('layouts.admin')

    @section('content')

    <div class="card" style="border-radius: 20px">
    
    <div class="card-header">
        <p class="fs-6 fw-bold">Change Password</p>
    </div>
    <div class="card-body">
        <form action="{{ route('change.password') }}" method="post">
            @csrf
                <div class="col-3">
                    <label for="old_password" class="form-label">Old Password</label>
                    <input 
                        type="password" 
                        class="form-control @error('old_password') is-invalid @enderror" 
                        id="old_password" 
                        name="old_password" 
                        placeholder="Enter old password"
                    >
                    @error('old_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-3 mt-4">
                    <label for="new_password" class="form-label">New Password</label>
                    <input 
                        type="password" 
                        class="form-control @error('new_password') is-invalid @enderror" 
                        id="new_password" 
                        name="new_password" 
                        placeholder="Enter new password"
                    >
                    @error('new_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>    
                <div class="col-3 mt-4">
                    <button class="btn btn-primary" type="submit" >Submit</button>
                </div>
        </form>
    </div>

    </div>

    @endsection
