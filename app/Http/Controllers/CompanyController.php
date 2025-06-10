<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    // แสดงรายการข้อมูลทั้งหมด
    public function index()
    {
        $company = Company::all();
        return view('company.index', compact('company'));
    }

    // แสดงฟอร์มสำหรับสร้างข้อมูลใหม่
    public function create()
    {
        return view('company.create');
    }

    // บันทึกข้อมูลใหม่
    public function store(Request $request)
    {
        
        $request->validate([
            'com_name'   => 'required|string|max:255',
            
            // สามารถเพิ่ม validation สำหรับ field อื่น ๆ ได้ตามความเหมาะสม
        ]);

        

        Company::create($request->all());

        return redirect()->route('company.index')
            ->with('success', 'บันทึกสำเร็จ');
    }

    // แสดงฟอร์มสำหรับแก้ไขข้อมูล
    public function edit(Company $company)
    {
        return view('company.edit', compact('company'));
    }

    // อัปเดตข้อมูลที่แก้ไข
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'com_name'   => 'required|string|max:255',
            // เพิ่ม validation field อื่น ๆ ตามต้องการ
        ]);
        // เพิ่ม updated_by
        $request['updated_by'] = Auth::id();
        $company->update($request->all());

        return redirect()->route('company.index')
            ->with('success', 'แก้ไขสำเร็จ');
    }

    // ลบข้อมูล
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('company.index')
            ->with('success', 'ลบสำเร็จ');
    }
}
