@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h1 class="mb-4">
            Restock จากบิล:
            <span class="text-primary">{{ $purchase->purchase_document_number }}</span>
        </h1>

        {{-- แสดงข้อความสำเร็จ --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('purchases.restock.store', $purchase) }}" method="POST">
            @csrf

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>สินค้า</th>
                        <th class="text-end">ราคาต่อชิ้น (฿)</th>
                        <th class="text-end">คงเหลือในบิล</th>
                        <th class="text-end">จำนวนที่จะ Restock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->purchaseItems as $item)
                        <tr>
                            {{-- ชื่อสินค้า --}}
                            <td>{{ $item->product_name }}</td>

                            {{-- ราคาต่อชิ้น --}}
                            <td class="text-end">{{ number_format($item->price, 2) }}</td>

                            {{-- จำนวนคงเหลือ --}}
                            <td class="text-end">{{ number_format($item->quantity) }}</td>

                            {{-- input จำนวนที่จะ Restock --}}
                            <td class="text-end">
                                <input type="number" name="restock_qty[{{ $item->id }}]"
                                    class="form-control text-end @error("restock_qty.{$item->id}") is-invalid @enderror"
                                    value="{{ old("restock_qty.{$item->id}", 0) }}" min="0"
                                    max="{{ $item->quantity }}">
                                @error("restock_qty.{$item->id}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary">บันทึก</button>
            <a href="#" class="btn btn-secondary">กลับ</a>
        </form>
    </div>
@endsection
