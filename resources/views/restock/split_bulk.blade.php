@extends('layouts.adminbase')

@section('content')
<div class="container">
  <h1 class="mb-4">Split All Restocked Units</h1>

  <form method="POST" action="{{ route('restock.split.bulk.handle') }}">
    @csrf

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>PurchaseItem ID</th>
          <th>Sale Price</th>
          <th>Barcode</th>
        </tr>
      </thead>
      <tbody>
        @foreach($units as $i => $u)
          <tr>
            <td>
              {{ $u['purchase_item_id'] }}
              <input type="hidden"
                     name="units[{{ $i }}][purchase_item_id]"
                     value="{{ $u['purchase_item_id'] }}">
            </td>
            <td>
              <input type="text"
                     name="sale_price[{{ $i }}]"
                     class="form-control"
                     value="{{ old("sale_price.$i", $u['sale_price']) }}">
            </td>
            <td>
              <input type="text"
                     name="barcode[{{ $i }}]"
                     class="form-control"
                     value="{{ old("barcode.$i", $u['barcode']) }}">
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <button class="btn btn-success">Save All Units</button>
    <a href="{{ route('stocks.index') }}" class="btn btn-secondary">Cancel</a>
  </form>
</div>
@endsection
