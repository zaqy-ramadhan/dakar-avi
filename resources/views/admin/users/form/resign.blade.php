<form class="mb-4"
action="{{ $user->offboarding ? route('offboarding.update', $user->id) : route('offboarding.store', $user->id) }}"
method="post">
@csrf
@if ($user->offboarding)
    @method('PUT')
@endif

<div class="col-sm-6 col-md-4 col-lg-4 mb-3">
    <label for="" class="form-label">Termination date</label>
    <input type="date" class="form-control" id="resign_date" name="resign_date"
         @if($user->latestEmployeeJob?->employment_status == false) value="{{ \Carbon\Carbon::parse($user->offboarding?->resign_date ?? null)->format('Y-m-d') }}" @endif
        @if (Auth::user()->getRole() != 'admin') readonly @endif>
    @error('resign_date')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="col-sm-6 col-md-4 col-lg-4 mb-3">
    <label for="" class="form-label">Termination reason</label>
    <input type="text" class="form-control" id="reason" name="reason"
        @if($user->latestEmployeeJob?->employment_status == false)value="{{ $user->offboarding?->reason ?? null }}" @endif
        @if (Auth::user()->getRole() != 'admin') readonly @endif>
    @error('reason')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<button type="submit" class="btn btn-primary"
    @if (Auth::user()->getRole() != 'admin') hidden @endif>Submit</button>
</form>