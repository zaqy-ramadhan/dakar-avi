<div class="row mb-3">
    <form action="{{ route('employee-inventory-number.store') }}" method="POST">
        @csrf

        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
                <label for="bpjs" class="form-label">Nomor BPJS Kesehatan</label>
                <input type="text" class="form-control" id="bpjs" name="bpjs"
                    value="{{ old('bpjs', $bpjs ? $bpjs->number : '') }}"
                    @if (Auth::user()->getRole() !== 'admin' || Request::is('*profile*')) disabled @endif>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 mb-3">
                {{-- <label for="eslip" class="form-label">Akun E-Slip</label> --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="username_eslip" class="form-label">Username E-Slip</label>
                        <input placeholder="Username" type="text" class="form-control" id="username_eslip"
                            name="username_eslip" value="{{ old('username_eslip', $eslip ? $eslip->number : '') }}"
                            @if (Auth::user()->getRole() !== 'admin' || Request::is('*profile*')) disabled @endif>
                    </div>
                    <div class="col-md-6">
                        <label for="password_eslip" class="form-label">Password E-Slip</label>
                        <input placeholder="Password" type="text" class="form-control" id="password_eslip"
                            name="password_eslip"
                            value="{{ old('password_eslip', $pass_eslip ? $pass_eslip->number : '') }}"
                            @if (Auth::user()->getRole() !== 'admin' || Request::is('*profile*')) disabled @endif>
                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-4 mb-3">
                <label for="bpjstk" class="form-label">Nomor BPJS TK</label>
                <input type="text" class="form-control" id="bpjstk" name="bpjstk"
                    value="{{ old('bpjstk', $bpjstk ? $bpjstk->number : '') }}"
                    @if (Auth::user()->getRole() !== 'admin' || Request::is('*profile*')) disabled @endif>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 mb-3">
                {{-- <label for="greatday" class="form-label">Akun Great Day</label> --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="username_greatday" class="form-label">Username Greatday</label>
                        <input placeholder="Username" type="text" class="form-control" id="username_greatday"
                            name="username_greatday"
                            value="{{ old('username_greatday', $greatday ? $greatday->number : '') }}"
                            @if (Auth::user()->getRole() !== 'admin' || Request::is('*profile*')) disabled @endif>
                    </div>
                    <div class="col-md-6">
                        <label for="password_greatday" class="form-label">Password Greatday</label>
                        <input placeholder="Password" type="text" class="form-control" id="password_greatday"
                            name="password_greatday"
                            value="{{ old('password_greatday', $pass_greatday ? $pass_greatday->number : '') }}"
                            @if (Auth::user()->getRole() !== 'admin' || Request::is('*profile*')) disabled @endif>
                    </div>
                </div>

            </div>
        </div>
        @if (Auth::user()->getRole() === 'admin' && !Request::is('*profile*'))
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        @endif
    </form>
</div>
