@extends('layouts.adminbase')

@section('content')
<div class="container">
    <h1>Edit Sub Expenss</h1>
    <form action="{{ route('sub_expenss.update', $subExpenss->expen_sub_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="expen_sub_name">Sub Expenss Name</label>
            <input type="text" name="expen_sub_name" class="form-control" value="{{ $subExpenss->expen_sub_name }}" required>
        </div>
        <div class="form-group">
            <label for="expen_sub_type">Sub Expenss Type</label>
            <input type="text" name="expen_sub_type" class="form-control" value="{{ $subExpenss->expen_sub_type }}" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Update</button>
    </form>
</div>
@endsection
