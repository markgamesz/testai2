{{-- resources/views/purchase_items/index.blade.php --}}
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
        <h3 class="mb-4">รายการสินค้า</h3>

        {{-- แสดงข้อความสำเร็จหรือข้อผิดพลาด --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table id="bootstrapdatatable" class="table table-striped table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>ลำดับ</th>
                    <th>ชื่อสินค้า</th>
                    <th>รายละเอียด1</th>
                    <th>รายละเอียด2</th>
                    <th>เลขเอกสาร</th>
                    <th>จำนวน</th>
                    <th>ราคาต่อชิ้น</th>
                    <th>วันที่สร้าง</th>
                    <th>ทำรายการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseItems as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        {{-- ชื่อสินค้า --}}
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->product_detail1 }}</td>
                        <td>{{ $item->product_detail2 }}</td>

                        {{-- เลขเอกสาร Purchase (purchase_document_number) --}}
                        <td>
                            {{-- ถ้า Relation ชื่อ purchase และ field ใน Purchase คือ purchase_number --}}
                            @if ($item->purchase)
                                {{ $item->purchase->purchase_document_number }}
                            @else
                                —
                            @endif
                        </td>

                        {{-- จำนวนสินค้า (item_quantity) --}}
                        <td>{{ number_format($item->quantity) }}</td>

                        {{-- ราคาต่อชิ้น --}}
                        @php
                            // ถ้ามีฟิลด์ sale_price ในตาราง PurchaseItem
                            $unitPrice = $item->price ?? null;

                            // ถ้าไม่มี sale_price แต่มีต้นทุนรวม (item_cost) กับจำนวน (item_quantity)
                            if (is_null($unitPrice) && $item->item_quantity > 0) {
                                $unitPrice = round($item->item_cost / $item->item_quantity, 2);
                            }
                        @endphp
                        <td>{{ $unitPrice !== null ? number_format($unitPrice, 2) : '-' }}</td>

                        {{-- วันที่สร้าง (optional) --}}
                        <td>{{ $item->created_at->format('Y-m-d') }}</td>
                        <td><a href="{{ route('stocks.process', ['purchase_item_id' => $item->id]) }}"
                                class="btn btn-sm btn-success">
                                แปรรูป
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">ยังไม่มีรายการ Purchase Item</td>
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
