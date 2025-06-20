{{-- resources/views/stock_transfers/create.blade.php --}}
@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h2>โอนสต็อกข้ามสาขา</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('stock_transfers.store') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>จากสาขา</label>
                    <select id="from_branch" name="from_branch_id" class="form-select" required>
                        <option value="">-- เลือกสาขาต้นทาง --</option>
                        @foreach ($branches as $b)
                            <option value="{{ $b->branch_id_id }}">{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>ไปยังสาขา</label>
                    <select name="to_branch_id" class="form-select" required>
                        <option value="">-- เลือกสาขาปลายทาง --</option>
                        @foreach ($branches as $b)
                            <option value="{{ $b->branch_id_id }}">{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="stocks-section" style="display:none">
                <h5>เลือกรายการสต็อก</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>รหัส</th>
                            <th>ชื่อสินค้า</th>
                            <th>จำนวน</th>

                        </tr>
                    </thead>
                    <tbody id="stocks-body">
                        {{-- ข้อมูลจะถูกเติมด้วย AJAX --}}
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary">โอนสต็อก</button>
        </form>
    </div>
@section('js')
    <script>
        // เมื่อคลิก checkbox “เลือกทั้งหมด”
        $('#checkAll').on('change', function() {
            $('.check-item').prop('checked', $(this).prop('checked'));
        });

        // ถ้ามีการคลิก checkbox แต่ละแถว ให้คำนวนว่าทั้งหมดถูกเลือกหรือไม่
        $('.check-item').on('change', function() {
            const allChecked = $('.check-item:checked').length === $('.check-item').length;
            $('#checkAll').prop('checked', allChecked);
        });

        document.getElementById('from_branch').addEventListener('change', function() {
            const branchId = this.value;
            const section = document.getElementById('stocks-section');
            const tbody = document.getElementById('stocks-body');

            tbody.innerHTML = '';
            if (!branchId) {
                section.style.display = 'none';
                return;
            }

            fetch(`/stock_transfers/stocks/${branchId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    data.forEach(item => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                    <td><input type="checkbox" class="check-item" name="stock_ids[]" value="${item.id}" ></td>
                    <td>${item.barcode}</td>
                    <td>${item.product_name}</td>
                    <td>${item.quantity}</td>`;
                        tbody.appendChild(tr);
                    });
                    section.style.display = 'block';
                })
                .catch(err => {
                    console.error('Error fetching stocks:', err);
                    alert('ไม่สามารถโหลดรายการสต็อกได้ กรุณาลองใหม่อีกครั้ง');
                });
        });
    </script>
@endsection
@endsection
