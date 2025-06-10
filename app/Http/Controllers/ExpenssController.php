<?php

namespace App\Http\Controllers;

use App\Models\Expenss;
use Illuminate\Http\Request;

class ExpenssController extends Controller
{
    // แสดงรายการ Expenss ทั้งหมด
    public function index()
    {
        //$expenss = Expenss::all();
        return view('expenss.index');
    }

    // แสดงฟอร์มสำหรับสร้าง Expenss ใหม่
    public function create()
    {
        return view('expenss.create');
    }

    // บันทึกข้อมูล Expenss ใหม่
    public function store(Request $request)
    {
        $request->validate([
            'expen_name'    => 'required|string|max:255',
            'expen_sub_id'  => 'required|exists:sub_expenss,expen_sub_id',
            // สามารถเพิ่ม validation สำหรับ field อื่นๆ ได้ตามต้องการ
        ]);

        Expenss::create($request->all());

        return redirect()->route('expenss.index')
            ->with('success', 'Expenss created successfully.');
    }

    // แสดงฟอร์มสำหรับแก้ไขข้อมูล Expenss
    public function edit(Expenss $expenss)
    {
        return view('expenss.edit', compact('expenss'));
    }

    // อัปเดตข้อมูล Expenss ที่แก้ไขแล้ว
    public function update(Request $request, Expenss $expenss)
    {
        $request->validate([
            'expen_name'    => 'required|string|max:255',
            'expen_sub_id'  => 'required|exists:sub_expenss,expen_sub_id',
        ]);

        $expenss->update($request->all());

        return redirect()->route('expenss.index')
            ->with('success', 'Expenss updated successfully.');
    }

    // ลบข้อมูล Expenss
    public function destroy(Expenss $expenss)
    {
        $expenss->delete();

        return redirect()->route('expenss.index')
            ->with('success', 'Expenss deleted successfully.');
    }
}
