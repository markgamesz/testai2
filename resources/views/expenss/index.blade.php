@extends('layouts.adminbase')

@section('content')
<div class="container">
    <h1>ค่าใช้จ่าย</h1>
    <a href="{{ route('expenss.create') }}" class="btn btn-primary">เพิ่มค่าใช้จ่ายใหม่</a>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>เลขที่ค่าใช้จ่าย</th>
                <th>expen_name</th>
                <th>insert_date</th>
                <th>buy_date</th>
                <th>expen_cost</th>
                <th>expen_vat</th>
                <th>expen_total</th>
                <th>remark</th>
                <th>expen_sub_id</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
           
        </tbody>
    </table>
</div>
@endsection
