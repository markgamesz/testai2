<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Partner;
use App\Models\Company;
use App\Models\PurchaseItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // แสดงรายการข้อมูลทั้งหมด
    public function index()
    {
        $purchases = Purchase::all();
        return view('purchases.index', compact('purchases'));
    }

    // แสดงฟอร์มสำหรับสร้างข้อมูลใหม่
    public function create()
    {

        $branches = Branch::all();
        $partners = Partner::all();
        #$companies = Company::all();

        // ส่งตัวแปร $branches ไปยัง view createPurchases
        return view('purchases.create', compact('branches', 'partners'));
    }

    // บันทึกข้อมูลใหม่
    public function store(Request $request)
    {
        /*$data = $request->validate([
            'partner_id'               => 'required|string|exists:partners,part_id',
            //'purchase_type'            => 'required|in:0,1',
            'purchase_document_number' => 'required|string',
            'product_name'             => 'required|array|min:1',
            'product_name.*'           => 'required|string',
            'product_detail1.*'        => 'nullable|string',
            'product_detail2.*'        => 'nullable|string',
            'product_qty.*'            => 'required|integer|min:1',
            'product_price.*'          => 'required|numeric',
        ]);*/

        $data = $request->validate([
            'partner_id'                => 'required|exists:partners,part_id',
            'branch_id'                 => 'required|exists:branches,branch_id',
            'purchase_document_number'  => 'required|string|unique:purchases,purchase_document_number',
            'insert_date'               => 'required|date',
            'buy_date'                  => 'required|date',
            'ref_id'                    => 'nullable|string',
            'payment_type'              => 'required|in:Cash,Credit',
            'product_name'              => 'required|array|min:1',
            'product_name.*'            => 'required|string',
            'product_detail1'           => 'nullable|array',
            'product_detail1.*'         => 'nullable|string',
            'product_detail2'           => 'nullable|array',
            'product_detail2.*'         => 'nullable|string',
            'product_qty'               => 'required|array',
            'product_qty.*'             => 'required|string',
            'product_price'             => 'required|array',
            'product_price.*'           => 'required|string',
            'product_total'             => 'required|array',
            'product_total.*'           => 'required|string',
            'vat_amount'                => 'required|string',
            'total'                     => 'required|string',
            'sum_qty'                 => 'required|string',
        ]);

        // รับมาจาก $request หรือคำนวณมา
        //$rawtotal_qty = $request->input('total_qty', 0);
        $rawVat = $request->input('vat_amount', 0);
        $rawTotal = $request->input('total', 0);

        // เอา comma ออก
        //$cleantotal_qty = str_replace(',', '', $rawtotal_qty);
        $cleanVat = str_replace(',', '', $rawVat);
        $cleanTatal = str_replace(',', '', $rawTotal);

        $names   = $data['product_name'];
        $detail1 = $data['product_detail1'] ?? [];
        $detail2 = $data['product_detail2'] ?? [];
        $qtys    = $data['product_qty'];
        $prices  = $data['product_price'];
        //$data['total_qty'] = round((int) $rawtotal_qty);
        $data['vat_amount'] = round((float) $cleanVat, 2);
        $data['total'] = round((float) $cleanTatal, 2);

        // ใช้ค่าจาก Request โดยตรงเพื่อแน่ใจว่าได้ branch_id
        //$branchId   = $request->input('branch_id');
        //$branchCode = $request->input('branch_code');

        DB::transaction(function () use ($data) {
            /* $purchase = Purchase::create([
                'user_id'                  => Auth::id(), 
                //'purchase_type'            => $data['purchase_type'],
                'purchase_document_number' => $data['purchase_document_number'],
                'branch_id'                => $branchId,
                'branch_code'              => $branchCode,
                //'insert_date'              => $data['insert_date'],
                //'buy_date'                 => $data['buy_date'],
                //'ref_id'                   => $data['ref_id'] ?? null,
                'part_id'                  => $data['partner_id'],
                //'partner_address'          => $data['partner_address'],
                //'vat_amount'               => $data['vat_amount'],
                //'purchase_total'           => $data['purchase_total'],
                //'payment_type'             => $data['payment_type'],
            ]);*/

            $purchase = Purchase::create([
                'partner_id'               => $data['partner_id'],
                'branch_id'                => $data['branch_id'],
                'purchase_document_number' => $data['purchase_document_number'],
                'insert_date'              => $data['insert_date'],
                'buy_date'                 => $data['buy_date'],
                'ref_id'                   => $data['ref_id'] ?? null,
                'payment_type'             => $data['payment_type'],
                'total_qty'                 => $data['sum_qty'],
                'vat_amount'                => $data['vat_amount'],
                'total'                     => $data['total'],
                'user_id'                   => Auth::id(),
            ]);

            foreach ($data['product_name'] as $i => $name) {
                // รับมาจาก $request หรือคำนวณมา
                $rawproduct_qty = $data['product_qty'][$i];
                $rawproduct_price = $data['product_price'][$i];

                // เอา comma ออก
                $cleanqty = str_replace(',', '', $rawproduct_qty);
                $cleanprice = str_replace(',', '', $rawproduct_price);

                $data['product_qty'][$i] = round((float) $cleanqty, 2);
                $data['product_price'][$i] = round((float) $cleanprice, 2);

                PurchaseItem::create([
                    'purchase_id' => $purchase->purchase_id,
                    'product_name'        => $name,
                    'product_detail1'     => $data['product_detail1'][$i],
                    'product_detail2'     => $data['product_detail2'][$i],
                    'quantity'    => $data['product_qty'][$i],
                    'price'       => $data['product_price'][$i],
                ]);
            }
        });


        return redirect()->route('purchases.index')
            ->with('success', 'Purchase created successfully.');
    }

    // แสดงฟอร์มสำหรับแก้ไขข้อมูล
    public function edit(Purchase $purchase)
    {


        $purchaseitems = $purchase->load('purchaseitems');
        $partner = $purchase->load('partners');
        $partners = Partner::orderBy('part_name')->get();
        $branches = Branch::orderBy('branch_name')->get();
        return view('purchases.edit', compact('purchaseitems', 'partners', 'branches', 'partner', 'purchase'));
    }

    // อัปเดตข้อมูลที่แก้ไข
    public function update(Request $request, Purchase $purchase)
    {
        // Sanitize comma-formatted numbers before validation
        $input = $request->all();
        $input['total']      = str_replace(',', '', $input['total'] ?? '');
        $input['vat_amount'] = str_replace(',', '', $input['vat_amount'] ?? '');
        $input['grand_total'] = str_replace(',', '', $input['grand_total'] ?? '');

        if (!empty($input['product_qty'])) {
            $input['product_qty'] = array_map(function ($v) {
                return str_replace(',', '', $v);
            }, $input['product_qty']);
        }
        if (!empty($input['product_price'])) {
            $input['product_price'] = array_map(function ($v) {
                return str_replace(',', '', $v);
            }, $input['product_price']);
        }
        $request->replace($input);
        $data = $request->validate([
            'partner_id'               => 'required|exists:partners,part_id',
            'branch_id'                => 'required|exists:branches,branch_id',
            'purchase_document_number' => 'required|string',
            'insert_date'              => 'required|date',
            'buy_date'                 => 'required|date',
            'ref_id'                   => 'nullable|string',
            'product_name.*'           => 'required|string|max:255',
            'product_detail1.*'        => 'nullable|string|max:255',
            'product_detail2.*'        => 'nullable|string|max:255',
            'product_qty.*'            => 'required|numeric|min:0',
            'product_price.*'          => 'required|numeric|min:0',
            'payment_type'              => 'required|in:Cash,Credit',
            'sum_qty'                 => 'required|string',
        ]);

        // Clean numeric inputs
        $total = (float) str_replace(',', '', $request->input('total', 0));
        $vat   = (float) str_replace(',', '', $request->input('vat_amount', 0));
        $grand = (float) str_replace(',', '', $request->input('grand_total', 0));

        // Update purchase
        $purchase->update([
            'part_id'               => $data['partner_id'],
            'branch_id'                => $data['branch_id'],
            'purchase_document_number' => $data['purchase_document_number'],
            'insert_date'              => $data['insert_date'],
            'buy_date'                 => $data['buy_date'],
            'ref_id'                   => $data['ref_id'],
            'vat_amount'             => $vat,
            'total'           => $total,
            'payment_type'               => $data['payment_type'],
            'total_qty'                 => $data['sum_qty'],

        ]);

        // Delete old items and recreate
        $purchase->purchaseItems()->delete();
        foreach ($request->input('product_name') as $i => $name) {
            $purchase->purchaseItems()->create([
                'product_name'    => $name,
                'product_detail1' => $request->input('product_detail1')[$i] ?? null,
                'product_detail2' => $request->input('product_detail2')[$i] ?? null,
                'quantity'     => (float) str_replace(',', '', $request->input('product_qty')[$i]),
                'price'   => (float) str_replace(',', '', $request->input('product_price')[$i]),



            ]);
        }

        return redirect()
            ->route('purchases.index')
            ->with('success', 'แก้ไขรายการรับเข้าเรียบร้อยแล้ว');
    }

    // ลบข้อมูล
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->status     = 0;
        $purchase->deleted_by = Auth::id();
        $purchase->save();

        return redirect()
            ->route('purchases.index')
            ->with('success', 'ลบรายการรับเข้าเรียบร้อยแล้ว');
    }

    // Method สำหรับ AJAX ให้สร้างเลขที่เอกสารใหม่ เมื่อเลือก "บริษัท"
    public function nextDocumentNumber(Request $request)
    {
        try {
            // รับค่า purchase_party จาก request ซึ่งควรเป็น "บริษัท" หรือ "บุคคล"
            $party = $request->input('purchase_type');
            if (!in_array($party, ['1', '0'])) {
                return response()->json(['document_number' => ''], 400);
            }

            // กำหนด prefix ตามประเภท: "บริษัท" => PC, "บุคคล" => PA
            $prefix = ($party === '1') ? 'PC' : 'PA';
            // เติมวันที่ในรูปแบบ YYMMDD
            $prefix .= date('ymd');  // ตัวอย่าง: PC250410 หรือ PA250410

            // ค้นหา Purchase ล่าสุดที่มีเลขที่เอกสารขึ้นต้นด้วย prefix นี้
            $lastPurchase = Purchase::where('purchase_document_number', 'like', $prefix . '%')
                ->orderBy('purchase_document_number', 'desc')
                ->first();

            if ($lastPurchase && !empty($lastPurchase->purchase_document_number)) {
                // สมมุติว่าเลขที่เอกสารเป็นรูปแบบ: prefix(8 ตัวอักษร) + run number (5 หลัก)
                $lastRun = intval(substr($lastPurchase->purchase_document_number, 8));
                $nextRun = $lastRun + 1;
            } else {
                // กรณีที่ยังไม่มีข้อมูลสำหรับ prefix นี้ ให้เริ่มที่ 1
                $nextRun = 1;
            }

            // รวมเลขที่เอกสาร โดยเติม run number ให้มีความยาว 5 หลัก
            $document_number = $prefix . sprintf("%05d", $nextRun);

            return response()->json(['document_number' => $document_number]);
        } catch (\Exception $e) {
            \Log::error('Error in nextDocumentNumber: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * สรุป Stock: แสดงจำนวนสินค้า และมูลค่ารวมต่อบิล
     */
    public function stock()
    {
        $stocks = Purchase::with('partner', 'purchaseItems')
            ->get()
            ->map(function ($purchase) {
                $quantity = $purchase->purchaseItems->sum('product_qty');
                $totalPrice = $purchase->purchaseItems->sum(function ($item) {
                    return $item->product_qty * $item->product_price;
                });

                return (object) [
                    'document_number' => $purchase->purchase_document_number,
                    'date' => $purchase->buy_date,
                    //'partner_name' => optional($purchase->partner)->part_name,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                ];
            });

        return view('purchases.stock', compact('stocks'));
    }

    public function stockSummary()
    {
        // เปลี่ยนเป็น 'quantity' ตามชื่อฟิลด์จริงในตาราง purchase_items
        $stocks = Purchase::withSum('purchaseItems', 'quantity')
            ->get();

        // คำนวณมูลค่ารวมของแต่ละบิล
        foreach ($stocks as $stock) {
            $stock->items_total_price = $stock->purchaseItems
                ->sum(function ($item) {
                    return $item->quantity * $item->price;
                });
        }


        return view('purchases.stock_summary', compact('stocks'));
    }

    /** Show the form where the user can enter qty+price to consume */
    public function showConsumeForm(Purchase $purchase)
    {
        // Eager‐load items
        $purchase->load('purchaseItems');
        return view('purchases.consume', compact('purchase'));
    }

    /** Process the form and deduct from stock */
    public function consumeStock(Request $request, Purchase $purchase)
    {
        // validate that each consume_qty is integer ≥0 and ≤ original qty,
        // and each consume_price is numeric ≥0
        $rules = [];
        foreach ($purchase->purchaseItems as $item) {
            $maxQty = $stockMap[$item->purchase_id] ?? 0;
            $rules["consume_qty.{$item->id}"]   = "nullable|integer|min:0|max:{$item->quantity}";
            $rules["consume_qty.{$item->id}"] = [
                'nullable',
                'integer',
                'min:0',
                // ดักไม่ให้เกินที่มีใน stock
                "max:{$maxQty}"
            ];
            $rules["consume_price.{$item->id}"] = "nullable|numeric|min:0";
        }
        $data = $request->validate($rules);

        DB::transaction(function () use ($purchase, $data) {
            foreach ($purchase->purchaseItems as $item) {
                $cid   = $item->id;
                $qty   = $data['consume_qty'][$cid]   ?? 0;
                $price = $data['consume_price'][$cid] ?? 0;

                if ($qty > 0) {
                    // adjust Stock model (firstOrCreate if missing)
                    $stock = Stock::firstOrCreate(
                        ['product_id' => $item->id],
                        ['quantity' => 0, 'avg_cost' => $item->price]
                    );

                    if ($stock->quantity < $qty) {
                        throw new \Exception("Stock for product #{$item->id} is insufficient.");
                    }

                    // subtract
                    $stock->quantity -= $qty;

                    // optionally recalc avg_cost, e.g. weighted:
                    // $remaining = $stock->quantity;
                    // $stock->avg_cost = $remaining
                    //     ? ( $remaining * $stock->avg_cost - $qty * $price ) / $remaining
                    //     : 0;

                    $stock->save();
                }
            }

            // mark this purchase as consumed
            $purchase->update([
                'status'      => 'completed',
                'consumed_by' => auth()->id(),
                'consumed_at' => now(),
            ]);
        });

        return redirect()
            ->route('purchases.consume.form', $purchase)
            ->with('success', 'ย่อยสินค้าเรียบร้อยแล้ว');
    }

    /** Show form to edit qty & total cost before restocking */
    public function showRestockForm(Purchase $purchase)
    {
        $purchase->load('purchaseItems');
        return view('purchases.restock', compact('purchase'));
    }

    /** Process restock: update stocks table */
    public function restock(Request $request, Purchase $purchase)
    {
        // 1) Validation per item
        $rules = [];
        foreach ($purchase->purchaseItems as $item) {
            $rules["qty.{$item->id}"]   = 'required|integer|min:0';
            $rules["total.{$item->id}"] = 'required|numeric|min:0';
        }
        $data = $request->validate($rules);

        // 2) Perform within transaction
        DB::transaction(function () use ($purchase, $data) {
            foreach ($purchase->purchaseItems as $item) {
                $qty       = $data['qty'][$item->id];
                $totalCost = $data['total'][$item->id];

                if ($qty <= 0) {
                    continue;
                }

                // unit cost from edited total
                $unitCost = $qty > 0 ? ($totalCost / $qty) : 0;

                // find or create stock record
                $stock = Stock::firstOrCreate(
                    ['product_id' => $item->product_id],
                    ['quantity'   => 0, 'avg_cost' => $unitCost]
                );

                // compute weighted average cost
                $existingQty     = $stock->quantity;
                $existingAvgCost = $stock->avg_cost;
                $newQty          = $existingQty + $qty;
                $newAvgCost      = $newQty > 0
                    ? (($existingQty * $existingAvgCost + $qty * $unitCost) / $newQty)
                    : $unitCost;

                // update stock
                $stock->update([
                    'quantity' => $newQty,
                    'avg_cost' => $newAvgCost,
                ]);
            }
        });

        return redirect()
            ->route('stocks.index')
            ->with('success', 'Restock completed and recorded into stocks table!');
    }
}
