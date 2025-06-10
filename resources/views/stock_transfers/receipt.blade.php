@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h3 class="mb-4">รายการรอรับสินค้า (Receipt)</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('stock_transfers.receipt_store') }}" method="POST">
            @csrf
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Transfer ID</th>
                        <th>จากสาขา</th>
                        <th>ไปยังสาขา</th>
                        <th>วันที่โอน</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr>
                            <td><input type="checkbox" name="transfer_id[]" value="{{ $transfer->id }}"></td>
                            <td>{{ $transfer->id }}</td>
                            <td>{{ $transfer->fromBranch->branch_name }}</td>
                            <td>{{ $transfer->toBranch->branch_name }}</td>
                            <td>{{ $transfer->transfer_date->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">ไม่มีรายการรอรับสินค้า</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">ยืนยันรับสินค้า</button>
        </form>
    </div>

    @section('js')
        <script>
            document.getElementById('selectAll').addEventListener('change', function() {
                document.querySelectorAll('input[name="transfer_id[]"]').forEach(cb => cb.checked = this.checked);
            });
        </script>
    @endsection
@endsection
