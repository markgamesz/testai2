@extends('layouts.adminbase')

@section('content')
<div class="container">
    <h1>Edit Expenss</h1>
    <form action="{{ route('expenss.update', $expenss->expen_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="expen_name">Expenss Name</label>
            <input type="text" name="expen_name" class="form-control" value="{{ $expenss->expen_name }}" required>
        </div>
        <div class="form-group">
            <label for="insert_date">Insert Date</label>
            <input type="datetime-local" name="insert_date" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($expenss->insert_date)) }}">
        </div>
        <div class="form-group">
            <label for="buy_date">Buy Date</label>
            <input type="date" name="buy_date" class="form-control" value="{{ $expenss->buy_date }}">
        </div>
        <div class="form-group">
            <label for="expen_cost">Expenss Cost</label>
            <input type="number" step="0.01" name="expen_cost" class="form-control" value="{{ $expenss->expen_cost }}">
        </div>
        <div class="form-group">
            <label for="expen_vat">Expenss VAT</label>
            <input type="number" step="0.01" name="expen_vat" class="form-control" value="{{ $expenss->expen_vat }}">
        </div>
        <div class="form-group">
            <label for="expen_total">Expenss Total</label>
            <input type="number" step="0.01" name="expen_total" class="form-control" value="{{ $expenss->expen_total }}">
        </div>
        <div class="form-group">
            <label for="remark">Remark</label>
            <textarea name="remark" class="form-control">{{ $expenss->remark }}</textarea>
        </div>
        <div class="form-group">
            <label for="expen_sub_id">Sub Expenss ID</label>
            <input type="number" name="expen_sub_id" class="form-control" value="{{ $expenss->expen_sub_id }}" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Update</button>
    </form>
</div>
@endsection
