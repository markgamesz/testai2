<?php

namespace App\Http\Controllers;


use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Restock;
use App\Models\RestockUnit;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestockController extends Controller
{

    public function showRestockForm(PurchaseItem $item)
    {
        return view('restock.restock', compact('item'));
    }

    /**
     * ประมวลผล Restock: 
     * 1) ลด quantity ใน purchase_items 
     * 2) เพิ่มหรืออัปเดต stock
     */
    // ประมวลผล restock แบบรายรายการ
    public function handleRestock(Request $request, PurchaseItem $item)
    {
        // 1. Validate ว่าจำนวนที่ Restock ต้องไม่เกินของเดิมใน DB
        $data = $request->validate([
            'restock_qty' => 'required|integer|min:1|max:' . $item->quantity,
        ]);

        DB::transaction(function () use ($item, $data) {
            // 2. ลดจำนวนใน purchase_items
            $item->decrement('quantity', $data['restock_qty']);

            // 3. คำนวณต้นทุนต่อหน่วยจาก price ของ PurchaseItem
            $unitCost = $item->price;

            // 4. อัปเดตหรือสร้าง record ใน stocks
            $stock = Stock::firstOrCreate(
                ['product_id' => $item->id],
                ['quantity'   => 0, 'avg_cost' => $unitCost]
            );

            // 5. คำนวณ weighted avg cost (ตัวอย่างใช้ต้นทุนเดิม)
            $newQty = $stock->quantity + $data['restock_qty'];
            $newAvg = $newQty > 0
                ? (($stock->quantity * $stock->avg_cost + $data['restock_qty'] * $unitCost) / $newQty)
                : $unitCost;

            $stock->update([
                'quantity' => $newQty,
                'avg_cost' => $newAvg,
            ]);
        });

        return redirect()
            ->route('stocks.index')
            ->with('success', 'Restock สำเร็จ! ลดจำนวนใน PurchaseItem และอัปเดต Stock แล้ว');
    }

    /** Step 2: show split form with split_count rows */
    public function showSplitForm(Restock $restock)
    {
        $cfg = session("restock.{$restock->id}");
        abort_unless($cfg, 404);

        $units = array_fill(0, $cfg['split_count'], [
            'sale_price' => number_format($cfg['unit_cost'], 2, '.', ''),
            'barcode'   => sprintf("%s-%04d", $restock->id, rand(1, 9999)),
        ]);

        return view('restock.split', compact('restock', 'units'));
    }

    /** Step 2: validate & persist each unit, ensure sum ≤ restock_total */
    public function handleSplit(Request $request, Restock $restock)
    {
        $cfg = session("restock.{$restock->id}");
        abort_unless($cfg, 404);

        $rules = [];
        foreach ($cfg['split_count'] ? range(0, $cfg['split_count'] - 1) : [] as $i) {
            $rules["sale_price.$i"] = 'required|numeric|min:0';
            $rules["barcode.$i"]    = 'required|string';
        }
        $data = $request->validate($rules);

        // check sum of sale_prices ≤ restock_total
        $sum = array_sum($data['sale_price']);
        if ($sum > $cfg['restock_total']) {
            return back()
                ->withErrors(['sale_price' => "มูลค่ารวม ({$sum}) เกิน restock_total ({$cfg['restock_total']})"])
                ->withInput();
        }

        DB::transaction(function () use ($restock, $data) {
            foreach ($data['sale_price'] as $i => $price) {
                RestockUnit::create([
                    'restock_id' => $restock->id,
                    'sale_price' => $price,
                    'barcode' => $data['barcode'][$i],
                ]);
            }
        });

        session()->forget("restock.{$restock->id}");
        return redirect()->route('stocks.index')
            ->with('success', 'Split & units saved!');
    }

    /** แสดงหน้ารวม Restock ทั้งหมด */
    public function index()
    {
        // เตรียมข้อมูลพร้อมความสัมพันธ์ไปยัง PurchaseItem
        //$restock = Restock::with('item')
        //  ->orderBy('restocked_at', 'desc')
        //->get();
        $stocks = Restock::with('product.purchase')->orderBy('id')->get();

        return view('restock.index', compact('stocks'));
    }


    public function showBulkSplitForm()
    {
        // session('restock') is an array keyed by purchase_item_id
        // each value has ['qty','unit_cost','split_count']
        $data = session('restock', []);

        // build a flat list of units
        $units = [];
        foreach ($data as $itemId => $cfg) {
            for ($i = 0; $i < $cfg['split_count']; $i++) {
                $units[] = [
                    'purchase_item_id' => $itemId,
                    'sale_price'       => number_format($cfg['unit_cost'], 2, '.', ''),
                    'barcode'          => "{$itemId}-" . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                ];
            }
        }

        return view('restocks.split_bulk', compact('units'));
    }

    /**
     * Step 3: Persist all RestockUnit rows
     */
    public function handleBulkSplit(Request $request)
    {
        // validate parallel arrays
        $rules = [
            'sale_price.*' => 'required|numeric|min:0',
            'barcode.*'    => 'required|string',
        ];
        $data = $request->validate($rules);

        foreach ($data['sale_price'] as $i => $price) {
            RestockUnit::create([
                'purchase_item_id' => $request->units[$i]['purchase_item_id'],
                'sale_price'       => $price,
                'barcode'          => $data['barcode'][$i],
            ]);
        }

        // clear session now that we’ve saved
        session()->forget('restock');

        return redirect()
            ->route('restocks.index')
            ->with('success', 'Bulk split completed!');
    }

    public function showForm(Purchase $purchase)
    {
        // โหลดความสัมพันธ์ purchaseItems เพื่อดึงจำนวนและราคา
        $purchase->load('purchaseItems');

        return view('purchases.restock', compact('purchase'));
    }
    public function store(Request $request, Purchase $purchase)
    {
        // สร้างกฎตรวจสอบแบบ per-item
        $rules = [];
        foreach ($purchase->purchaseItems as $item) {
            // restock_qty ต้องไม่เกิน quantity เดิมใน purchase_items
            $rules["restock_qty.{$item->id}"] = 'integer|min:0|max:' . $item->quantity;
        }
        $data = $request->validate($rules);

        DB::transaction(function () use ($purchase, $data) {
            foreach ($purchase->purchaseItems as $item) {
                $qtyToRestock = $data['restock_qty'][$item->id] ?? 0;
                if ($qtyToRestock <= 0) {
                    continue; // ถ้ามากกว่า 0 เท่านั้นถึงจะ Restock
                }

                // 1) ลดจำนวนใน purchase_items
                $item->decrement('quantity', $qtyToRestock);

                // 2) ต้นทุนต่อหน่วย = price จาก purchase_items
                $unitCost = $item->price;

                // 3) อัปเดตหรือสร้างใน stocks
                $stock = Stock::firstOrCreate(
                    ['product_id' => $item->id],
                    ['quantity'   => 0, 'avg_cost' => $unitCost]
                );
                // คำนวณ weighted average cost (ตัวอย่างใช้ต้นทุนเดิม)
                $newQty = $qtyToRestock;
                $newAvg = $newQty > 0
                    ? (($stock->quantity * $stock->avg_cost + $qtyToRestock * $unitCost) / $newQty)
                    : $unitCost;

                $stock->update([
                    'quantity' => $newQty,
                    'avg_cost' => $newAvg,
                ]);
            }
        });

        return redirect()
            ->route('stocks.index')
            ->with('success', 'บันทึกสต๊อกแปรรูปสำเร็จ');
    }
}
