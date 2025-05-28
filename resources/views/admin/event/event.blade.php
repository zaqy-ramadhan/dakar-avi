@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
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
        <h2>Event Management</h2>
        <a class="btn btn-primary mb-3 float-end" href="{{ route('admin.events.create') }}">Create
            Event</a>
        <div class="card" style="overflow-x: auto; width: 100%;">
            {!! $dataTable->table() !!}
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
