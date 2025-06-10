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
        <h1>รายการรับซื้อ</h1>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">เพิ่มรายการรับเข้า</a>
        <br><br><br>
        <table id="bootstrapdatatable" class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>เลขที่เอกสาร</th>

                    <th>รหัสลูกค้า</th>
                    <th>วันที่ทำรายการ</th>
                    <th>วันที่ซื้อ</th>

                    <th>เลขอ้างอิง</th>
                    <th>จำนวนชิ้น</th>

                    <th>ต้นทุนVat</th>
                    <th>ต้นทุนสุทธิ</th>
                    <th>สถานะ</th>
                    <th>ยกเลิกโดย</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->purchase_document_number }}</td>

                        <td>{{ $purchase->partner_id }}</td>
                        <td>{{ $purchase->insert_date }}</td>
                        <td>{{ $purchase->buy_date }}</td>

                        <td>{{ $purchase->ref_id }}</td>
                        <td>{{ $purchase->total_qty }}</td>

                        <td>{{ $purchase->vat_amount }}</td>
                        <td>{{ number_format($purchase->total, 2, '.', ',') }}</td>
                        <td>{{ $purchase->status ? 'ใช้งาน' : 'ยกเลิกบิล' }}</td>
                        <td>{{ optional($purchase->deleter)->name }}</td>
                        <!--   <td>
                                <a href="{{ route('purchases.edit', $purchase->purchase_id) }}"
                                    class="btn btn-sm btn-warning">แก้ไข</a>
                                <form action="{{ route('purchases.destroy', $purchase->purchase_id) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('ต้องการยกเลิกรายการหรือไม่')">ยกเลิกรายการ</button>
                                </form>
                            </td>-->
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="actionDropdown-{{ $purchase->purchase_id }}" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    ทำรายการ
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="actionDropdown-">


                                    <li><a class="dropdown-item text-success"
                                            href="{{ route('purchases.edit', $purchase->purchase_id) }}">แก้ไข</a></li>
                                    <li>
                                        <form action="{{ route('purchases.destroy', $purchase->purchase_id) }}"
                                            method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger"
                                                onclick="return confirm('ต้องการยกเลิกรายการหรือไม่')">ยกเลิกรายการ</button>
                                        </form>
                                    </li>

                                </ul>
                            </div>


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
