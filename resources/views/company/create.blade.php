@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h1>ข้อมูลบริษัท</h1>
        <form id="partnerForm" action="{{ route('company.store') }}" method="POST">
            @csrf

            <div class="col-md-6">
                <div class="form-group">
                    <label for="com_name">ชื่อบริษัท</label><span class="text-danger">*</span>
                    <input type="text" name="com_name" class="form-control" placeholder="เช่น บริษัท เอบีซี จำกัด"
                        required>
                </div>

                <div class="form-group">
                    <label for="phone">เบอร์โทรศัพท์</label>ุ<span class="text-danger">*</span>
                    <input type="text" name="phone" id="phone" class="form-control" value=""
                        placeholder="เช่น 0812345678" required>
                </div>



                <div class="form-group">
                    <label for="com_detial">ที่อยู่</label><span class="text-danger">*</span>
                    <textarea name="com_detial" class="form-control" placeholder="บ้านเลขที่ ซอย หมู่ ถนน" required></textarea>
                </div>
                <div class="form-group">
                    <label for="province">จังหวัด</label><span class="text-danger">*</span>
                    <select name="province" id="province" class="form-select" required>
                        <option value="">เลือกจังหวัด</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="district">อำเภอ/เขต</label><span class="text-danger">*</span>
                    <select name="district" id="district" class="form-select" required>
                        <option value="">เลือกอำเภอ/เขต</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subdistrict">ตำบล/แขวง</label><span class="text-danger">*</span>
                    <select name="subdistrict" id="subdistrict" class="form-select" required>
                        <option value="">เลือกตำบล/แขวง</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="zipcode">รหัสไปรษณีย์</label><span class="text-danger">*</span>
                    <select name="zipcode" id="zipcode" class="form-select" required>
                        <option value="">เลือกรหัสไปรษณีย์</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="com_vatnum">เลขประจำตัวผู้เสียภาษี</label><span class="text-danger">*</span>
                    <input  type="text" id="com_vatnum" name="com_vatnum" class="form-control" maxlength="13"
                        placeholder="เลขที่ผู้เสียภาษี 13หลัก" required>
                </div>

                <div id="vat-error" class="text-danger" style="display:none;">
                    เลข VAT ต้องประกอบด้วยตัวเลข 13 หลักเท่านั้น
                </div>


                <button type="submit" class="btn btn-primary mt-3">บันทึก</button>
                <a href="{{ route('company.index') }}" class="btn btn-warning mt-3">ย้อนกลับ</a>
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

                $('#com_vatnum').on('input', function() {
                    // ลบอักขระที่ไม่ใช่ตัวเลขออก
                    this.value = this.value.replace(/\D/g, '');
                });

               
            });
            (function() {
                const $form = document.getElementById('partnerForm');
                const $vat = document.getElementById('com_vatnum');
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

        document.addEventListener('DOMContentLoaded', function() {
            var phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                // เอาแต่ตัวเลขออกมา
                var digits = this.value.replace(/\D/g, '');
                // ตัดให้ไม่เกิน 10 หลัก
                if (digits.length > 10) digits = digits.slice(0, 10);

                // ใส่ dash ตามตำแหน่ง
                var part1 = digits.slice(0, 3);
                var part2 = digits.length > 3 ? digits.slice(3, 6) : '';
                var part3 = digits.length > 6 ? digits.slice(6) : '';

                var formatted = part1;
                if (part2) formatted += '-' + part2;
                if (part3) formatted += '-' + part3;

                this.value = formatted;
            });
        });
    </script>
@endsection
@endsection
