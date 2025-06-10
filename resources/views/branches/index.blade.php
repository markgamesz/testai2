@extends('layouts.adminbase')

@section('content')
<div class="container">
    <h1>รายการ สาขา</h1>
    <a href="{{ route('branches.create') }}" class="btn btn-primary">เพิ่ม สาขา ใหม่</a>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>รหัสสาขา</th>
                <th>ชื่อสาขา</th>
                <th>เบอร์โทรศัพท์</th>
                <th>ที่อยู่</th>
                <th>สถานะ</th>
                <th>แก้ไขโดย</th>
                <th>จัดการสาขา</th>
            </tr>
        </thead>
        <tbody>
            @foreach($branches as $branch)
            <tr>
                <td>{{ $branch->branch_id }}</td>
                <td>{{ $branch->branch_name }}</td>
                <td>{{ $branch->phone }}</td>
                <td>{{ "$branch->branch_address $branch->subdistrict $branch->district $branch->province $branch->zipcode" }}</td>
                <td>{{ $branch->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}</td>
                <td>{{ $branch->updater->name }}</td>
                <td>
                    <a href="{{ route('branches.edit', $branch->branch_id) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                    
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
