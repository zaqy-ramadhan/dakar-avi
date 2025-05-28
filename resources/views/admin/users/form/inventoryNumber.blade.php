<div class="row mb-3">
    <form
        action="{{ route('employee-inventory-number.store') }}"
        method="POST">
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
                <label for="eslip" class="form-label">Akun E-Slip</label>
                <div class="input-group">
                    <span class="input-group-text">Username</span>
                    <input placeholder="Username" type="text" class="form-control" id="eslip"
                    name="username_eslip" value="{{ old('username_eslip', $eslip ? $eslip->number : '') }}"
                    @if (Auth::user()->getRole() !== 'admin' || Request::is('*profile*')) disabled @endif>
                    <span class="input-group-text">Password</span>
                    <input placeholder="Password" type="text" class="form-control" id="eslip"
                    name="password_eslip" value="{{ old('password_eslip', $pass_eslip ? $pass_eslip->number : '') }}"
                    @if (Auth::user()->getRole() !== 'admin' || Request::is('*profile*')) disabled @endif>
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
                <label for="greatday" class="form-label">Akun Great Day</label>
                <div class="input-group">
                    <span class="input-group-text">Username</span>
                    <input placeholder="Username" type="text" class="form-control" id="greatday"
                    name="username_greatday" value="{{ old('username_greatday', $greatday ? $greatday->number : '') }}"
                    @if (Auth::user()->getRole() !== 'admin' || Request::is('*profile*')) disabled @endif>
                    <span class="input-group-text">Password</span>
                    <input placeholder="Password" type="text" class="form-control" id="greatday"
                    name="password_greatday" value="{{ old('password_greatday', $pass_greatday ? $pass_greatday->number : '') }}"
                    @if (Auth::user()->getRole() !== 'admin' || Request::is('*profile*')) disabled @endif>
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
