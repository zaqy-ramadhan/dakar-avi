@extends('layouts.admin')

@section('content')

<div class="card" style="border-radius: 20px">
    <div class="card-header">
        <p class="fs-8 fw-bold"> Laporan Karyawan Masuk</p>
    </div>
    <div class="card-body">
         <div class="col align-items-between d-flex mb-4">
            <form action="" method="GET" class="d-flex">
                <div class="input-group">
                    <select name="month" class="form-select">
                        @foreach ([
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ] as $num => $name)
                            <option value="{{ $num }}" {{ request('month', date('m')) == $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="number" name="year" class="form-control" min="2000" max="2100" value="{{ request('year', date('Y')) }}">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('joinedThisMonth', request()->query()) }}" class="btn btn-success">
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
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employeeJob as $index => $contract)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $contract['npk'] }}</td>
                            <td>{{ $contract['name'] }}</td>
                            <td>{{ $contract['department'] }}</td>
                            <td>{{ $contract['start_date'] }}</td>
                            <td>{{ $contract['end_date'] }}</td>
                            <td>{{ $contract['status'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada karyawan masuk untuk bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection