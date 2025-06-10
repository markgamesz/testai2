{{-- resources/views/stocks/process.blade.php --}}
@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h3 class="mb-4">ถอนสินค้าและบันทึก Stock Process</h3>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('stocks.process_store') }}" method="POST" id="processForm">
            @csrf

            {{-- 1. Withdraw table --}}
            <h4>1. ถอนสินค้า (Withdraw)</h4>
            <table class="table table-bordered align-middle" id="withdrawTable">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px">#</th>
                        <th>สินค้า</th>
                        <th>คงเหลือ</th>
                        <th>ราคาต่อหน่วย (฿)</th>
                        <th>ราคารวมคงเหลือ (฿)</th>
                        <th>จำนวนที่จะถอน</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchaseItems as $i => $item)
                        @php
                            $remain = $item->quantity;
                            $unitPrice = $item->quantity > 0;
                            $totalRemain = $remain * $item->price;
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->product_name }} ({{ $item->purchase->purchase_document_number }})</td>
                            <td>
                                <span class="remain-qty">{{ $remain }}</span>
                                <input type="hidden" name="withdraw_item_id[]" value="{{ $item->id }}">
                                <input type="hidden" name="withdraw_orig_qty[{{ $i }}]"
                                    value="{{ $remain }}">
                            </td>
                            <td>
                                <span class="remain-unit-price">{{ number_format($item->price, 2) }}</span>
                                <input type="hidden" name="withdraw_unit_price[{{ $i }}]"
                                    value="{{ number_format($item->price, 2, '.', '') }}">
                            </td>
                            <td>
                                <span class="remain-total">{{ number_format($totalRemain, 2) }}</span>
                            </td>
                            <td>
                                <input type="number" name="withdraw_qty[{{ $i }}]"
                                    class="form-control withdraw-qty" value="0" min="0"
                                    max="{{ $remain }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- 2. Dynamic Stock Process table --}}
            <h4 class="mt-4">2. บันทึก Stock Process (Dynamic)</h4>
            <table class="table table-bordered align-middle" id="stockProcessTable">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px">#</th>

                        <th>จำนวน</th>
                        <th>ราคาต่อหน่วย</th>
                        <th>ราคารวม</th>
                        <th>ต้นทุนต่อหน่วย</th>
                        <th>ต้นทุนรวม</th>
                        <th>% กำไร</th>
                        <th style="width:80px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="stock-row">
                        <td class="row-index">1</td>

                        <td>
                            <input type="number" name="stock_qty[]" class="form-control stock-qty" value="0"
                                min="0" required>
                        </td>
                        <td>
                            <input type="number" name="stock_unit_price[]" class="form-control stock-unit-price"
                                value="0.00" step="0.01" min="0" required>
                        </td>
                        <td>
                            <input type="text" name="stock_sale_total[]" class="form-control stock-sale-total text-end"
                                value="0.00" readonly>
                        </td>
                        <td>
                            <input type="text" name="stock_unit_cost[]" class="form-control stock-unit-cost text-end"
                                value="0.00" readonly>
                        </td>
                        <td>
                            <input type="text" name="stock_cost_total[]" class="form-control stock-cost-total text-end"
                                value="0.00" readonly>
                        </td>
                        <td>
                            <input type="text" name="stock_margin_pct[]" class="form-control stock-margin-pct text-end"
                                value="0.00" readonly>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-secondary btn-sm copy-stock-row" disabled>คัดลอก</button>
                            <button type="button" class="btn btn-danger btn-sm remove-stock-row" disabled>ลบ</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" id="addStockRowBtn" class="btn btn-primary mb-3">+ เพิ่มรายการ Stock</button>

            <div>
                <button type="submit" class="btn btn-success">บันทึกทั้งหมด</button>
                <a href="{{ route('stocks.index') }}" class="btn btn-secondary">ย้อนกลับ</a>
            </div>
        </form>
    </div>


@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.querySelector('#stockProcessTable tbody');
            const addBtn = document.getElementById('addStockRowBtn');

            function updateIndices() {
                tbody.querySelectorAll('tr').forEach((tr, i) => {
                    tr.querySelector('.row-index').textContent = i + 1;
                    tr.querySelectorAll('.remove-stock-row, .copy-stock-row')
                        .forEach(btn => btn.disabled = (i === 0));
                });
            }

            function recalc(tr) {
                const qty = parseFloat(tr.querySelector('.stock-qty').value) || 0;
                const up = parseFloat(tr.querySelector('.stock-unit-price').value) || 0;
                const unitCost = parseFloat(tr.querySelector('.select-stock-item')
                    .selectedOptions[0].dataset.unitCost) || 0;

                const saleTotal = qty * up;
                const costTotal = qty * unitCost;
                const marginPct = saleTotal > 0 ? (saleTotal - costTotal) / saleTotal * 100 : 0;

                tr.querySelector('.stock-sale-total').value = saleTotal.toFixed(2);
                tr.querySelector('.stock-unit-cost').value = unitCost.toFixed(2);
                tr.querySelector('.stock-cost-total').value = costTotal.toFixed(2);
                tr.querySelector('.stock-margin-pct').value = marginPct.toFixed(2);
            }

            // เมื่อคลิก +เพิ่ม
            addBtn.addEventListener('click', () => {
                const clone = tbody.querySelector('tr').cloneNode(true);
                // ล้างค่าใน clone
                clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                clone.querySelectorAll('input').forEach(inp => {
                    if (inp.classList.contains('stock-qty')) inp.value = '0';
                    else if (inp.classList.contains('stock-unit-price')) inp.value = '0.00';
                    else inp.value = '0.00';
                });
                tbody.appendChild(clone);
                updateIndices();
            });

            // Delegation: ลบ, คัดลอก, เปลี่ยนค่า
            tbody.addEventListener('click', e => {
                let tr;
                if (e.target.matches('.remove-stock-row')) {
                    if (tbody.querySelectorAll('tr').length > 1) {
                        e.target.closest('tr').remove();
                        updateIndices();
                    }
                }
                if (e.target.matches('.copy-stock-row')) {
                    tr = e.target.closest('tr');
                    const cp = tr.cloneNode(true);
                    tbody.appendChild(cp);
                    updateIndices();
                    recalc(cp);
                }
            });

            tbody.addEventListener('change', e => {
                if (e.target.matches('.select-stock-item')) {
                    recalc(e.target.closest('tr'));
                }
            });

            tbody.addEventListener('input', e => {
                if (e.target.matches('.stock-qty, .stock-unit-price')) {
                    recalc(e.target.closest('tr'));
                }
            });

            // เริ่มต้น
            updateIndices();
            tbody.querySelectorAll('tr').forEach(tr => recalc(tr));
        });
    </script>
@endsection
@endsection
