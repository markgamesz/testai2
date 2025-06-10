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
        <h1 class="mb-4">รายการสต๊อก</h1>
        <table id="bootstrapdatatable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>เลขที่เอกสาร</th>
                    <th>เลขที่เอกสารอ้างอิง</th>
                    
                    <th class="text-end">จำนวนรับเข้า(ชิ้น)</th>
                    <th class="text-end">จำนวนคงเหลือ(ชิ้น)</th>
                    <th class="text-end">ราคาทุนรับเข้า(ชิ้น)</th>
                    <th class="text-end">ราคาทุนที่เหลือ(ชิ้น)</th>
                    <th class="text-end">ทำรายการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                    <tr>
                        <td>{{ $stock->purchase_document_number }}</td>
                        <td>{{ $stock->ref_id }}</td>
                        
                        <td class="text-end">
                            {{ number_format($stock->total_qty) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($stock->purchase_items_sum_quantity) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($stock->total, 2, '.', ',') }}
                        </td>
                        <td class="text-end">
                            {{ number_format($stock->items_total_price, 2, '.', ',') }}
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="actionDropdown-{{ $stock->purchase_id }}" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    ทำรายการ
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="actionDropdown-">
                                    <li><a class="dropdown-item text-success"
                                            href="{{ route('stocks.restock_from_purchase', $stock->purchase_id) }}">แปรรูป</a>
                                    </li>
                                    <li><a class="dropdown-item text-success" href="#">ข้อมูล</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">ไม่มีข้อมูล</td>
                    </tr>
                @endforelse
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
