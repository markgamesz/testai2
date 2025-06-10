@extends('layouts.adminbase')

@section('content')
    <div class="container">
        <h1>เพิ่มรายการค่าใช้จ่าย</h1>
        <form action="{{ route('sub_expenss.store') }}" method="POST">
            @csrf
            <div class="col-md-6">


                <!-- Dropdown เลือกประเภท Partner -->
                <div class="form-group">
                    <label for="expen_sub_type">ประเภทค่าใช้จ่าย</label><span class="text-danger">*</span>
                    <select name="expen_sub_type" id="expen_sub_type" class="form-select" required>
                        <option value="">เลือกประเภท</option>
                        <option value="0">บุคคล</option>
                        <option value="1">บริษัท</option>
                    </select>
                </div>


                <!-- แสดงรหัส Partner (expen_sub_id_gen) ที่จะถูกสร้างอัตโนมัติ -->
                <div class="form-group">
                    <label for="expen_sub_id_gen">รหัสค่าใช้จ่าย(ระบบจะสร้างอัตโนมัติ)</label>
                    <input type="text" name="expen_sub_id_gen" id="expen_sub_id_gen" class="form-control" readonly>
                </div>

                <!-- ฟิลด์อื่นๆ สำหรับข้อมูล Partner -->
                <div class="form-group">
                    <label for="expen_sub_name">ชื่อผู้ขาย</label><span class="text-danger">*</span>
                    <input type="text" name="expen_sub_name" id="expen_sub_name" class="form-control" required>
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
                    <label for="part_vatstatus">เลือกสถานะVat</label><span class="text-danger">*</span>
                    <select name="part_vatstatus" class="form-select">
                        <option value="">เลือกสถานะมีVat/ไม่มีVat</option>
                        <option value="1">มีVat</option>
                        <option value="0">ไม่มี่Vat</option>
                    </select>
                </div>


                <button type="submit" class="btn btn-primary mt-3">Submit</button>





            </div>

        </form>
    </div>
@endsection
