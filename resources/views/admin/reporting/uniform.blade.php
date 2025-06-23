@extends('layouts.admin')

@section('content')
    <div class="card" style="border-radius: 20px">
        <div class="card-header">
            <p class="fs-8 fw-bold">Uniform Renewal Report</p>
        </div>
        <div class="card-body">
            <div class="col align-items-between d-flex mb-4">
                <form action="" method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="month" name="date" class="form-control"
                            value="{{ request('date', now()->format('Y-m')) }}">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                        <a href="{{ route('uniform.renewal', array_merge(request()->query(), ['export' => 'excel'])) }}"
                            class="btn btn-success">
                            <i class="ti ti-file-spreadsheet"></i> Export Excel
                        </a>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NPK</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($uniformRefresh as $index => $employee)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $employee['npk'] }}</td>
                                <td>{{ $employee['name'] }}</td>
                                <td>{{ $employee['department'] }}</td>
                                <td>
                                    <a href="{{ route('users.index.employment.detail', $employee['id']) }}"
                                        class="btn btn-sm btn-outline-primary"><i
                                            class="ti ti-clipboard-list fs-6"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data karyawan untuk refresh seragam.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
