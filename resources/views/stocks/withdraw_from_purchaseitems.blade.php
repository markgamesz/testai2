{{-- resources/views/stocks/withdraw_from_purchaseitems.blade.php --}}
@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h3 class="mb-4">ถอนสินค้า (Withdraw) จาก Purchase Items</h3>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('stocks.store_withdraw') }}" method="POST" id="withdrawForm">
            @csrf

            <table class="table table-bordered align-middle" id="withdrawTable">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px">#</th>
                        <th>เลือกรายการ (PurchaseItem)</th>
                        <th>คงเหลือ</th>
                        <th>จำนวนที่จะถอน</th>
                        <th style="width:80px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- old inputs --}}
                    @if (old('purchase_item_id'))
                        @foreach (old('purchase_item_id') as $i => $oldId)
                            @php
                                $itm = \App\Models\PurchaseItem::find($oldId);
                                $avail = $itm ? $itm->quantity - ($itm->quantity ?? 0) : 0;
                                $oldQty = old("withdraw_qty.$i", 0);
                            @endphp
                            <tr class="withdraw-row">
                                <td class="row-index">{{ $i + 1 }}</td>
                                {{-- select --}}
                                <td>
                                    <select name="purchase_item_id[]" class="form-select select-item" required>
                                        <option value="">-- เลือกรายการ --</option>
                                        @foreach ($purchaseItems as $pi)
                                            @php
                                                $remain = $pi->quantity;
                                            @endphp
                                            <option value="{{ $pi->id }}" data-remain="{{ $remain }}"
                                                {{ $oldId == $pi->id ? 'selected' : '' }}>
                                                {{ $pi->product_name }} (บิล {{ $pi->purchase->purchase_document_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("purchase_item_id.$i")
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                    <input type="hidden" name="original_qty[]" class="orig-qty-hidden"
                                        value="{{ $avail }}">
                                </td>
                                {{-- available --}}
                                <td>
                                    <span class="available-qty">{{ $avail }}</span>
                                </td>
                                {{-- withdraw qty --}}
                                <td>
                                    <input type="number" name="withdraw_qty[]" class="form-control withdraw-qty"
                                        value="{{ $oldQty }}" min="0" max="{{ $avail }}" required>
                                    @error("withdraw_qty.$i")
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </td>
                                {{-- actions --}}
                                <td class="text-center">
                                    <button type="button" class="btn btn-secondary btn-sm copy-row">คัดลอก</button>
                                    <button type="button" class="btn btn-danger btn-sm remove-row">ลบ</button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        {{-- initial row --}}
                        <tr class="withdraw-row">
                            <td class="row-index">1</td>
                            <td>
                                <select name="purchase_item_id[]" class="form-select select-item" required>
                                    <option value="">-- เลือกรายการ --</option>
                                    @foreach ($purchaseItems as $pi)
                                        @php
                                            $remain = $pi->item_quantity - ($pi->used_quantity ?? 0);
                                        @endphp
                                        <option value="{{ $pi->id }}" data-remain="{{ $remain }}">
                                            {{ $pi->item_name }} (บิล {{ $pi->purchase->purchase_number }})
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="original_qty[]" class="orig-qty-hidden" value="0">
                            </td>
                            <td><span class="available-qty">0</span></td>
                            <td>
                                <input type="number" name="withdraw_qty[]" class="form-control withdraw-qty" value="0"
                                    min="0" required>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-secondary btn-sm copy-row" disabled>คัดลอก</button>
                                <button type="button" class="btn btn-danger btn-sm remove-row" disabled>ลบ</button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <button type="button" id="addRowBtn" class="btn btn-primary mb-3">+ เพิ่มรายการ</button>

            <button type="submit" class="btn btn-success">บันทึกการถอน</button>
            <a href="{{ route('stocks.index') }}" class="btn btn-secondary">ย้อนกลับ</a>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function updateRows() {
                $('#withdrawTable tbody tr').each(function(idx) {
                    $(this).find('.row-index').text(idx + 1);
                    if (idx === 0) {
                        $(this).find('.remove-row, .copy-row').attr('disabled', true);
                    } else {
                        $(this).find('.remove-row, .copy-row').attr('disabled', false);
                    }
                });
            }

            // on select change: update available and original hidden, and cap withdraw input
            $(document).on('change', '.select-item', function() {
                let $tr = $(this).closest('tr'),
                    remain = parseInt($('option:selected', this).data('remain')) || 0;
                $tr.find('.available-qty').text(remain);
                $tr.find('.orig-qty-hidden').val(remain);
                let $w = $tr.find('.withdraw-qty');
                $w.attr('max', remain);
                if (parseInt($w.val()) > remain) $w.val(remain);
            });

            // on withdraw input: ensure ≤ max and ≥0
            $(document).on('input', '.withdraw-qty', function() {
                let mx = parseInt($(this).attr('max')) || 0,
                    v = parseInt($(this).val()) || 0;
                if (v < 0) v = 0;
                if (v > mx) v = mx;
                $(this).val(v);
            });

            // add row
            $('#addRowBtn').click(function() {
                let $clone = $('#withdrawTable tbody tr:first').clone();
                $clone.find('select').val('').trigger('change');
                $clone.find('input.withdraw-qty').val(0);
                $clone.find('input.orig-qty-hidden').val(0);
                $clone.find('.available-qty').text(0);
                $clone.find('.remove-row, .copy-row').attr('disabled', false);
                $('#withdrawTable tbody').append($clone);
                updateRows();
            });

            // copy row
            $(document).on('click', '.copy-row', function() {
                let $tr = $(this).closest('tr'),
                    $clone = $tr.clone();
                $clone.find('.remove-row, .copy-row').attr('disabled', false);
                $('#withdrawTable tbody').append($clone);
                updateRows();
            });

            // remove row
            $(document).on('click', '.remove-row', function() {
                if ($('#withdrawTable tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                    updateRows();
                }
            });

            updateRows();
            // trigger initial select change if old inputs exist
            $('#withdrawTable tbody tr').each(function() {
                $(this).find('.select-item').trigger('change');
            });
        });
    </script>
@endpush
