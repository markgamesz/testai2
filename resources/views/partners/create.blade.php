@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h1>เพิ่มผู้จำหน่าย/ผู้ขาย</h1>
        <form id="partnerForm" action="{{ route('partners.store') }}" method="POST">
            @csrf
            <div class="col-md-6">
                <!-- Dropdown เลือกประเภท Partner -->
                <div class="form-group">
                    <label for="partner_type">ประเภทผู้ขาย</label><span class="text-danger">*</span>
                    <select name="partner_type" id="partner_type" class="form-select">
                        <option value="">เลือกประเภท</option>
                        <option value="1">บุคคล</option>
                        <option value="2">บริษัท</option>
                    </select>
                </div>

                <!-- แสดงรหัส Partner (part_id) ที่จะถูกสร้างอัตโนมัติ -->
                <div class="form-group">
                    <label for="part_id">รหัสผู้ขาย(ระบบจะสร้างอัตโนมัติ)</label>
                    <input type="text" name="part_id" id="part_id" class="form-control" readonly>
                </div>

                <!-- ฟิลด์อื่นๆ สำหรับข้อมูล Partner -->
                <div class="form-group">
                    <label for="part_name">ชื่อผู้ขาย</label><span class="text-danger">*</span>
                    <input type="text" name="part_name" id="part_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="phone">เบอร์โทรศัพท์</label><span class="text-danger">*</span>
                    <input type="text" name="phone" id="phone" class="form-control" value=""
                        placeholder="เช่น 0812345678" required>
                </div>

                <div class="form-group">
                    <label for="part_detial">ที่อยู่</label><span class="text-danger">*</span>
                    <textarea name="part_detial" class="form-control" placeholder="บ้านเลขที่ ซอย หมู่ ถนน"></textarea>
                </div>
                <div class="form-group">
                    <label for="province">จังหวัด</label><span class="text-danger">*</span>
                    <select name="province" id="province" class="form-select">
                        <option value="">เลือกจังหวัด</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="district">อำเภอ/เขต</label><span class="text-danger">*</span>
                    <select name="district" id="district" class="form-select">
                        <option value="">เลือกอำเภอ/เขต</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subdistrict">ตำบล/แขวง</label><span class="text-danger">*</span>
                    <select name="subdistrict" id="subdistrict" class="form-select">
                        <option value="">เลือกตำบล/แขวง</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="zipcode">รหัสไปรษณีย์</label><span class="text-danger">*</span>
                    <select name="zipcode" id="zipcode" class="form-select">
                        <option value="">เลือกรหัสไปรษณีย์</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="part_vatnum">เลขประจำตัวผู้เสียภาษี</label><span class="text-danger">*</span>
                    <input type="text" id="part_vatnum" name="part_vatnum" class="form-control" maxlength="13"
                        placeholder="เลขที่ผู้เสียภาษี 13หลัก">
                </div>

                
                <div class="form-group">
                    <label for="part_vatstatus">เลือกสถานะVat</label><span class="text-danger">*</span>
                    <select name="part_vatstatus" class="form-select">
                        <option value="">เลือกสถานะมีVat/ไม่มีVat</option>
                        <option value="1">มีVat</option>
                        <option value="0">ไม่มี่Vat</option>
                    </select>
                </div>

                <div id="vat-error" class="text-danger" style="display:none;">
                    เลข VAT ต้องประกอบด้วยตัวเลข 13 หลักเท่านั้น
                </div>
                <button type="submit" class="btn btn-primary mt-3">บันทึก</button>
                <a href="{{ route('partners.index') }}" class="btn btn-warning mt-3">ย้อนกลับ</a>
            </div>
        </form>
    </div>

@section('js')
    <script>
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

            

            (function() {
                const $form = document.getElementById('partnerForm');
                const $vat = document.getElementById('part_vatnum');
                const $error = document.getElementById('vat-error');

                // ลบอักขระที่ไม่ใช่ตัวเลขออกทุกครั้งที่พิมพ์
                $vat.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '');
                    $error.style.display = 'none';
                });

                // ก่อนส่งฟอร์ม ให้เช็คความยาว 13 หลัก
                $form.addEventListener('submit', function(e) {
                    const v = $vat.value.trim();
                    if (v.length !== 13) {
                        e.preventDefault(); // หยุดการส่งฟอร์ม
                        $error.style.display = 'block';
                        $vat.focus();
                    }
                });
            })();


        });
    </script>
@endsection
@endsection
