<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    // แสดงรายการ Partner ทั้งหมด
    public function index()
    {
        $partners = Partner::all();
        return view('partners.index', compact('partners'));
    }

    // แสดงฟอร์มสำหรับสร้าง Partner ใหม่
    public function create()
    {
        return view('partners.create');
    }

    // บันทึกข้อมูล Partner ใหม่
    public function store(Request $request)
    {
        $request->validate([
            'part_name' => 'required|string|max:255',
            // สามารถเพิ่ม validation สำหรับฟิลด์อื่น ๆ ได้ตามต้องการ
        ]);

        Partner::create($request->all());

        return redirect()->route('partners.index')
            ->with('success', 'บันทึกสำเร็จ');
    }

    // แสดงฟอร์มสำหรับแก้ไขข้อมูล Partner
    public function edit(Partner $partner)
    {
        return view('partners.edit', compact('partner'));
    }

    // อัปเดตข้อมูล Partner ที่แก้ไขแล้ว
    public function update(Request $request, Partner $partner)
    {
        $request->validate([
            'part_name' => 'required|string|max:255',
            // เพิ่ม validation สำหรับฟิลด์อื่น ๆ ตามต้องการ
        ]);

        $partner->update($request->all());

        return redirect()->route('partners.index')
            ->with('success', 'บันทึกสำเร็จ');
    }

    // ลบข้อมูล Partner
    public function destroy(Partner $partner)
    {
        $partner->delete();

        return redirect()->route('partners.index')
            ->with('success', 'ลบสำเร็จ');
    }

    public function nextId(Request $request)
    {
        try {
            $type = $request->input('partner_type');
            // ตรวจสอบว่าค่า partner_type เป็น '1' หรือ '2'
            if (! in_array($type, ['1', '2'], true)) {
                throw new \InvalidArgumentException("Invalid partner_type: {$type}");
            }

            // ค้นหา Partner ล่าสุดที่มี part_id เริ่มต้นด้วย prefix ที่เลือก
            $max = Partner::where('part_id', 'like', $type . '%')->max(Partner::raw('CAST(part_id AS UNSIGNED)'));

            if ($max) {
                // แปลงค่า part_id เป็น integer แล้วเพิ่มทีละ 1
                $next = $max + 1;
            } else {
                // กรณียังไม่มีข้อมูล ให้ใช้ค่าเริ่มต้น

                $next = (int)($type . '0000001');
            }

            return response()->json(['next_id' => $next]);
        } catch (\Exception $e) {
            // บันทึก error ลง log สำหรับตรวจสอบภายหลัง
            \Log::error('Error in nextId: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Method details สำหรับ AJAX
    public function details($id)
    {
        $partner = Partner::find($id);
        if (!$partner) {
            return response()->json(['error' => 'Partner not found'], 404);
        }
        // เลือกส่งเฉพาะ fields ที่ต้องการแสดงข้อมูล address
        return response()->json([
            'part_detial'  => $partner->part_detial,
            'subdistrict'  => $partner->subdistrict,
            'district'     => $partner->district,
            'province'     => $partner->province,
            'zipcode'      => $partner->zipcode,
        ]);
    }

    public function search(Request $request)
    {
        $q     = $request->input('q');
        $field = $request->input('field', 'part_name'); // default

        // ปลอดภัย: ตรวจสอบให้ field เป็นหนึ่งใน 3 ค่า
        if (! in_array($field, ['part_name', 'phone', 'part_vatnum'])) {
            $field = 'part_name';
        }

        $results = Partner::where($field, 'like', "%{$q}%")
            ->limit(20)
            ->get(['part_id', 'part_name', 'phone', 'part_vatnum', 'part_detial', 'district', 'subdistrict', 'province', 'zipcode', 'partner_type']);


        // เติม full_address ถ้าต้องการ
        $results->transform(function ($p) {
            $p->full_address = $p->part_detial;
            return $p;
        });

        return response()->json($results);
    }
    public function storeAjax(Request $request)
    {
        $data = $request->validate([
            'part_name'   => 'required|string|max:255',
            'part_detial' => 'required|string',
            'type'        => 'required|in:1,2',
            // ถ้าต้องการแยก location ให้เพิ่ม validation ตามฟิลด์นั้นๆ
        ]);

        // สร้างรหัส part_id ใหม่ (เหมือน next-id logic)
        $prefix = $data['type'] === '1' ? '1' : '2';
        $last = Partner::where('part_id', 'like', $prefix . '%')->orderBy('part_id', 'desc')->first();
        $seq  = $last ? intval(substr($last->part_id, 1)) + 1 : 1;
        $data['part_id'] = $prefix . str_pad($seq, 7, '0', STR_PAD_LEFT);

        $p = Partner::create($data);

        return response()->json([
            'id'      => $p->part_id,
            'name'    => $p->part_name,
            'type'    => $p->type,
            'address' => $p->part_detial
        ]);
    }
    public function list()
    {
        // ถ้าอยากกรองเฉพาะ active ก็ใส่ where(...) เพิ่มได้
        $partners = Partner::orderBy('part_name')->get(['part_id', 'part_name', 'partner_type', 'part_detial']);
        return response()->json($partners);
    }
}
