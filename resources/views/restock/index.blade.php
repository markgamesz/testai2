{{-- resources/views/stocks/index.blade.php --}}
@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h1 class="mb-4">Stock รายการสินค้า</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Document No.</th>
                    <th>ชื่อสินค้า</th>
                    <th class="text-end">จำนวนคงเหลือ</th>
                    <th class="text-end">ต้นทุนเฉลี่ย/หน่วย (฿)</th>
                    <th class="text-end">มูลค่าคงเหลือ (฿)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        {{-- ดึงเลขที่เอกสารจาก PurchaseItem → Purchase --}}
                        <td>
                            {{ optional(optional($stock->product)->purchase)->purchase_document_number }}
                        </td>

                        {{-- ชื่อสินค้า จาก PurchaseItem --}}
                        <td>{{ optional($stock->product)->product_name }}</td>

                        {{-- จำนวนคงเหลือ ในตาราง stocks --}}
                        <td class="text-end">{{ number_format($stock->quantity) }}</td>

                        {{-- ต้นทุนเฉลี่ย/หน่วย --}}
                        <td class="text-end">{{ number_format($stock->avg_cost, 2) }}</td>

                        {{-- มูลค่าคงเหลือ (quantity × avg_cost) --}}
                        <td class="text-end">{{ number_format($stock->quantity * $stock->avg_cost, 2) }}</td>

                        <td>
                            <a href="{{ route('purchases.restock.form', optional($stock->product)->purchase_id) }}"
                                class="btn btn-sm btn-primary">
                                Restock
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">ยังไม่มี Stock</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
