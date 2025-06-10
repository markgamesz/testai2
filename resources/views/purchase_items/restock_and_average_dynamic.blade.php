{{-- resources/views/purchase_items/restock_and_average_dynamic.blade.php --}}
@extends('layouts.adminbase')

@section('content')
<div class="container">
    <h3 class="mb-4">Restock &amp; เฉลี่ยราคาต้นทุน (แบบ Dynamic)</h3>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('purchaseitems.update_stock') }}" method="POST" id="restockAverageForm">
        @csrf

        <table class="table table-bordered align-middle" id="restockTable">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>ชื่อสินค้า</th>
                    <th>จำนวนเดิม</th>
                    <th>ต้นทุนเดิม/ชิ้น (฿)</th>
                    <th>จำนวนที่จะเพิ่ม</th>
                    <th>ต้นทุนเพิ่ม/ชิ้น (฿)</th>
                    <th>จำนวนรวม</th>
                    <th>ต้นทุนเฉลี่ยใหม่/ชิ้น (฿)</th>
                    <th style="width: 80px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- ถ้ามี old inputs ให้วนลูปสร้างแถวเก่ากลับมา --}}
                @if(old('item_id'))
                    @foreach(old('item_id') as $index => $oldId)
                        @php
                            // ถอดข้อมูล PurchaseItem เดิมตาม ID
                            $model = \App\Models\PurchaseItem::find($oldId);
                            $origQty = $model ? $model->item_quantity : 0;
                            $origUnitCost = ($origQty > 0 && $model) 
                                            ? ($model->item_cost / $origQty) 
                                            : 0;
                            // ค่าเดิมจาก old() ถ้ามี
                            $oldAddQty = old("add_qty.$index", 0);
                            $oldAddUnitCost = old("add_unit_cost.$index", number_format($origUnitCost,2,'.',''));
                        @endphp
                        <tr class="restock-row">
                            <td class="row-index">{{ $index + 1 }}</td>

                            {{-- เลือก PurchaseItem --}}
                            <td>
                                <select name="item_id[]" class="form-select select-item" required>
                                    <option value="">-- เลือกรายการ --</option>
                                    @foreach($allItems as $itm)
                                        <option value="{{ $itm->id }}"
                                            data-quantity="{{ $itm->quantity }}"
                                            data-cost="{{ $itm->quantity > 0 ? $itm->cost / $itm->quantity : 0 }}"
                                            {{ $oldId == $itm->id ? 'selected' : '' }}>
                                            {{ $itm->name }} (บิล {{ $itm->purchase->purchase_number }})
                                        </option>
                                    @endforeach
                                </select>
                                @error("item_id.$index")
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>

                            {{-- จำนวนเดิม --}}
                            <td>
                                <span class="original-qty">{{ $origQty }}</span>
                                <input type="hidden" name="original_qty[]" class="orig-qty-hidden" value="{{ $origQty }}">
                            </td>

                            {{-- ต้นทุนเดิม/ชิ้น --}}
                            <td>
                                <span class="original-unit-cost">{{ number_format($origUnitCost, 2) }}</span>
                                <input type="hidden" name="original_unit_cost[]" class="orig-unit-cost-hidden" value="{{ number_format($origUnitCost, 2, '.', '') }}">
                            </td>

                            {{-- จำนวนที่จะเพิ่ม --}}
                            <td>
                                <input type="number" name="add_qty[]" class="form-control add-qty" 
                                       value="{{ $oldAddQty }}" min="0">
                                @error("add_qty.$index")
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>

                            {{-- ต้นทุนเพิ่ม/ชิ้น --}}
                            <td>
                                <input type="number" name="add_unit_cost[]" class="form-control add-unit-cost" 
                                       value="{{ $oldAddUnitCost }}" step="0.01" min="0">
                                @error("add_unit_cost.$index")
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>

                            {{-- จำนวนรวม --}}
                            <td>
                                <input type="text" name="total_qty[]" class="form-control total-qty text-end" 
                                       value="{{ intval($origQty) + intval($oldAddQty) }}" readonly>
                            </td>

                            {{-- ต้นทุนเฉลี่ยใหม่/ชิ้น --}}
                            <td>
                                @php
                                    $combinedTotalCost = ($origQty * $origUnitCost) + ($oldAddQty * $oldAddUnitCost);
                                    $newQty = $origQty + $oldAddQty;
                                    $avgCost = $newQty > 0 ? $combinedTotalCost / $newQty : 0;
                                @endphp
                                <input type="text" name="average_unit_cost[]" class="form-control average-unit-cost text-end" 
                                       value="{{ number_format($avgCost, 2) }}" readonly>
                            </td>

                            {{-- ปุ่มจัดการ --}}
                            <td class="text-center">
                                <button type="button" class="btn btn-secondary btn-sm copy-row">คัดลอก</button>
                                <button type="button" class="btn btn-danger btn-sm remove-row">ลบ</button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    {{-- แถวเริ่มต้น 1 แถว --}}
                    <tr class="restock-row">
                        <td class="row-index">1</td>

                        {{-- เลือก PurchaseItem --}}
                        <td>
                            <select name="item_id[]" class="form-select select-item" required>
                                <option value="">-- เลือกรายการ --</option>
                                @foreach($allItems as $itm)
                                    <option value="{{ $itm->id }}"
                                        data-quantity="{{ $itm->item_quantity }}"
                                        data-cost="{{ $itm->item_quantity > 0 ? $itm->item_cost / $itm->item_quantity : 0 }}">
                                        {{ $itm->item_name }} (บิล {{ $itm->purchase->purchase_number }})
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        {{-- จำนวนเดิม --}}
                        <td>
                            <span class="original-qty">0</span>
                            <input type="hidden" name="original_qty[]" class="orig-qty-hidden" value="0">
                        </td>

                        {{-- ต้นทุนเดิม/ชิ้น --}}
                        <td>
                            <span class="original-unit-cost">0.00</span>
                            <input type="hidden" name="original_unit_cost[]" class="orig-unit-cost-hidden" value="0.00">
                        </td>

                        {{-- จำนวนที่จะเพิ่ม --}}
                        <td>
                            <input type="number" name="add_qty[]" class="form-control add-qty" value="0" min="0">
                        </td>

                        {{-- ต้นทุนเพิ่ม/ชิ้น --}}
                        <td>
                            <input type="number" name="add_unit_cost[]" class="form-control add-unit-cost" value="0.00" step="0.01" min="0">
                        </td>

                        {{-- จำนวนรวม --}}
                        <td>
                            <input type="text" name="total_qty[]" class="form-control total-qty text-end" value="0" readonly>
                        </td>

                        {{-- ต้นทุนเฉลี่ยใหม่/ชิ้น --}}
                        <td>
                            <input type="text" name="average_unit_cost[]" class="form-control average-unit-cost text-end" value="0.00" readonly>
                        </td>

                        {{-- ปุ่มจัดการ --}}
                        <td class="text-center">
                            <button type="button" class="btn btn-secondary btn-sm copy-row" disabled>คัดลอก</button>
                            <button type="button" class="btn btn-danger btn-sm remove-row" disabled>ลบ</button>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <button type="button" id="addRowBtn" class="btn btn-primary mb-3">+ เพิ่มรายการ</button>

        <button type="submit" class="btn btn-success">บันทึกการ Restock &amp; เฉลี่ย</button>
        <a href="{{ route('purchaseitems.index') }}" class="btn btn-secondary">ย้อนกลับ</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // ฟังก์ชันอัปเดตลำดับแถวและสถานะปุ่ม
        function updateRowSettings() {
            $('#restockTable tbody tr').each(function(index) {
                $(this).find('.row-index').text(index + 1);
                if (index === 0) {
                    $(this).find('.remove-row, .copy-row').attr('disabled', true);
                } else {
                    $(this).find('.remove-row, .copy-row').attr('disabled', false);
                }
            });
        }

        // ฟังก์ชันคำนวณค่าแถวเดียว
        function recalcRow($row) {
            let origQty = parseFloat($row.find('.orig-qty-hidden').val()) || 0;
            let origCost = parseFloat($row.find('.orig-unit-cost-hidden').val()) || 0;
            let addQty = parseFloat($row.find('.add-qty').val()) || 0;
            if (addQty < 0) addQty = 0;
            let addCost = parseFloat($row.find('.add-unit-cost').val()) || 0;
            if (addCost < 0) addCost = 0;

            // คำนวณจำนวนรวม และต้นทุนรวม
            let totalQty = origQty + addQty;
            let origTotalCost = origQty * origCost;
            let addTotalCost = addQty * addCost;
            let combinedTotalCost = origTotalCost + addTotalCost;

            // ต้นทุนเฉลี่ยใหม่ต่อชิ้น
            let avgCost = 0;
            if (totalQty > 0) {
                avgCost = combinedTotalCost / totalQty;
            }

            $row.find('.total-qty').val(totalQty.toFixed(0));
            $row.find('.average-unit-cost').val(avgCost.toFixed(2));
        }

        // สร้าง handler: เมื่อเลือก PurchaseItem เปลี่ยน จะอัปเดต original fields และ recalc
        $(document).on('change', '.select-item', function() {
            let $row = $(this).closest('tr');
            let $opt = $(this).find(':selected');
            let origQty = parseFloat($opt.data('quantity')) || 0;
            let origCostPer = parseFloat($opt.data('cost')) || 0;
            $row.find('.original-qty').text(origQty);
            $row.find('.orig-qty-hidden').val(origQty);
            $row.find('.original-unit-cost').text(origCostPer.toFixed(2));
            $row.find('.orig-unit-cost-hidden').val(origCostPer.toFixed(2));
            recalcRow($row);
        });

        // เมื่อเปลี่ยน add_qty หรือ add_unit_cost ให้ recalc แถว
        $(document).on('input', '.add-qty, .add-unit-cost', function() {
            let $row = $(this).closest('tr');
            recalcRow($row);
        });

        // ปุ่มเพิ่มแถว: clone แถวแรก แล้วเคลียร์ค่า และอัปเดตลำดับ
        $('#addRowBtn').click(function() {
            let $firstRow = $('#restockTable tbody tr:first').clone();
            $firstRow.find('input, select').each(function() {
                let name = $(this).attr('name');
                if ($(this).is('select')) {
                    $(this).val('').trigger('change');
                } else {
                    $(this).val('');
                }
            });
            // เซ็ต default value ให้เป็น 0
            $firstRow.find('.original-qty').text('0');
            $firstRow.find('.orig-qty-hidden').val('0');
            $firstRow.find('.original-unit-cost').text('0.00');
            $firstRow.find('.orig-unit-cost-hidden').val('0.00');
            $firstRow.find('.total-qty').val('0');
            $firstRow.find('.average-unit-cost').val('0.00');
            $firstRow.find('.copy-row, .remove-row').attr('disabled', false);
            $('#restockTable tbody').append($firstRow);
            updateRowSettings();
        });

        // ปุ่มคัดลอกแถว: clone แถวปัจจุบัน แล้ว recalc new clone
        $(document).on('click', '.copy-row', function() {
            let $row = $(this).closest('tr');
            let $clone = $row.clone();
            // ให้ recalc ค่าใน clone ใหม่
            $clone.find('.remove-row, .copy-row').attr('disabled', false);
            $('#restockTable tbody').append($clone);
            updateRowSettings();
            recalcRow($clone);
        });

        // ปุ่มลบแถว
        $(document).on('click', '.remove-row', function() {
            if ($('#restockTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
                updateRowSettings();
            }
        });

        // เรียกครั้งแรกให้ตั้งค่า
        updateRowSettings();

        // ถ้ามี old inputs อยู่แล้ว แต่ไม่มีการคลิกใดๆ ให้ recalc ทุกแถว
        $('#restockTable tbody tr').each(function() {
            recalcRow($(this));
        });
    });
</script>
@endpush
