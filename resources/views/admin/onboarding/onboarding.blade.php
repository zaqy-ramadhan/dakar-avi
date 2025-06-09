@extends('layouts.admin')

@section('content')
    <div class="card p-4" style="border-radius: 20px">
        @if (Request::is('*onboarding*'))
            <h2>Employee Onboarding</h2>
        @elseif (Request::is('*offboarding*'))
            <h2>Employee Offboarding</h2>
        @else
            <h2>Employment Data</h2>
        @endif

        @include('admin.users.form.job')

    </div>
@endsection
@push('scripts')
@endpush
