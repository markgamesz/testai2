@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h1>ข้อมูลบริษัท</h1>
        <form id="partnerForm" action="{{ route('company.update', $company->com_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <div class="form-group">
                    <label for="com_name">ชื่อบริษัท</label><span class="text-danger">*</span>
                    <input type="text" name="com_name" class="form-control" value="{{ $company->com_name }}" placeholder="เช่น บริษัท เอบีซี จำกัด" required>
                </div>
                <div class="form-group">
                    <label for="phone">เบอร์โทรศัพท์</label><span class="text-danger">*</span>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ $company->phone }}"
                        placeholder="เช่น 0812345678" required>
                </div>

                <div class="form-group">
                    <label for="com_detial">ที่อยู่</label><span class="text-danger">*</span>
                    <textarea name="com_detial" class="form-control" placeholder="บ้านเลขที่ ซอย หมู่ ถนน" required>{{ $company->com_detial }}</textarea>
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
                    <input id="com_vatnum" type="text" name="com_vatnum" class="form-control" value="{{ $company->com_vatnum }}"
                        maxlength="13" placeholder="เลขที่ผู้เสียภาษี 13หลัก" required>
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
            // กำหนดค่าที่มีอยู่ (จากฐานข้อมูล)
            var selectedProvince = "{{ $company->province }}";
            var selectedDistrict = "{{ $company->district }}";
            var selectedSubdistrict = "{{ $company->subdistrict }}";
            var selectedZipcode = "{{ $company->zipcode }}";

            // ตัวแปรสำหรับเก็บข้อมูล JSON
            var addressData = [];

            // โหลดข้อมูลจากไฟล์ JSON thai_address.json ครั้งเดียว
            $.getJSON("/json/thai_address.json", function(data) {
                addressData = data; // เก็บข้อมูลไว้ในตัวแปร addressData

                var provinceSelect = $('#province');
                provinceSelect.empty();
                provinceSelect.append('<option value="">เลือกจังหวัด</option>');

                // เติมข้อมูล dropdown จังหวัด โดยใช้ชื่อจังหวัดเป็น value
                $.each(addressData, function(index, province) {
                    provinceSelect.append(
                        $('<option>', {
                            value: province.name_th,
                            text: province.name_th
                        })
                    );
                });

                // ตั้งค่า pre-selected จังหวัดและ trigger change
                if (selectedProvince) {
                    provinceSelect.val(selectedProvince).trigger('change');
                }
            });

            // เมื่อเลือกจังหวัด ให้เติม dropdown เขต
            $('#province').change(function() {
                var selectedProvinceName = $(this).val();
                var selectedProvinceData = addressData.find(function(item) {
                    return item.name_th === selectedProvinceName;
                });
                var districtSelect = $('#district');
                districtSelect.empty().append('<option value="">เลือกเขต</option>');
                $('#subdistrict').empty().append('<option value="">เลือกแขวง</option>');
                $('#zipcode').empty().append('<option value="">เลือกรหัสไปรษณีย์</option>');

                if (selectedProvinceData && selectedProvinceData.amphure) {
                    $.each(selectedProvinceData.amphure, function(index, amphure) {
                        districtSelect.append(
                            $('<option>', {
                                value: amphure.name_th, // ใช้ชื่อเขตเป็น value
                                text: amphure.name_th
                            })
                        );
                    });
                }
                // ตั้งค่า pre-selected เขตถ้ามี
                if (selectedDistrict) {
                    districtSelect.val(selectedDistrict).trigger('change');
                }
            });

            // เมื่อเลือกเขต ให้เติม dropdown แขวง
            $('#district').change(function() {
                var selectedProvinceName = $('#province').val();
                var selectedDistrictName = $(this).val();
                var selectedProvinceData = addressData.find(function(item) {
                    return item.name_th === selectedProvinceName;
                });
                var selectedDistrictData = null;
                if (selectedProvinceData && selectedProvinceData.amphure) {
                    selectedDistrictData = selectedProvinceData.amphure.find(function(item) {
                        return item.name_th === selectedDistrictName;
                    });
                }
                var subdistrictSelect = $('#subdistrict');
                subdistrictSelect.empty().append('<option value="">เลือกแขวง</option>');
                $('#zipcode').empty().append('<option value="">เลือกรหัสไปรษณีย์</option>');

                if (selectedDistrictData && selectedDistrictData.tambon) {
                    $.each(selectedDistrictData.tambon, function(index, tambon) {
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
                // ตั้งค่า pre-selected แขวงถ้ามี
                if (selectedSubdistrict) {
                    subdistrictSelect.val(selectedSubdistrict).trigger('change');
                }
            });

            // เมื่อเลือกแขวง ให้เติม dropdown รหัสไปรษณีย์
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
                    // ตั้งค่า pre-selected รหัสไปรษณีย์ถ้ามี
                    if (selectedZipcode) {
                        zipcodeSelect.val(selectedZipcode);
                    }
                }
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
    </script>
@endsection


@endsection
