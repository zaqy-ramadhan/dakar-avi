@extends('layouts.admin')

@section('content')
<div class="card" style="border-radius: 20px">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h4>Payroll List</h4>
    <a href="{{ route('payroll.create') }}" class="btn btn-primary">+ New Payroll</a>
  </div>
  <div class="card-body">
    <table id="datatable" class="table table-bordered">
      <thead>
        <tr>
          <th>No</th>
          <th>Title</th>
          <th>Periode</th>
          <th>Total Employee</th>
          <th>Total Salary</th>
          <th>Action</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    
$(function() {
  $('#datatable').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route('payroll.index') }}',
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex' },
      { data: 'title', name: 'title' },
      { data: 'periode', name: 'periode' },
      { data: 'total_employee', name: 'total_employee' },
      { data: 'total_salary', name: 'total_salary' },
      { data: 'action', name: 'action', orderable: false, searchable: false },
    ]
  });
});
</script>
@endsection
