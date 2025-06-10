@extends('layouts.adminbase')

@section('content')
<div class="container">
    <h1>ข้อมูลบริษัท</h1>
    @if(count($company)==0)
    <a href="{{ route('company.create') }}" class="btn btn-primary">เพิ่มบริษัท</a>
    @endif
    
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                
                <th>ชื่อบริษัท</th>
                <th>เบอร์โทรศัพท์</th>
                <th>ที่อยู่</th>
                <th>เลขผู้เสียภาษี</th>
                
                <th>แก้ไข</th>
            </tr>
        </thead>
        <tbody>
            @foreach($company as $companies)
            <tr>
                
                <td>{{ $companies->com_name }}</td>
                <td>{{ $companies->phone }}</td>
                <td>{{ "$companies->com_detial $companies->subdistrict $companies->district $companies->province $companies->zipcode" }}</td>
                <td>{{ $companies->com_vatnum }}</td>
                
                <td>
                    <a href="{{ route('company.edit', $companies->com_id) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                <!--    <form action="{{ route('company.destroy', $companies->com_id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>-->
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
