<?php

namespace App\Http\Controllers;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    //
    public function index()
    {
        // ดึง PurchaseItem พร้อมความสัมพันธ์กับ Purchase (เพื่อเอา purchase_number มาแสดง)
        $purchaseItems = PurchaseItem::all();

        return view('purchase_items.index', compact('purchaseItems'));
    }

    /**
     * แสดงฟอร์ม Restock & เฉลี่ยราคาต้นทุน สำหรับ PurchaseItem เดียว (อ้างอิงจาก ID)
     *
     * @param  int  $purchaseitem  (คือ id ของ PurchaseItem)
     * @return \Illuminate\View\View
     */
    public function restockAndAverage($purchaseitem)
    {
        $allItems = PurchaseItem::with('purchase')->get();
    return view('purchase_items.restock_and_average_dynamic', compact('allItems'));
    }

    /**
     * บันทึกการ Restock & เฉลี่ยต้นทุนเมื่อกดปุ่ม “บันทึก” ในฟอร์ม
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStock(Request $request)
    {
        // 1. Validate ว่ามี add_qty และ add_unit_cost สำหรับแต่ละ ID ที่ส่งมา
        $rules = [];
        foreach ($request->input('add_qty', []) as $id => $val) {
            $rules["add_qty.$id"]        = 'nullable|integer|min:0';
            $rules["add_unit_cost.$id"]  = 'nullable|numeric|min:0';
        }
        $request->validate($rules);

        // 2. วนลูปเพื่ออัปเดตแต่ละ PurchaseItem
        foreach ($request->input('add_qty', []) as $id => $addQty) {
            $addQty = intval($addQty);
            if ($addQty <= 0) {
                continue; // ข้ามถ้าไม่ได้เพิ่มจำนวน
            }

            $purchaseItem = PurchaseItem::find($id);
            if (! $purchaseItem) {
                continue;
            }

            // ดึงข้อมูลเดิมจากฐานข้อมูล
            $origQty  = intval($purchaseItem->item_quantity);
            $origCost = floatval($purchaseItem->item_cost);
            $origUnitCost = $origQty > 0 ? ($origCost / $origQty) : 0;

            // ดึงต้นทุนเพิ่มเติมต่อชิ้น
            $addUnitCost = floatval($request->input("add_unit_cost.$id", 0));
            if ($addUnitCost < 0) {
                $addUnitCost = 0;
            }

            // คำนวณต้นทุนที่เพิ่มเข้ามาทั้งหมด
            $addTotalCost = $addQty * $addUnitCost;

            // คำนวณยอดใหม่ (จำนวนรวม และต้นทุนรวม)
            $newQty            = $origQty + $addQty;
            $origTotalCost     = $origQty * $origUnitCost;      // เท่ากับ $origCost
            $combinedTotalCost = $origTotalCost + $addTotalCost; // ต้นทุนรวมใหม่ทั้งหมด

            // อัปเดตฟิลด์ในตาราง purchase_items
            $purchaseItem->item_quantity = $newQty;
            $purchaseItem->item_cost     = $combinedTotalCost; // เก็บเป็นต้นทุนรวมใหม่

            // (ถ้ามีฟิลด์อื่นๆ เช่น unit_cost, ก็ให้คำนวณแล้วอัปเดตด้วย)
            // ตัวอย่าง: $purchaseItem->unit_cost = $newQty > 0 ? ($combinedTotalCost / $newQty) : 0;

            $purchaseItem->save();
        }

        return redirect()
            ->route('purchaseitems.index') // กลับไปหน้ารายการ PurchaseItems
            ->with('success', 'ทำการ Restock & เฉลี่ยราคาต้นทุนเรียบร้อยแล้ว');
    }

    
}
