@extends('layouts.adminbase')
@section('content')
    <div class="container">
        <h3>ใบโอน #[{{ $transfer->id }}]</h3>
        <p>จากสาขา: {{ $transfer->fromBranch->name }} → ไปยัง: {{ $transfer->toBranch->name }}</p>
        <p>วันที่โอน: {{ $transfer->transfer_date->format('Y-m-d') }}</p>

        <div class="mb-3">
            <label>สแกน Barcode:</label>
            <input id="scanInput" class="form-control" autocomplete="off">
        </div>

        <table class="table table-bordered" id="itemsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Barcode</th>
                    <th>ชื่อสินค้า</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transfer->items as $i => $it)
                    <tr data-barcode="{{ $it->barcode }}">
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $it->barcode }}</td>
                        <td>{{ $it->purchaseItem->item_name }}</td>
                        <td class="status-cell">
                            @if ($it->scanned == '1')
                                <span class="badge bg-success">สแกนแล้ว</span>
                            @else
                                <span class="badge bg-secondary">รอสแกน</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if (!$transfer->isReceived())
            <form action="{{ route('stock_transfers.receive', $transfer) }}" method="POST">
                @csrf
                <button id="receiveBtn" type="submit" class="btn btn-primary" disabled>รับเข้า</button>
            </form>
        @endif
    </div>
@endsection

@section('js')
    <script>
        const scanInput = document.getElementById('scanInput');
        const tbody = document.getElementById('itemsTable').querySelector('tbody');
        const receiveBtn = document.getElementById('receiveBtn');
        scanInput.focus();

        scanInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                let bc = this.value.trim();
                if (!bc) return;
                fetch(`{{ url('stock_transfers') }}/{{ $transfer->id }}/scan`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        barcode: bc
                    })
                }).then(r => r.json()).then(j => {
                    let row = tbody.querySelector(`tr[data-barcode="${bc}"]`);
                    if (row) {
                        row.querySelector('.status-cell').innerHTML =
                            '<span class="badge bg-success">สแกนแล้ว</span>';
                    }
                    this.value = '';
                    checkAllScanned();
                });
            }
        });

        function checkAllScanned() {
            let all = Array.from(tbody.querySelectorAll('tr'));
            let done = all.every(r => r.querySelector('.status-cell').textContent.includes('สแกนแล้ว'));
            receiveBtn.disabled = !done;
        }
    </script>
@endsection
