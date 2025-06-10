<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Purchase;
use App\Models\Purchaseitem;
use Illuminate\Http\Request;

class StockController extends Controller
{
    //
    public function index()
    {
        // Eager-load purchaseItem และ purchase
        $stocks = Stock::with('product.purchase')
            ->orderBy('id')
            ->get();

        return view('stocks.index', compact('stocks'));
    }

    public function storeWithBill(Request $request)
    {
        // 1) ตรวจสอบเบื้องต้นด้วย Laravel Validator
        $request->validate([
            'partner_id'   => 'required|exists:partners,id',
            'vat_number'   => 'required|digits:13',
            'quantity'     => 'required|integer|min:1',
            'total_cost'   => 'required|numeric|min:0',
            'bill_cost'    => 'required|numeric', // ค่าที่ส่ง hidden มา
        ]);

        // 2) เช็คว่าทุนที่กรอก ตรงกับราคาทุนในบิล (กรณี
        //    ต้องแน่ใจว่าตัวแปร $bill_cost มาจากฝั่ง DB จริง ๆ ไม่ใช่จาก Hidden Field โดยตรง)
        //    ให้ดึงราคาทุนในบิลจากฐานข้อมูลใหม่อีกครั้ง
        $billId   = $request->input('bill_id'); // สมมติเรามี bill_id มาด้วย
        $billData = Purchase::find($billId);
        if (!$billData) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลบิลที่ต้องการใช้ตรวจสอบ');
        }
        $actualBillCost = floatval($billData->total_cost); // หรือคอลัมน์จริงที่เก็บราคา
        $enteredCost    = floatval($request->input('total_cost'));

