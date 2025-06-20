@extends('layouts.adminbase')
@section('content')
<div class="container">
  <h3>ประวัติการโอน</h3>
  <a href="{{ route('stock_transfers.create') }}" class="btn btn-primary mb-3">โอนใหม่</a>
  <table class="table table-bordered">
    <thead><tr><th>#</th><th>จากสาขา</th><th>ไปสาขา</th><th>วันที่</th><th>สถานะ</th></tr></thead>
    <tbody>
      @foreach($transfers as $t)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $t->fromBranch->branch_name }}</td>
        <td>{{ $t->toBranch->branch_name }}</td>
        <td>{{ $t->transfer_date->format('Y-m-d') }}</td>
        <td>{{ ucfirst($t->status) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  {{ $transfers->links() }}
</div>
@endsection
