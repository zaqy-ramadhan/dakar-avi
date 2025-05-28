@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card mb-3" style="border-radius: 20px">
            <div class="card-header">
                <p class="fs-8 fw-bold">Starter Kit Rule</p>
            </div>
            <div class="card-body">
                <a href="{{ route('inventory-rules.create') }}" class="btn btn-primary float-end">Tambah Rule</a>
            </div>
        </div>

        <table class="table table-bordered table-striped" id="datatable">
            <thead class="table">
                <tr>
                    <th>No.</th>
                    <th>Role</th>
                    <th>Department</th>
                    {{-- <th>Level</th>
                    <th>Job Status</th> --}}
                    <th>Items</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rules as $index => $rule)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $rule->dakarRole->role_name ?? '-' }}</td>
                        {{-- <td>{{ $rule->department->department_name ?? '-' }}</td> --}}
                        <td>
                            @foreach ($rule->department as $department)
                            <span class="badge bg-primary m-1">{{ $department->department_name }}</span>
                            @endforeach
                        </td>
                        {{-- <td>{{ $rule->level->level_name ?? '-' }}</td>
                        <td>{{ $rule->job_status ?? '-' }}</td> --}}
                        <td>
                            @foreach ($rule->items as $item)
                                <span class="badge bg-success m-1">{{ $item->item_name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('inventory-rules.edit', $rule->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-edit"></i>
                            </a>
                            <form action="{{ route('inventory-rules.destroy', $rule->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus rule ini?')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Tambahkan DataTables dan jQuery -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    // paginate: {
                    //     first: "Awal",
                    //     last: "Akhir",
                    //     next: "Berikutnya",
                    //     previous: "Sebelumnya"
                    // }
                }
            });
        });
    </script>
@endsection
