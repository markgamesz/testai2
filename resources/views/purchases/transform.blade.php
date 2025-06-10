@extends('layouts.adminbase')

@section('content')
<div class="container">
  <h1 class="mb-4">แปรรูปจากบิล {{ $purchase->purchase_document_number }}</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form method="POST"
        action="{{ route('purchases.transform.store', $purchase->purchase_id) }}">
    @csrf
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>สินค้า</th>
          <th class="text-end">คงเหลือ</th>
          <th class="text-end">จำนวนที่จะแปรรูป</th>
          <th class="text-end">ราคาขาย/หน่วย</th>
        </tr>
      </thead>
      <tbody>
        @foreach($purchase->purchaseItems as $item)
          <tr>
            <td>{{ $item->product_name }}</td>
            <td class="text-end">{{ $item->quantity }}</td>
            <td>
              <input type="number"
                     name="qty[{{ $item->id }}]"
                     class="form-control text-end @error("qty.{$item->id}") is-invalid @enderror"
                     min="0" max="{{ $item->quantity }}"
                     value="{{ old("qty.{$item->id}") }}">
              @error("qty.{$item->id}")
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </td>
            <td>
              <input type="text"
                     name="sale_price[{{ $item->id }}]"
                     class="form-control text-end @error("sale_price.{$item->id}") is-invalid @enderror"
                     value="{{ old("sale_price.{$item->id}") }}">
              @error("sale_price.{$item->id}")
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <button type="submit" class="btn btn-primary">บันทึกการแปรรูป</button>
    <a href="{{ route('purchases.stockSummary') }}" class="btn btn-secondary">กลับ</a>
  </form>
</div>
@endsection
