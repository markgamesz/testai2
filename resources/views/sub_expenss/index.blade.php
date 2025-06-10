@extends('layouts.adminbase')

@section('content')
<div class="container">
    <h1>Sub Expenss</h1>
    <a href="{{ route('sub_expenss.create') }}" class="btn btn-primary">Add New Sub Expenss</a>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>expen_sub_id</th>
                <th>expen_sub_name</th>
                <th>expen_sub_type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subExpenss as $item)
            <tr>
                <td>{{ $item->expen_sub_id }}</td>
                <td>{{ $item->expen_sub_name }}</td>
                <td>{{ $item->expen_sub_type }}</td>
                <td>
                    <a href="{{ route('sub_expenss.edit', $item->expen_sub_id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('sub_expenss.destroy', $item->expen_sub_id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
