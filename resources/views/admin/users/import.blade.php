@extends('layouts.admin')

@section('content')
<div class="card p-4">
    <h1>Import Data</h1>
    <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Pilih File Excel</label>
            <input type="file" class="form-control" id="file" name="file" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Import</button>
    </form>
</div>
@endsection