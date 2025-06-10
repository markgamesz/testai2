@extends('layouts.adminbase')

@section('content')
<div class="container">
  <h1 class="mb-4">
    Restock สินค้า: 
    <span class="text-primary">{{ $item->product_name }}</span>
  </h1>

  {{-- แสดงยอดคงเหลือก่อน Restock --}}
  <p>คงเหลือในบิล (PurchaseItem): 
    <strong>{{ number_format($item->quantity) }}</strong>
  </p>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('restock.restock.handle', $item) }}" method="POST">
    @csrf

    <div class="mb-3">
      <label for="restock_qty" class="form-label">จำนวนที่จะ Restock</label>
      <input
        type="number"
        id="restock_qty"
        name="restock_qty"
        class="form-control @error('restock_qty') is-invalid @enderror"
        value="{{ old('restock_qty', 1) }}"
        min="1"
        max="{{ $item->quantity }}"
        required
      >
      @error('restock_qty')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary">บันทึก</button>
    <a href="{{ route('purchases.stock') }}" class="btn btn-secondary">ยกเลิก</a>
  </form>
</div>
@endsection
