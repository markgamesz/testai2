{{-- resources/views/stocks/index.blade.php --}}
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
        <h1 class="mb-4">รายการสต๊อกแปรรูป</h1>

        {{-- แสดงข้อความสำเร็จถ้ามี --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table id="" class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Document No.</th>
                    <th>ชื่อสินค้า</th>
                    
                    <th>รายละเอียด 1</th>
                    <th>รายละเอียด 2</th>
                    <th class="text-end">จำนวนคงเหลือ</th>
                    <th class="text-end">ต้นทุนเฉลี่ย(หน่วย) (฿)</th>
                    <th class="text-end">มูลค่าคงเหลือ (฿)</th>
                    
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        {{-- ดึงเลขที่เอกสารจาก PurchaseItem → Purchase --}}
                        <td>
                            {{ optional($stock->purchaseItem->purchase)->purchase_document_numbe }}
                        </td>

                        {{-- ชื่อสินค้า จาก PurchaseItem --}}
                        <td>{{ optional($stock->product)->product_name }}</td>
                        <td>{{ optional($stock->product)->product_detail1 }}</td>
                        <td>{{ optional($stock->product)->product_detail2 }}</td>
                        {{-- จำนวนคงเหลือใน Stock --}}
                        <td class="text-end">{{ number_format($stock->quantity) }}</td>

                        {{-- ต้นทุนเฉลี่ย ต่อหน่วย --}}
                        <td class="text-end">{{ number_format($stock->avg_cost, 2) }}</td>

                        {{-- มูลค่าคงเหลือ (quantity × avg_cost) --}}
                        <td class="text-end">{{ number_format($stock->quantity * $stock->avg_cost, 2) }}</td>

                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">ยังไม่มี Stock</td>
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
