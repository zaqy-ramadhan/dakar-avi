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
        value="{{ \Carbon\Carbon::parse($user->offboarding?->resign_date ?? null)->format('Y-m-d') }}"
        @if (!in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3'])) readonly @endif>
    @error('resign_date')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="col-sm-6 col-md-4 col-lg-4 mb-3">
    <label for="" class="form-label">Termination reason</label>
    <select class="form-control" id="reason" name="reason"
        @if (!in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3'])) disabled @endif>
        <option value="">-- Select reason --</option>
        @foreach($reason as $item)
            <option value="{{ $item->reason }}"
                @if(($user->offboarding?->reason == $item->reason)) selected @endif>
                {{ $item->reason }}
            </option>
        @endforeach
    </select>
    @error('reason')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<button type="submit" class="btn btn-primary"
    @if (!in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3'])) hidden @endif>Submit</button>
</form>