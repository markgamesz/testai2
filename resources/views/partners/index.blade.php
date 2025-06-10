@extends('layouts.adminbase')


@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.0/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" />
@endsection

@section('content')
    <div class="container">
        <h1>รายการผู้จำหน่าย/ผู้ขาย</h1>
        <a href="{{ route('partners.create') }}" class="btn btn-primary">เพิ่มผู้จำหน่าย/ผู้ขายใหม่</a> <br><br>
        <table class="table table-bordered mt-3" id="bootstrapdatatable">
            <thead>
                <tr>
                    <th>รหัสผู้ขาย</th>
                    <th>ชื่อผู้ขาย</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>ที่อยู่</th>
                    <th>เลขที่ผู้เสียภาษีผู้ขาย</th>
                    <th>สถานะVat</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($partners as $partner)
                    <tr>
                        <td>{{ $partner->part_id }}</td>
                        <td>{{ $partner->part_name }}</td>
                        <td>{{ $partner->phone }}</td>
                        <td>{{ "$partner->part_detial $partner->subdistrict $partner->district $partner->province $partner->zipcode" }}
                        </td>
                        <td>{{ $partner->part_vatnum }}</td>
                        <td>{{ $partner->part_vatstatus ? 'มีVat' : 'ไม่มี่Vat' }}</td>
                        <td>
                            <a href="{{ route('partners.edit', $partner->part_id) }}"
                                class="btn btn-sm btn-warning">แก้ไข</a>
                           
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@section('js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#bootstrapdatatable').DataTable({
                responsive: true,
                select: true,
                dom: 'Blfrtip',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    ['10', '25', '50', '100', 'all']
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel" aria-hidden="true"> Export a EXCEL</i>'
                    },
                    'pageLength'
                ],
            });
        });
    </script>
@endsection

@endsection
