<?php

namespace App\Http\Controllers;


use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\BranchStockTransfer;
use App\Models\BranchStockTransferItem;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;

class BranchStockTransferController extends Controller
{
    //
    public function create()
    {
        $branches     = Branch::all();
        $purchaseItems = PurchaseItem::with('purchase')->get();
        return view('stock_transfers.create', compact('branches', 'purchaseItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_branch'    => 'required|exists:branches,branch_id',
            'to_branch'      => 'required|exists:branches,branch_id|different:from_branch',
            'transfer_date'  => 'required|date',
            'item_id.*'      => 'required|exists:purchase_items,id',
            'qty.*'          => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($data) {
            $transfer = BranchStockTransfer::create([
                'from_branch_id' => $data['from_branch'],
                'to_branch_id'  => $data['to_branch'],
                'transfer_date' => $data['transfer_date'],
            ]);

            foreach ($data['item_id'] as $i => $itemId) {
                $qty = $data['qty'][$i];

                // ต้นทาง
                $bsFrom = BranchStock::firstOrCreate(
                    ['branch_id' => $data['from_branch'], 'purchase_item_id' => $itemId],
                    ['quantity' => 0]
                );
                if ($bsFrom->quantity < $qty) {
                    throw new \Exception("สต็อกไม่เพียงพอสำหรับ Item {$itemId}");
                }
                $bsFrom->decrement('quantity', $qty);

                // ปลายทาง
                $bsTo = BranchStock::firstOrCreate(
                    ['branch_id' => $data['to_branch'], 'purchase_item_id' => $itemId],
                    ['quantity' => 0]
                );
                $bsTo->increment('quantity', $qty);

                // บันทึกประวัติ
                BranchStockTransferItem::create([
                    'transfer_id'       => $transfer->id,
                    'purchase_item_id'  => $itemId,
                    'quantity'          => $qty,
                ]);
            }
        });

        return redirect()->route('stock_transfers.create')
            ->with('success', 'โอนสินค้าสำเร็จ');
    }
}
