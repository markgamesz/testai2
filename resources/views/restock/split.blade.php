@extends('layouts.adminbase')
@section('content')
<div class="container">
  <h1>Split Restock #{{ $restock->id }}</h1>
  <form method="POST"
        action="{{ route('restock.split.handle',$restock) }}">
    @csrf
    <table class="table table-bordered">
      <thead>
       <tr>
         <th>Sale Price</th>
         <th>Barcode</th>
       </tr>
      </thead>
      <tbody>
      @foreach($units as $i=>$u)
        <tr>
          <td>
            <input type="text"
                   name="sale_price[{{ $i }}]"
                   class="form-control"
                   value="{{ old("sale_price.$i",$u['sale_price']) }}">
          </td>
          <td>
            <input type="text"
                   name="barcode[{{ $i }}]"
                   class="form-control"
                   value="{{ old("barcode.$i",$u['barcode']) }}">
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
    <button class="btn btn-success">Save Units</button>
  </form>
</div>
@endsection
