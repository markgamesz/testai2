@extends('layouts.adminbase')

@section('content')
<div class="container">
  <h1 class="mb-4">ย่อยสินค้า: {{ $purchase->purchase_document_number }}</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('purchases.consume', $purchase->purchase_id) }}" method="POST">
    @csrf
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>สินค้า</th>
          <th class="text-end">สินค้าคงเหลือในบิล</th>
          <th class="text-end">ราคาคงเหลือในบิล</th>
          <th class="text-end">จำนวนที่จะย่อย</th>
          <th class="text-end">ราคาต่อหน่วยที่จะย่อย</th>
        </tr>
      </thead>
      <tbody>
      @foreach($purchase->purchaseItems as $item)
        <tr>
          <td>{{ $item->name }}</td>
          <td class="text-end">{{ $item->quantity }}</td>
          <td class="text-end">{{ $item->price }}</td>

          <td>
            <input type="number"
                   name="consume_qty[{{ $item->id }}]"
                   class="form-control text-end @error("consume_qty.$item->id") is-invalid @enderror"
                   value="{{ old("consume_qty.$item->id") }}"
                   max="{{ $item->quantity }}"
                   min="0">
            @error("consume_qty.$item->id")
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </td>

          <td>
            <input type="text"
                   name="consume_price[{{ $item->id }}]"
                   class="form-control text-end @error("consume_price.$item->id") is-invalid @enderror"
                   value="{{ old("consume_price.$item->id") }}">
            @error("consume_price.$item->id")
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>

    <button type="submit" class="btn btn-primary">ยืนยันการย่อย</button>
    <a href="{{ route('purchases.stockSummary') }}" class="btn btn-secondary">กลับ</a>
  </form>
</div>
@endsection