        if (number_format($enteredCost, 2, '.', '') !== number_format($actualBillCost, 2, '.', '')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'ทุนที่กำหนดไม่ตรงกับราคาทุนในบิล');
        }

        // 3) ถ้าผ่านทุกอย่าง → คำนวณราคาไม่รวม VAT & ราคาต่อหน่วยอีกรอบ (ฝั่ง Server)
        $quantity  = intval($request->input('quantity'));
        $noVatTotal = $enteredCost / 1.07;
        $unitCost   = $noVatTotal / $quantity;

        // 4) บันทึกลงตาราง stocks (หรือ table ที่ออกแบบไว้)  
        Stock::create([
            'partner_id'  => $request->input('partner_id'),
            'vat_number'  => $request->input('vat_number'),
            'bill_id'     => $billId,
            'quantity'    => $quantity,
            'total_cost'  => $enteredCost,
            'unit_cost'   => round($unitCost, 2),
            // … fields อื่น ๆ ตาม schema
        ]);

        return redirect()->route('stocks.index')
            ->with('success', 'บันทึกรายการสินค้าเรียบร้อยแล้ว');
    }

    public function createRestockFromPurchase($purchase_id)
    {
        // ดึง Purchase (เพื่อแสดงเลขบิล, ชื่อร้าน ฯลฯ)
        $purchase = Purchase::findOrFail($purchase_id);

        // ดึงรายการ PurchaseItem ของ Purchase นั้น
        // สมมติ PurchaseItem มีฟิลด์ item_cost, item_quantity => unit_cost = item_cost / item_quantity
        $purchaseItems = Purchaseitem::where('purchase_id', $purchase->purchase_id)
            ->get()
            ->map(function ($item) {
                // คำนวณต้นทุนต่อหน่วย ถ้าในตาราง PurchaseItem เก็บ cost ทั้งก้อน
                $unitCost = 0;
                if ($item->quantity > 0) {
                    $unitCost = $item->price / $item->quantity;
                }
                return (object)[
                    'id'               => $item->id,
                    'product_name'     => $item->product_name,
                    'unit_price'       => $item->price,  // สมมติมีฟิลด์ sale_price ใน PurchaseItem
                    'remain_quantity'  => $item->quantity,
                    // สมมติคุณเก็บว่ามีการใช้งานไปแล้วเท่าไร (used_quantity)
                    'unit_cost'        => $unitCost,
                ];
            });

        // ถ้าไม่มี used_quantity จริงๆ และคุณบันทึกต้นทุนต่อหน่วยไว้ตรง PurchaseItem
        // ก็สามารถเปลี่ยนตามโครงสร้างตารางของคุณได้

        return view('stocks.restock_from_purchase', [
            'purchaseNumber' => $purchase->purchase_document_number,
            'restockItems'   => $purchaseItems,
        ]);
    }

    /**
     * บันทึกข้อมูล Restock เมื่อกดปุ่ม “บันทึก”
     */
    public function storeRestock(Request $request)
    {
        // Validate จำนวนที่จะ restock ทุกตัว
        $rules = [];
        $messages = [];
        foreach ($request->input('restock_qty', []) as $itemId => $qty) {
            $rules["restock_qty.$itemId"] = 'nullable|integer|min:0';
        }
        $request->validate($rules, $messages);

        // บันทึก Stock ทีละรายการ
        foreach ($request->input('restock_qty', []) as $itemId => $qty) {
            $qty = intval($qty);
            if ($qty > 0) {
                // ค่าใน form
                $saleTotal   = floatval($request->input("sale_total.$itemId"));
                $unitCost    = floatval($request->input("unit_cost.$itemId"));
                $costTotal   = floatval($request->input("cost_total.$itemId"));
                $marginPct   = floatval($request->input("margin_pct.$itemId"));
                //$vatNumber   = $request->input("vat_number.$itemId");
                // สมมติคุณมีฟิลด์ vat_number มาแยกจากแถวอื่น ถ้าไม่มีก็ไม่ต้องเอา

                Stock::create([
                    'purchase_item_id' => $itemId,
                    'quantity'         => $qty,
                    'unit_cost'        => $unitCost,
                    'total_cost'       => $costTotal,
                    'sale_total'       => $saleTotal,
                    'margin_pct'       => $marginPct,
                    // ถ้ามีฟิลด์อื่นๆ ให้ใส่เพิ่มเติมที่นี่
                ]);
            }
        }

        return redirect()->route('stocks.index')
            ->with('success', 'ทำการ Restock สำเร็จแล้ว');
    }


    public function createWithdrawFromPurchaseItems(Request $request)
    {
        $query = PurchaseItem::with('purchase');

        if ($request->has('purchase_item_id') && is_numeric($request->purchase_item_id)) {
            $query->where('id', $request->purchase_item_id);
        }

        $purchaseItems = $query->get();

        return view('stocks.withdraw_from_purchaseitems', compact('purchaseItems'));
    }

    /**
     * บันทึกการถอนสินค้า (Withdraw)
     * ลดยอด used_quantity ใน PurchaseItem และสร้าง Stock record (ถ้ามี)
     */
    public function storeWithdraw(Request $request)
    {
        // Validate ว่ามี purchase_item_id[] และ withdraw_qty[] และเป็นตัวเลข
        $rules = [];
        foreach ($request->input('purchase_item_id', []) as $i => $id) {
            $rules["purchase_item_id.$i"] = 'required|integer|exists:purchase_items,id';
            $rules["withdraw_qty.$i"]     = 'required|integer|min:0';
        }
        $request->validate($rules);

        foreach ($request->input('purchase_item_id') as $i => $itemId) {
            $qty = intval($request->input("withdraw_qty.$i", 0));
            if ($qty <= 0) {
                continue;
            }

            $item = PurchaseItem::find($itemId);
            if (! $item) {
                continue;
            }

            // คงเหลือเดิม = item_quantity - used_quantity
            $used     = $qty ?? 0;
            $available = $item->quantity - $used;

            // ถ้าขอถอนเกินคงเหลือ ให้ใช้คงเหลือแทน
            $withdraw = min($qty, $available);

            // อัปเดต used_quantity ใน PurchaseItem
            $item->quantity = $used + $withdraw;
            $item->save();

            // ถ้ามี Stock model สำหรับบันทึกเคลื่อนไหวสต็อก
            // สามารถบันทึกเป็นรายการ negative ได้ (ถ้าไม่ต้องการ ให้คอมเมนต์ส่วนนี้ออก)
            $unitCost = $item->item_quantity > 0
                ? ($item->item_cost / $item->item_quantity)
                : 0;

            Stock::create([
                'purchase_item_id' => $item->id,
                'quantity'         => -$withdraw,
                
            ]);
        }

        return redirect()
            ->route('stocks.index')
            ->with('success', 'ถอนสินค้าเรียบร้อยแล้ว');
    }

     public function processForm(Request $request)
    {
        $query = PurchaseItem::with('purchase');

        if ($request->has('purchase_item_id') && is_numeric($request->purchase_item_id)) {
            $query->where('id', $request->purchase_item_id);
        }

        $purchaseItems = $query->get();

        return view('stocks.process', compact('purchaseItems'));
    }

    /**
     * บันทึกการ Withdraw และ Stock Process พร้อมอัปเดต PurchaseItem
     */
    public function processStore(Request $request)
    {
        // 1) Validate ข้อมูลทั้งสองกลุ่ม
        $rules = [];

        // Withdraw validation
        foreach ($request->input('withdraw_item_id', []) as $i => $id) {
            $rules["withdraw_item_id.$i"] = 'required|integer|exists:purchase_items,id';
            $rules["withdraw_qty.$i"]     = 'required|integer|min:0';
        }

        // Stock Process validation
        foreach ($request->input('stock_item_id', []) as $i => $id) {
            $rules["stock_item_id.$i"]       = 'required|integer|exists:purchase_items,id';
            $rules["stock_qty.$i"]           = 'required|integer|min:1';
            $rules["stock_unit_price.$i"]    = 'required|numeric|min:0';
            // sale_total, unit_cost, cost_total, margin_pct อ่านจาก readonly fields
        }

        $request->validate($rules);

        //dd($request->validate($rules));

        // 2) Withdraw: ลดจำนวนใน PurchaseItem และบันทึก Stock record (negative)
        foreach ($request->input('withdraw_item_id', []) as $i => $itemId) {
            $qty = intval($request->input("withdraw_qty.$i", 0));
            if ($qty <= 0) {
                continue;
            }

            $pi = PurchaseItem::find($itemId);
            if (! $pi) {
                continue;
            }

            // คงเหลือปัจจุบัน
            $available = $pi->quantity;
            $withdraw  = min($qty, $available);
            if ($withdraw <= 0) {
                continue;
            }

            // ลดจำนวน
            $pi->quantity -= $withdraw;
            $pi->save();

            // บันทึกประวัติ Withdraw
            $unitCost = $pi->quantity + $withdraw > 0;

            Stock::create([
                'purchase_item_id' => $pi->id,
                'quantity'         => $withdraw,
                'unit_cost'        => $unitCost,
                'total_cost'       => ($withdraw * $unitCost),
                'type'             => 'withdraw',
            ]);
        }

        // 3) Stock Process: สร้าง Stock record (positive) ตาม input
        foreach ($request->input('stock_item_id', []) as $i => $itemId) {
            $qty        = intval($request->input("stock_qty.$i", 0));
            if ($qty <= 0) {
                continue;
            }
            $unitPrice  = floatval($request->input("stock_unit_price.$i"));
            $saleTotal  = floatval($request->input("stock_sale_total.$i"));
            $unitCost   = floatval($request->input("stock_unit_cost.$i"));
            $costTotal  = floatval($request->input("stock_cost_total.$i"));
            $marginPct  = floatval($request->input("stock_margin_pct.$i"));

            Stock::create([
                'purchase_item_id' => $itemId,
                'quantity'         => $qty,
                'unit_price'       => $unitPrice,
                'sale_total'       => $saleTotal,
                'unit_cost'        => $unitCost,
                'total_cost'       => $costTotal,
                'margin_pct'       => $marginPct,
                'type'             => 'in',  // ประเภทเข้า
            ]);
        }

        return redirect()
            ->route('stocks.index')
            ->with('success', 'ดำเนินการถอนสินค้าและบันทึก Stock Process เรียบร้อยแล้ว');
    }
}
