@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card p-4">
            <h3>Kepala Dinas Tenaga Kerja</h3>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <form class="mt-4" action="{{ $disnaker ? route('disnaker.update', $disnaker->nip) : route('disnaker.store') }}" method="POST">
                @csrf
                @if ($disnaker)
                    @method('PUT')
                @endif
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama"
                        value="{{ $disnaker ? $disnaker->nama : old('nama') }}" required>
                </div>
                <div class="form-group mt-3">
                    <label for="nip">NIP</label>
                    <input type="text" class="form-control" id="nip" name="nip"
                        value="{{ $disnaker ? $disnaker->nip : old('nip') }}" required>
                </div>
                <button type="submit" class="btn btn-primary mt-3">{{ $disnaker ? 'Update' : 'Create' }}</button>
            </form>
        </div>
    </div>
@endsection
