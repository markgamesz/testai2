@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h3 class="mb-4">ประวัติการโอนสินค้า (Stock Transfers)</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>จากสาขา</th>
                    <th>ไปยังสาขา</th>
                    <th>วันที่โอน</th>
                    <th>สถานะ</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfers as $transfer)
                    <tr>
                        <td>{{ $loop->iteration + ($transfers->currentPage() - 1) * $transfers->perPage() }}</td>
                        <td>{{ $transfer->fromBranch->branch_name }}</td>
                        <td>{{ $transfer->toBranch->branch_name }}</td>
                        <td>{{ $transfer->transfer_date->format('Y-m-d') }}</td>
                        <td>{{ ucfirst($transfer->status) }}</td>
                        <td>
                            <a href="{{ route('stock_transfers.show', $transfer->id) }}" class="btn btn-sm btn-info">ดู</a>
                            @if ($transfer->status === 'in_transit')
                                <a href="{{ route('stock_transfers.receipt', ['transfer_id' => $transfer->id]) }}"
                                    class="btn btn-sm btn-success">รับสินค้า</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">ยังไม่มีการโอนสินค้า</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $transfers->links() }}
    </div>
@endsection
