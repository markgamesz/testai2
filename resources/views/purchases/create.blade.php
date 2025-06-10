@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h1>เพิ่มรายการรับเข้า</h1>
        <form action="{{ route('purchases.store') }}" method="POST">
            @csrf

            <div class="row">


                {{-- เลือก บริษัท --}}
                <!-- <div class="col-md-6 form-group">
                                                                                        <label for="purchase_type">เลือกบริษัท/บุคคล</label>
                                                                                        <select name="purchase_type" id="purchase_type" class="form-select" required>
                                                                                            <option value="">-- เลือกบริษัท --</option>
                                                                                            <option value="0">บุคคล</option>
                                                                                            <option value="1">บริษัท</option>

                                                                                        </select>
                                                                                    </div>-->
                {{-- เลือก Partner --}}
                <div class="col-md-6 form-group">
                    <label for="partner_id">เลือกผู้ขาย</label>
                    <select name="partner_id" id="partner_id" class="form-select" required>
                        <option value="">-- เลือกผู้ขาย --</option>
                        @foreach ($partners as $p)
                            <option value="{{ $p->part_id }}"data-type="{{ $p->partner_type - 1 }}">{{ $p->part_name }}
                            </option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                        data-bs-target="#partnerModal">ค้นหา</button>
                    <!--- <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                                                data-bs-target="#partnerModal1">เพิ่มผู้ขาย</button> -->
                    <a href="{{ route('partners.create') }}" target="_blank" class="btn btn-primary">เพิ่มผู้ขาย</a>
                    <button type="button" id="btn-refresh-partners" class="btn btn-outline-secondary"
                        title="รีเฟรชรายชื่อ">
                        <i class="bi-arrow-clockwise"></i> <!-- Bootstrap Icon -->
                    </button>


                </div>


                {{-- เลขที่เอกสาร --}}
                <div class="col-md-6 form-group">
                    <label for="purchase_document_number">เลขที่เอกสาร</label>
                    <input type="text" name="purchase_document_number" id="purchase_document_number" class="form-control"
                        readonly>
                </div>

                {{-- เลือกสาขา --}}
                <div class="col-md-6 form-group">
                    <label for="branch_id">เลือกคลังรับสินค้า</label>
                    <select name="branch_id" id="branch_id" class="form-select" required>
                        <option value="">-- เลือกคลังรับสินค้า --</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->branch_id }}" data-code="{{ $branch->branch_id }}">
                                {{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- รหัสสาขา --}}
                <div class="col-md-6 form-group">
                    <label for="branch_code">รหัสสาขา</label>
                    <input type="text" name="branch_code" id="branch_code" class="form-control" readonly>
                </div>

                {{-- วันที่บันทึกข้อมูล --}}
                <div class="col-md-6 form-group">
                    <label for="insert_date">วันที่บันทึกข้อมูล</label>
                    <input type="date" name="insert_date" id="insert_date" class="form-control"
                        value="{{ date('Y-m-d') }}" required>
                </div>

                {{-- วันที่ซื้อ --}}
                <div class="col-md-6 form-group">
                    <label for="buy_date">วันที่ซื้อ</label>
                    <input type="date" name="buy_date" id="buy_date" class="form-control" value="{{ date('Y-m-d') }}"
                        required>
                </div>

                {{-- เลขที่เอกสารอ้างอิง --}}
                <div class="col-md-6 form-group">
                    <label for="ref_id">เลขที่เอกสารอ้างอิง</label>
                    <input type="text" name="ref_id" id="ref_id" class="form-control">
                </div>







                {{-- ที่อยู่ Partner --}}
                <div class="col-md-12 form-group">
                    <label for="partner_address">ที่อยู่ผู้ขาย</label>
                    <textarea name="partner_address" id="partner_address" class="form-control" rows="2" readonly></textarea>
                </div>
            </div>


            {{-- Dynamic Products --}}
            <h3>รายการสินค้า</h3>
            <table class="table" id="products_table">
                <thead>
                    <tr>
                        <th>ชื่อสินค้า</th>
                        <th>รายละเอียด1</th>
                        <th>รายละเอียด2</th>
                        <th>จำนวน(ชิ้น)</th>
                        <th>ราคาก่อนVat(ชิ้น)</th>
                        <th>ราคารวมก่อนVat</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="product-row">
                        <td><input type="text" name="product_name[]" class="form-control" required></td>
                        <td><input type="text" name="product_detail1[]" class="form-control"></td>
                        <td><input type="text" name="product_detail2[]" class="form-control"></td>
                        <td><input type="text" name="product_qty[]" class="form-control" required>
                        </td>
                        <td><input type="text" name="product_price[]" class="form-control" required>
                        </td>
                        <td><input type="text" name="product_total[]" class="form-control" required>
                        </td>

                        <td>
                            <div class="btn-group" role="group" aria-label="Action Buttons">
                                <button type="button" class="btn btn-secondary copy-row">คัดลอก</button>
                                <button type="button" class="btn btn-danger remove-row">ลบ</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" id="add_product" class="btn btn-primary">เพิ่มรายการ</button>

            {{-- Vat & Total --}}
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="form-group ">
                        <label for="payment_type">ประเภทชำระเงิน</label>
                        <select class="form-select" id="payment_type" name="payment_type">
                            <option value="Cash" {{ old('payment_type') == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Credit" {{ old('payment_type') == 'Credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 form-group">
                    <label for="sum_qty">จำนวนสินค้าทั้งหมด(ชิ้น)</label>
                    <input type="text" name="sum_qty" id="sum_qty" class="form-control" readonly>
                </div>
                <div class="col-md-2 form-group">
                    <label for="total">ยอดรวมก่อนVat</label>
                    <input type="text" name="total" id="total" class="form-control" readonly>
                </div>
                <div class="col-md-2 form-group">
                    <label for="vat_amount">Vat(7%)</label>
                    <input type="text" name="vat_amount" id="vat_amount" class="form-control" readonly>
                </div>
                <div class="col-md-2 form-group">
                    <label for="grand_total">ยอดรวม</label>
                    <input type="text" name="grand_total" id="grand_total" class="form-control" readonly>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">บันทึก</button>
            <a href="{{ route('purchases.index') }}" class="btn btn-warning mt-3">ย้อนกลับ</a>
        </form>
    </div>


    <!-- Partner Search Modal (Bootstrap 5) -->
    <div class="modal fade" id="partnerModal" tabindex="-1" aria-labelledby="partnerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="partnerModalLabel">ค้นหา</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Tab: ค้นหา Partner -->
                    <div class="tab-pane fade show active" id="tab-search">
                        <div class="input-group mb-2">
                            <select id="searchCategory" class="form-select">
                                <option value="part_name">ชื่อ</option>
                                <option value="phone">เบอร์โทร</option>
                                <option value="part_vatnum">เลข VAT</option>
                            </select>
                            <input type="text" id="partner_search" class="form-control"
                                placeholder="พิมพ์ข้อความค้นหา...">
                        </div>
                        <ul id="partner_results" class="list-group"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Partner Modal -->
    <div class="modal fade" id="partnerModal1" tabindex="-1" aria-labelledby="partnerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="partnerModalLabel">เพิ่มผู้ขาย</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="partnerCreateForm">
                        @csrf

                        <div class="mb-3">
                            <label for="partner_type">ประเภท</label>
                            <select name="partner_type" id="partner_type" class="form-select" required>
                                <option value="">เลือกประเภท</option>
                                <option value="1">บุคคล</option>
                                <option value="2">บริษัท</option>
                            </select>
                        </div>
                        <!-- แสดงรหัส Partner (part_id) ที่จะถูกสร้างอัตโนมัติ -->
                        <div class="mb-3">
                            <label for="part_id">รหัสผู้ขาย(ระบบจะสร้างอัตโนมัติ)</label>
                            <input type="text" name="part_id" id="part_id" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="part_name">ชื่อผู้ขาย</label>
                            <input type="text" name="part_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone">เบอร์โทรศัพท์</label><span class="text-danger">*</span>
                            <input type="text" name="phone" id="phone" class="form-control" value=""
                                placeholder="เช่น 0812345678" required>
                        </div>
                        <div class="mb-3">
                            <label for="part_detial">ที่อยู่</label>
                            <input type="text" name="part_detial" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="province">จังหวัด</label><span class="text-danger">*</span>
                            <select name="province" id="province" class="form-select">
                                <option value="">เลือกจังหวัด</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="district">อำเภอ/เขต</label><span class="text-danger">*</span>
                            <select name="district" id="district" class="form-select">
                                <option value="">เลือกอำเภอ/เขต</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="subdistrict">ตำบล/แขวง</label><span class="text-danger">*</span>
                            <select name="subdistrict" id="subdistrict" class="form-select">
                                <option value="">เลือกตำบล/แขวง</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="zipcode">รหัสไปรษณีย์</label><span class="text-danger">*</span>
                            <select name="zipcode" id="zipcode" class="form-select">
                                <option value="">เลือกรหัสไปรษณีย์</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="part_vatnum">เลขประจำตัวผู้เสียภาษี</label><span class="text-danger">*</span>
                            <input type="text" name="part_vatnum" class="form-control" maxlength="13"
                                placeholder="เลขที่ผู้เสียภาษี 13หลัก">
                        </div>
                        <div class="mb-3">
                            <label for="part_vatstatus">เลือกสถานะVat</label><span class="text-danger">*</span>
                            <select name="part_vatstatus" class="form-select">
                                <option value="">เลือกสถานะมีVat/ไม่มีVat</option>
                                <option value="1">มีVat</option>
                                <option value="0">ไม่มี่Vat</option>
                            </select>
                        </div>

                        <button type="button" id="savePartnerBtn" class="btn btn-primary">บันทึก</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



@section('js')
    <script>
        $(function() {

            // เวลาโฟกัส ลบ comma ออก
            $(document).on('focus',
                'input[name="product_price[]"],input[name="product_total[]"], #total, #vat_amount, #grand_total',
                function() {
                    this.value = this.value.replace(/,/g, '');
                }
            );

            // ตอน blur เติม comma ให้สวย
            $(document).on('blur',
                'input[name="product_price[]"],input[name="product_total[]"], #total, #vat_amount, #grand_total',
                function() {
                    if (!this.value) return;
                    let n = parseFloat(this.value) || 0;
                    this.value = n.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            );

            // ฟังก์ชันช่วย format ตัวเลขให้มี comma และ 2 ทศนิยม
            function fmt(v) {
                let n = parseFloat(v) || 0;
                return n.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Add / Remove product rows
            $('#add_product').click(function() {
                var row = $('.product-row:first').clone();
                row.find('input').val('');
                $('#products_table tbody').append(row);
            });

            $(document).on('click', '.copy-row', function() {
                var $tr = $(this).closest('tr');
                var $clone = $tr.clone();

                // (ถ้าต้องการ reset บางฟิลด์ ให้ทำที่นี่ เช่น)
                // $clone.find('input[name^="product_price"]').val('');

                // ต่อท้าย tbody
                $('#products_table tbody').append($clone);

                // ถ้ามีการคำนวณยอด เช่น calculateTotals()
                calculateTotals3();
            });




            $(document).on('click', '.remove-row', function() {
                if ($('#products_table tbody tr').length > 1) $(this).closest('tr').remove();
                calculateTotals3();
            });

            // คำนวณ line total เมื่อ qty/price เปลี่ยน (ยังคง oninput ไว้เพื่อคำนวณ)
            $(document).on('input',
                'input[name="product_qty[]"], input[name="product_price[]"]',
                function() {
                    let $row = $(this).closest('tr');
                    let qty = parseFloat($row.find('input[name="product_qty[]"]').val().replace(/,/g, '')) || 0;
                    let price = parseFloat($row.find('input[name="product_price[]"]').val().replace(/,/g,
                        '')) || 0;
                    // คำนวณ line total
                    let line = qty * price;
                    $row.find('input[name="product_total[]"]').val(fmt(line));
                    calculateTotals3(); // ฟังก์ชันคำนวณรวม (ไม่เปลี่ยน)
                }
            );

            // เมื่อเปลี่ยนจำนวน หรือ ราคา ให้คำนวณใหม่
            $(document).on('input', 'input[name^="product_qty"],input[name^="product_price"]', function() {
                calculateTotals();
            });

            // ฟังก์ชันคำนวณยอดรวมทั้งตาราง (รวม line totals ทั้งหมด)
            function calculateTotals2() {
                var subtotal = 0;
                $('input[name="product_total[]"]').each(function() {
                    subtotal += parseFloat($(this).val()) || 0;
                });

            }

            // ฟังก์ชันคำนวณยอดรวม ยอดรวมก่อนVat ยอดรวม
            function calculateTotals3() {
                let subtotal = 0;
                let totalQty = 0;
                $('#products_table tbody tr').each(function() {

                    // ดึง qty และ total ของแต่ละแถว
                    let qty = parseFloat(
                        $(this).find('input[name="product_qty[]"]')
                        .val().replace(/,/g, '')
                    ) || 0;
                    let line = parseFloat(
                        $(this).find('input[name="product_total[]"]').val().replace(/,/g, '')
                    ) || 0;

                    totalQty  += qty;
                    subtotal += line;
                });

                // Vat 7% กรณี partner มี Vat
                let vatStatus = +$('#partner_id option:selected').data('type') || 0;
                let vat = vatStatus === 1 ? subtotal * 0.07 : 0;


                $('#sum_qty').val(totalQty.toLocaleString('en-US'));
                // เติม comma ให้ทุกฟิลด์
                $('#total').val(fmt(subtotal));
                $('#vat_amount').val(fmt(vat));
                $('#grand_total').val(fmt(subtotal + vat));
            }

            // เรียกครั้งแรกถ้ามีข้อมูลเริ่มต้น
            calculateTotals3();


            // Branch change
            $('#branch_id').change(function() {
                $('#branch_code').val($(this).find(':selected').data('code') || '');
            });

            function genDocumentNumber(partnerType) {

                if (partnerType === 0 || partnerType === 1) {
                    $.ajax({
                        url: '{{ route('purchases.nextDocumentNumber') }}',
                        type: 'GET',
                        data: {
                            purchase_type: partnerType
                        },
                        success: function(response) {
                            $('#purchase_document_number').val(response.document_number);
                        },
                        error: function(xhr) {
                            console.error('Error generating document number:', xhr.responseText);
                            $('#purchase_document_number').val('');
                        }
                    });
                } else {
                    // ถ้า type นอกเหนือ 0 หรือ 1 ให้เคลียร์
                    $('#purchase_document_number').val('');
                }
            }
            $('#partner_id').change(function() {
                // ดึง data-type แล้วแปลงเป็น integer
                var partnerType = parseInt($(this).find('option:selected').data('type'), 10);
                var partnerId = $(this).val();

                genDocumentNumber(partnerType);
                if (partnerId) {
                    $.ajax({
                        url: '{{ route('partners.details', '') }}' + '/' + partnerId,
                        type: 'GET',
                        success: function(response) {
                            // รวมข้อมูลที่อยู่จาก response
                            // สมมุติว่า response มีส่วนของ part_detial, subdistrict, district, province และ zipcode
                            var fullAddress = '';
                            if (response.part_detial) {
                                fullAddress += response.part_detial;
                            }
                            if (response.subdistrict) {
                                fullAddress += ' แขวง' + response.subdistrict;
                            }
                            if (response.district) {
                                fullAddress += ' อำเภอ' + response.district;
                            }
                            if (response.province) {
                                fullAddress += ' จังหวัด' + response.province;
                            }
                            if (response.zipcode) {
                                fullAddress += ' ' + response.zipcode;
                            }
                            $('#partner_address').val(fullAddress);
                        },
                        error: function(xhr) {
                            console.error("Error:", xhr.responseText);
                            $('#partner_address').val('');
                        }
                    });
                } else {
                    $('#partner_address').val('');
                }
                calculateTotals3();
            });

            // เมื่อพิมพ์ค้นหาใน modal
            $('#partner_search').on('input', function() {
                var q = $(this).val().trim();
                var category = $('#searchCategory').val(); // 'part_name', 'phone' หรือ 'part_vatnum'
                if (q.length < 2) {
                    $('#partner_results').empty();
                    return;
                }
                // ภายใน success callback ของการค้นหา
                $.getJSON("{{ route('partners.search') }}", {
                    q: q,
                    field: category
                }, function(data) {
                    var $ul = $('#partner_results').empty();
                    data.forEach(function(item) {
                        //var partnerType = parseInt(item.partner_type);
                        var label = item.part_name +
                            ' — ' + (item.phone || '-') +
                            ' — ' + (item.part_vatnum || '-') +
                            ' — ' + (item.part_detial || '-');

                        //genDocumentNumber(partnerType);
                        $('<li>')
                            .addClass('list-group-item list-group-item-action')
                            .data('id', item.part_id)
                            .data('name', item.part_name) // เก็บชื่อ
                            .data('address', item.part_detial) // เก็บที่อยู่
                            .data('province', item.province)
                            .data('district', item.district)
                            .data('subdistrict', item.subdistrict)
                            .data('zipcode', item.zipcode)
                            .data('partner_type', item.partner_type)
                            .text(label)
                            .appendTo($ul);
                    });
                });

            });

            // เมื่อคลิกเลือกจากผลลัพธ์
            $('#partner_results').on('click', 'li', function() {
                var id = $(this).data('id'),
                    name = $(this).data('name'), // ดึงชื่อ
                    address = $(this).data('address'),
                    province = $(this).data('province'),
                    district = $(this).data('district'),
                    subdistrict = $(this).data('subdistrict'),
                    zipcode = $(this).data('zipcode'),
                    partner_type = $(this).data('partner_type'),
                    partnerType = partner_type - 1;

                //var partnerType = parseInt(item.partner_type);

                // เติมลงในฟิลด์ของฟอร์มหลัก
                $('#partner_id').val(id);
                $('#partner_name').val(name); // เติมชื่อพาร์ทเนอร์
                $('#partner_address,#full_address').val(
                    `${address} ${subdistrict} ${district} ${province} ${zipcode}`);




                genDocumentNumber(partnerType);


                // ปิด modal
                $('#partnerModal').modal('hide');
            });

            // Save new Partner via AJAX
            $('#savePartnerBtn').click(function() {
                // 1. เก็บค่าจากฟอร์ม
                const type = $('#partnerCreateForm select[name="partner_type"]').val();
                const partId = $('#partnerCreateForm input[name="part_id"]').val();
                const name = $('#partnerCreateForm input[name="part_name"]').val();
                const phone = $('#partnerCreateForm input[name="phone"]').val();
                const detail = $('#partnerCreateForm input[name="part_detial"]').val();
                const province = $('#partnerCreateForm select[name="province"]').val();
                const district = $('#partnerCreateForm select[name="district"]').val();
                const subdistrict = $('#partnerCreateForm select[name="subdistrict"]').val();
                const zipcode = $('#partnerCreateForm select[name="zipcode"]').val();
                const vatnum = $('#partnerCreateForm input[name="part_vatnum"]').val();
                const vatstatus = $('#partnerCreateForm select[name="part_vatstatus"]').val();

                // 2. สร้าง object ส่ง AJAX (ไม่ต้อง serialize ก็ได้)
                const data = {
                    partner_type: type,
                    part_id: partId,
                    part_name: name,
                    phone: phone,
                    part_detial: detail,
                    province: province,
                    district: district,
                    subdistrict: subdistrict,
                    zipcode: zipcode,
                    part_vatnum: vatnum,
                    part_vatstatus: vatstatus,
                    _token: '{{ csrf_token() }}'
                };

                // 3. ส่งไปสร้างใน DB
                $.post("{{ route('partners.storeAjax') }}", data)
                    .done(function(res) {
                        // 4. เพิ่ม <option> ใหม่ พร้อม data-attr เก็บข้อมูลครบ
                        $('#partner_id')
                            .append(`
                                <option value="${res.part_id}"
                                data-type="${res.partner_type}"
                                data-phone="${res.phone}"
                                data-address="${res.part_detial}"
                                data-province="${res.province}"
                                data-district="${res.district}"
                                data-subdistrict="${res.subdistrict}"
                                data-zipcode="${res.zipcode}"
                                data-vatnum="${res.part_vatnum}"
                                data-vatstatus="${res.part_vatstatus}"
                                selected>
                                ${res.part_name}
                                </option>
                            `)
                            .val(res.part_id);

                        // แสดงที่อยู่หรือเบอร์โทรฯ ในฟิลด์ที่เตรียมไว้
                        $('#partner_address').val(res.part_detial);
                        $('#partner_phone').val(res.phone);

                        // 5. ปิด modal
                        $('#partnerModal1').modal('hide');
                    })
                    .fail(function(err) {
                        alert('บันทึก partner ไม่สำเร็จ: ' + err.responseText);
                    });
            });
        });


        $(document).ready(function() {
            // โหลดข้อมูลจากไฟล์ JSON thai_address.json ใน public/json/
            $.getJSON("/json/thai_address.json", function(data) {
                var provinceSelect = $('#province');

                // เติมข้อมูล dropdown จังหวัด โดยใช้ค่าเป็น string ชื่อจังหวัด
                $.each(data, function(index, province) {
                    provinceSelect.append(
                        $('<option>', {
                            value: province.name_th, // ใช้ชื่อจังหวัดเป็น value
                            text: province.name_th
                        })
                    );
                });

                // เมื่อเลือกจังหวัด ให้เติม dropdown เขต (amphure) โดยใช้ชื่อเขตเป็น value
                provinceSelect.change(function() {
                    var selectedProvinceName = $(this).val();
                    // ค้นหาข้อมูลจังหวัดที่เลือกจาก array data
                    var selectedProvince = data.find(function(item) {
                        return item.name_th === selectedProvinceName;
                    });
                    var districtSelect = $('#district');
                    // ล้างข้อมูล dropdown เขต, แขวง และ รหัสไปรษณีย์
                    districtSelect.empty().append('<option value="">เลือกเขต</option>');
                    $('#subdistrict').empty().append('<option value="">เลือกแขวง</option>');
                    $('#zipcode').empty().append('<option value="">เลือกรหัสไปรษณีย์</option>');

                    if (selectedProvince && selectedProvince.amphure) {
                        $.each(selectedProvince.amphure, function(index, amphure) {
                            districtSelect.append(
                                $('<option>', {
                                    value: amphure.name_th, // ใช้ชื่อเขตเป็น value
                                    text: amphure.name_th
                                })
                            );
                        });
                    }
                });

                // เมื่อเลือกเขต ให้เติม dropdown แขวง (tambon) โดยใช้ชื่อแขวงเป็น value
                $('#district').change(function() {
                    var selectedProvinceName = $('#province').val();
                    var selectedDistrictName = $(this).val();
                    var selectedProvince = data.find(function(item) {
                        return item.name_th === selectedProvinceName;
                    });
                    var selectedDistrict = null;
                    if (selectedProvince && selectedProvince.amphure) {
                        selectedDistrict = selectedProvince.amphure.find(function(item) {
                            return item.name_th === selectedDistrictName;
                        });
                    }
                    var subdistrictSelect = $('#subdistrict');
                    subdistrictSelect.empty().append('<option value="">เลือกแขวง</option>');
                    $('#zipcode').empty().append('<option value="">เลือกรหัสไปรษณีย์</option>');

                    if (selectedDistrict && selectedDistrict.tambon) {
                        $.each(selectedDistrict.tambon, function(index, tambon) {
                            subdistrictSelect.append(
                                $('<option>', {
                                    value: tambon.name_th, // ใช้ชื่อแขวงเป็น value
                                    text: tambon.name_th,
                                    "data-zip": tambon
                                        .zip_code // เก็บรหัสไปรษณีย์ใน data attribute
                                })
                            );
                        });
                    }
                });

                // เมื่อเลือกแขวง ให้แสดงรหัสไปรษณีย์ใน dropdown รหัสไปรษณีย์
                $('#subdistrict').change(function() {
                    var selectedZip = $(this).find(':selected').data('zip');
                    var zipcodeSelect = $('#zipcode');
                    zipcodeSelect.empty();
                    if (selectedZip) {
                        zipcodeSelect.append(
                            $('<option>', {
                                value: selectedZip,
                                text: selectedZip
                            })
                        );
                    }
                });
            });

            $('#partner_type').change(function() {
                var prefix = $(this).val();
                console.log("Partner Type Selected:", prefix);
                if (prefix) {
                    $.ajax({
                        url: '{{ route('partners.nextId') }}',
                        type: 'GET',
                        data: {
                            partner_type: prefix
                        },
                        success: function(response) {
                            console.log("Response from server:", response);
                            $('#part_id').val(response.next_id);
                        },
                        error: function(xhr) {
                            console.log("AJAX Error:", xhr.responseText);
                        }
                    });
                } else {
                    $('#part_id').val('');
                }
            });

            $('#btn-refresh-partners').on('click', function() {
                var $btn = $(this).prop('disabled', true);
                var $select = $('#partner_id');

                $.getJSON("{{ route('partners.list') }}")
                    .always(function() {
                        $btn.prop('disabled', false);
                    })
                    .done(function(list) {
                        // เคลียร์ตัวเลือกเดิม
                        $select.empty()
                            .append('<option value="">-- เลือกผู้ขาย --</option>');

                        // เติมจากข้อมูลใหม่
                        list.forEach(function(p) {
                            $select.append(
                                $('<option>', {
                                    value: p.part_id,
                                    'data-type': p.partner_type - 1,
                                    text: p.part_name
                                })
                            );
                        });
                    })
                    .fail(function() {
                        alert('ไม่สามารถโหลดรายชื่อผู้ขายได้ กรุณาลองใหม่');
                    });
            });


        });
    </script>
@endsection
@endsection
