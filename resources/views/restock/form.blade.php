@extends('layouts.adminbase')
@section('content')
<div class="container">
  <h1>Restock: {{ $item->product_name }}</h1>
  <form method="POST"
        action="{{ route('restock.handle',$item) }}">
    @csrf
    <div class="mb-3">
      <label>จำนวน restock</label>
      <input type="number" name="restock_qty"
             class="form-control"
             value="{{ old('restock_qty',1) }}"
             min="1" max="{{ $item->quantity }}">
      @error('restock_qty')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label>ราคารวมที่จ่าย</label>
      <input type="text" name="restock_total"
             class="form-control"
             value="{{ old('restock_total', number_format($item->quantity*$item->price,2,'.','')) }}">
      @error('restock_total')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label>จำนวนหน่วยที่จะ split</label>
      <input type="number" name="split_count"
             class="form-control"
             value="{{ old('split_count', $item->quantity) }}"
             min="1">
      @error('split_count')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <button class="btn btn-primary">บันทึก</button>
  </form>
</div>
@endsection
