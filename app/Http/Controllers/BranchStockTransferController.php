<?php

namespace App\Http\Controllers;


use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\BranchStockTransfer;
use App\Models\BranchStockTransferItem;
use App\Models\PurchaseItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BranchStockTransferController extends Controller
{

    public function index()
    {
        $transfers = BranchStockTransfer::with('fromBranch', 'toBranch')
            ->orderByDesc('transfer_date')
            ->paginate(10);
        return view('stock_transfers.index', compact('transfers'));
    }

    // ฟอร์มแสดงใบโอน + สแกน Barcode
    public function show(BranchStockTransfer $transfer)
    {
        $transfer->load('items.purchaseItem');
        $branches = Branch::all();
        return view('stock_transfers.show', compact('transfer', 'branches'));
    }

    // เริ่มโอนใหม่ (optional)
    public function create()
    {
        $branches = Branch::all();
        // เพิ่มการดึงสินค้าสำหรับ dropdown
        $purchaseItems = PurchaseItem::with('purchase')->get();
        return view('stock_transfers.create', compact('branches', 'purchaseItems'));
    }

    // สร้างใบโอน
    public function store(Request $request)
    {
        $data = $request->validate([
            'from_branch' => 'required|exists:branches,id',
            'to_branch'  => 'required|exists:branches,id|different:from_branch',
            'transfer_date' => 'required|date',
            'barcodes.*' => 'required|string',
        ]);

        DB::transaction(function () use ($data) {
            $t = BranchStockTransfer::create([
                'from_branch_id' => $data['from_branch'],
                'to_branch_id'  => $data['to_branch'],
                'transfer_date' => $data['transfer_date'],
            ]);
            foreach ($data['barcodes'] as $bc) {
                // หา PurchaseItem จาก barcode
                $pi = PurchaseItem::where('barcode', $bc)->first();
                BranchStockTransferItem::create([
                    'transfer_id' => $t->id,
                    'purchase_item_id' => $pi->id,
                    'barcode' => $bc,
                ]);
            }
            return redirect()->route('stock_transfers.show', $t)
                ->with('success', 'สร้างใบโอนเรียบร้อย');
        });
    }

    // AJAX: สแกน Barcode หน้ารายการ
    public function scan(Request $request, BranchStockTransfer $transfer)
    {
        $request->validate(['barcode' => 'required|string']);
        $item = $transfer->items()->where('barcode', $request->barcode)->firstOrFail();
        $item->scanned = '1';
        $item->save();
        return response()->json(['ok' => true]);
    }

    // รับสินค้าเข้า ถ้า scan ครบทุกแถว
    public function receive(Request $request, BranchStockTransfer $transfer)
    {
        if ($transfer->items()->where('scanned', '0')->exists()) {
            return back()->withErrors('ยังสแกนไม่ครบทุกรายการ');
        }
        // mark รับสำเร็จ
        $transfer->update(['status' => 'received', 'received_at' => now()]);
        // อัปเดตสต็อกสาขาปลายทาง
        foreach ($transfer->items as $it) {
            BranchStock::updateOrCreate([
                'branch_id' => $transfer->to_branch_id,
                'purchase_item_id' => $it->purchase_item_id
            ], ['quantity' => DB::raw('quantity + ' . $it->quantity)]);
        }
        return redirect()->route('stock_transfers.show', $transfer)
            ->with('success', 'รับสินค้าเข้าเรียบร้อย');
    }

    // ฟังก์ชันสำหรับ AJAX ดึง stocks ตาม branchId
    public function fetchStocks($branchId)
    {
        // ตรวจสอบว่ามี branchId หรือไม่ (optional)
        if (!is_numeric($branchId)) {
            return response()->json(['error' => 'Invalid branch ID'], 400);
        }

        // ดึง stocks จากตาราง stocks ตาม branch_id
        $stocks = Stock::with('product')
            ->where('branch_id', $branchId)
            ->get();

        return response()->json($stocks);
    }
}
