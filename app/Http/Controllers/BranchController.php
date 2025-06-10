<?php

namespace App\Http\Controllers;



use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class BranchController extends Controller
{
    // แสดงรายการ Branch ทั้งหมด
    public function index()
    {
        $branches = Branch::all();
        return view('branches.index', compact('branches'));
    }

    // แสดงฟอร์มสำหรับสร้าง Branch ใหม่
    public function create()
    {
        return view('branches.create');
    }

    // บันทึกข้อมูล Branch ใหม่
    public function store(Request $request)
    {
        $company = Company::all();
        $request->validate([

            'branch_name'    => 'required|string|max:50',

        ]);
        // เพิ่ม updated_by
        $request['updated_by'] = Auth::id();
        foreach($company as $companies){
            $request['com_id'] = $companies->com_id;
        }
        
        Branch::create($request->all());

        return redirect()->route('branches.index')
            ->with('success', 'บันทึกสาขาสำเร็จ');
    }

    // แสดงฟอร์มสำหรับแก้ไข Branch
    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    // อัปเดตข้อมูล Branch ที่แก้ไขแล้ว
    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'branch_name'    => 'required|string|max:50',
            'is_active'   => 'required|boolean',

        ]);

        // เพิ่ม updated_by
        $request['updated_by'] = Auth::id();

        $branch->update($request->all());

        return redirect()->route('branches.index')
            ->with('success', 'แก้ไขสำเร็จ');
    }

    // ลบข้อมูล Branch
    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->route('branches.index')
            ->with('success', 'Branch deleted successfully.');
    }
}
