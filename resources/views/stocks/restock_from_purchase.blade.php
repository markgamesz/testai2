{{-- resources/views/stocks/restock_from_purchase.blade.php --}}
@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h3 class="mb-4">Restock จากบิล: {{ $purchaseNumber }} </h3>
        {{-- purchaseNumber คือเลขบิล เช่น “PA25060500001” ที่ Controller ส่งมา --}}

        {{-- Flash Message --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('stocks.store_restock') }}" method="POST" id="restockForm">
            @csrf

            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>สินค้า</th>
                        <th>ราคาต่อชิ้น (฿)</th>
                        <th>คงเหลือในบิล</th>
                        <th>จำนวนที่จะ Restock</th>
                        <th>ราคาขายรวม (฿)</th>
                        <th>ต้นทุนต่อหน่วย (฿)</th>
                        <th>ต้นทุนรวม (฿)</th>
                        <th>% กำไร</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($restockItems as $item)
                        <tr data-item-id="{{ $item->id }}" class="restock-row">
                            {{-- ชื่อสินค้า --}}
                            <td>{{ $item->product_name }}</td>

                            {{-- ราคาต่อชิ้น --}}
                            <td>
                                <span
                                    class="unit-price">{{ number_format($item->unit_price * $item->remain_quantity, 2) }}</span>
                                {{-- เก็บราคาต่อชิ้นใน hidden input (ถ้าต้องส่งไป backend) --}}
                                <input type="hidden" name="unit_price[{{ $item->id }}]"
                                    value="{{ number_format($item->unit_price, 2, '.', '') }}">
                            </td>

                            {{-- คงเหลือในบิล --}}
                            <td>
                                <span class="remain-qty">{{ $item->remain_quantity }}</span>
                            </td>

                            {{-- จำนวนที่จะ Restock (input) --}}
                            <td>
                                <input type="number" name="restock_qty[{{ $item->id }}]"
                                    class="form-control restock-qty" min="0" max="{{ $item->remain_quantity }}"
                                    value="0" data-max="{{ $item->remain_quantity }}">
                                @error("restock_qty.$item->id")
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>

                            {{-- ราคาขายรวม --}}
                            <td>
                                <input type="text" class="form-control sale-total text-end"
                                    name="sale_total[{{ $item->id }}]" value="0.00" readonly>
                            </td>

                            {{-- ต้นทุนต่อหน่วย --}}
                            <td>
                                <span class="cost-unit">{{ number_format($item->unit_price, 2) }}</span>
                                <input type="hidden" name="unit_cost[{{ $item->id }}]" class="unit-cost-hidden"
                                    value="{{ number_format($item->unit_price, 2, '.', '') }}">
                            </td>

                            {{-- ต้นทุนรวม --}}
                            <td>
                                <input type="text" class="form-control cost-total text-end"
                                    name="cost_total[{{ $item->id }}]" value="0.00" readonly>
                            </td>

                            {{-- % กำไร (Margin%) --}}
                            <td>
                                <input type="text" class="form-control margin-pct text-end"
                                    name="margin_pct[{{ $item->id }}]" value="0.00" readonly>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">บันทึก</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">กลับ</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // ฟังก์ชันคำนวณทุกแถว เมื่อเปลี่ยนแปลงจำนวนที่จะ Restock
            function calculateRow($row) {
                // ดึงค่าจากแต่ละช่อง
                let qty = parseFloat($row.find('.restock-qty').val()) || 0;
                let unitPrice = parseFloat($row.find('input[name^="unit_price"]').val()) || 0;
                let unitCost = parseFloat($row.find('.unit-cost-hidden').val()) || 0;

                // คำนวณราคาขายรวม = qty × unitPrice
                let saleTotal = qty * unitPrice;
                // คำนวณต้นทุนรวม = qty × unitCost
                let costTotal = qty * unitCost;

                // คำนวณ % กำไร = (saleTotal − costTotal) / saleTotal × 100
                let marginPct = 0;
                if (saleTotal > 0) {
                    marginPct = ((saleTotal - costTotal) / saleTotal) * 100;
                }

                // ปัด 2 ตำแหน่ง
                saleTotal = saleTotal.toFixed(2);
                costTotal = costTotal.toFixed(2);
                marginPct = marginPct.toFixed(2);

                // แสดงผลลงใน input
                $row.find('.sale-total').val(saleTotal);
                $row.find('.cost-total').val(costTotal);
                $row.find('.margin-pct').val(marginPct);
            }

            // เมื่อมีการเปลี่ยนแปลงจำนวน Restock
            $('.restock-qty').on('input', function() {
                let $row = $(this).closest('.restock-row');

                // ถ้าใส่ค่าเกิน max ให้บังคับกลับเป็น max
                let max = parseInt($(this).data('max'));
                let val = parseInt($(this).val()) || 0;
                if (val > max) {
                    $(this).val(max);
                    val = max;
                }
                if (val < 0) {
                    $(this).val(0);
                    val = 0;
                }

                calculateRow($row);
            });

            // เมื่อโหลดหน้าครั้งแรก ให้คำนวณฟิลด์ทั้งหมดเป็น 0.00
            $('.restock-row').each(function() {
                calculateRow($(this));
            });
        });
    </script>
@endpush
