@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h1>แก้ไขข้อมูลสาขา</h1>
        <form action="{{ route('branches.update', $branch->branch_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <div class="form-group">
                    <label for="branch_id">รหัสสาขา</label><span class="text-danger">*</span>
                    <!-- แสดง branch_id แบบ readonly -->
                    <input type="text" name="branch_id" id="branch_id" class="form-control" maxlength="5"
                        value="{{ $branch->branch_id }}" required>
                </div>
                <div class="form-group">
                    <label for="branch_name">ชื่อสาขา</label><span class="text-danger">*</span>
                    <input type="text" name="branch_name" id="branch_name" class="form-control"
                        value="{{ $branch->branch_name }}" required>
                </div>
                <div class="form-group">
                    <label for="phone">เบอร์โทรศัพท์</label><span class="text-danger">*</span>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ $branch->phone }}"
                        placeholder="เช่น 081-234-5678" required>
                </div>
                <div class="form-group">
                    <label for="branch_address">ที่อยู่</label><span class="text-danger">*</span>
                    <textarea name="branch_address" class="form-control" placeholder="บ้านเลขที่ ซอย หมู่ ถนน" required>{{ $branch->branch_address }}</textarea>
                </div>
                <div class="form-group">
                    <label for="province">จังหวัด</label><span class="text-danger">*</span>
                    <select name="province" id="province" class="form-select" required>
                        <option value="{{ $branch->province }}">เลือกจังหวัด</option>
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
                    <label for="is_active">สถานะ</label>
                    <select name="is_active" id="is_active" class="form-select" required>
                        <option value="1" {{ old('is_active', $branch->is_active ?? true) ? 'selected' : '' }}>
                            เปิดใช้งาน
                        </option>
                        <option value="0" {{ old('is_active', $branch->is_active ?? true) ? '' : 'selected' }}>
                            ปิดใช้งาน
                        </option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mt-3">บันทึก</button>
                <a href="{{ route('branches.index') }}" class="btn btn-warning mt-3">ย้อนกลับ</a>
            </div>

            

        </form>
    </div>

@section('js')
    <script>
        $(document).ready(function() {
            // กำหนดค่าที่มีอยู่ (จากฐานข้อมูล)
            var selectedProvince = "{{ $branch->province }}";
            var selectedDistrict = "{{ $branch->district }}";
            var selectedSubdistrict = "{{ $branch->subdistrict }}";
            var selectedZipcode = "{{ $branch->zipcode }}";

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
        });
    </script>
@endsection
@endsection
