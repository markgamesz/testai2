<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SubExpenssController;
use App\Http\Controllers\ExpenssController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\RestockController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PurchaseItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/partners/list', [PartnerController::class, 'list'])->name('partners.list');
    Route::get('/partners/search', [PartnerController::class, 'search'])->name('partners.search');
    Route::post('/partners/store-ajax', [PartnerController::class, 'storeAjax'])->name('partners.storeAjax');
    Route::get('/partners/next-id', [PartnerController::class, 'nextId'])->name('partners.nextId');
    Route::get('/partners/details/{id}', [PartnerController::class, 'details'])->name('partners.details');
    Route::resource('partners', PartnerController::class);
    Route::resource('company', CompanyController::class);
    Route::get('purchases/stock-summary', [PurchaseController::class, 'stockSummary'])->name('purchases.stockSummary');
    Route::get('stock', [PurchaseController::class, 'stock'])->name('purchases.stock');
    Route::get('/purchases/next-document', [PurchaseController::class, 'nextDocumentNumber'])->name('purchases.nextDocumentNumber');

    // Show the consume‐form
    Route::get('purchases/{purchase}/consume', [PurchaseController::class, 'showConsumeForm'])->name('purchases.consume.form');

    // Handle the consume‐submission
    Route::post(
        'purchases/{purchase}/consume',
        [PurchaseController::class, 'consumeStock']
    )->name('purchases.consume');
    Route::resource('purchases', PurchaseController::class);
    Route::resource('sub_expenss', SubExpenssController::class);
    Route::resource('expenss', ExpenssController::class);
    Route::resource('branches', BranchController::class);

    Route::post(
        'purchase-items/{item}/restock',
        [RestockController::class, 'handleRestock']
    )->name('restock.handle');
    Route::get(
        'purchase-items/{item}/restock',
        [RestockController::class, 'showRestockForm']
    )->name('restock.form');

    Route::get(
        'restocks/{restock}/split',
        [RestockController::class, 'showSplitForm']
    )->name('restock.split.form');



    // POST บันทึก restock
    Route::post(
        'purchases/{purchase}/restock',
        [RestockController::class, 'handleRestock']
    )->name('purchases.restock.handle');

    Route::post(
        'restocks/{restock}/split',
        [RestockController::class, 'handleSplit']
    )->name('restock.split.handle');
    Route::get('restock', [RestockController::class, 'index'])
        ->name('restock.index');

    // 1️⃣ Show the bulk-split form
    Route::get(
        'restocks/split-bulk',
        [RestockController::class, 'showBulkSplitForm']
    )->name('restock.split.bulk');

    // 2️⃣ Handle the bulk-split submission
    Route::post(
        'restocks/split-bulk',
        [RestockController::class, 'handleBulkSplit']
    )->name('restock.split.bulk.handle');

    // 1) ฟอร์ม Restock (GET)
    Route::get(
        'purchase-items/{item}/restock',
        [RestockController::class, 'showRestockForm']
    )->name('restock.restock.form');

    // 2) ประมวลผล Restock (POST)
    Route::post(
        'purchase-items/{item}/restock',
        [RestockController::class, 'handleRestock']
    )->name('restock.restock.handle');

    Route::get(
        'purchases/{purchase}/restock',
        [RestockController::class, 'showForm']
    )->name('purchases.restock.form');

    Route::post(
        'purchases/{purchase}/restock',
        [RestockController::class, 'store']
    )->name('purchases.restock.store');

    Route::get('stocks', [StockController::class, 'index'])
        ->name('stocks.index');

    // 2) แสดงฟอร์ม “เพิ่ม Stock พร้อมเฉลี่ยราคาทุนจาก PurchaseItem”
    Route::get('/stocks/create-with-purchaseitem', [StockController::class, 'createWithPurchaseItem'])
        ->name('stocks.create_with_purchaseitem');

    // 3) บันทึกข้อมูล Stock จากฟอร์ม (POST)
    Route::post('/stocks/store-with-purchaseitem', [StockController::class, 'storeWithPurchaseItem'])
        ->name('stocks.store_with_purchaseitem');

    // หน้าเลือก Restock จาก Purchase (GET)
    Route::get('/stocks/restock/{purchaseId}', [StockController::class, 'createRestockFromPurchase'])
        ->name('stocks.restock_from_purchase');

    // เมื่อกด “บันทึก” Restock (POST)
    Route::post('/stocks/store-restock', [StockController::class, 'storeRestock'])
        ->name('stocks.store_restock');

    Route::get('/purchaseitems', [PurchaseItemController::class, 'index'])
        ->name('purchaseitems.index');

    // เส้นทางสำหรับแสดงฟอร์ม Restock & เฉลี่ยต้นทุน ของ PurchaseItem แต่ละรายการ
    Route::get('/purchaseitems/{purchaseitem}/restock-average', [PurchaseItemController::class, 'restockAndAverage'])
        ->name('purchaseitems.restock_and_average');

    // เมื่อส่งฟอร์ม (POST) ไปบันทึกการ Restock & เฉลี่ยต้นทุน
    Route::post('/purchaseitems/update-stock', [PurchaseItemController::class, 'updateStock'])
        ->name('purchaseitems.update_stock');

    Route::post('/purchases/store-dynamic', [PurchaseController::class, 'storeDynamic'])
        ->name('purchases.store_dynamic');




    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/withdraw', [StockController::class, 'createWithdrawFromPurchaseItems'])
        ->name('stocks.withdraw_from_purchaseitems');
    Route::post('/stocks/store-withdraw', [StockController::class, 'storeWithdraw'])
        ->name('stocks.store_withdraw');

    // GET ฟอร์ม Withdraw & Stock Process (optionally รับ purchase_item_id เพื่อ pre‐select)
    Route::get('/stocks/process', [StockController::class, 'processForm'])->name('stocks.process');

    // POST บันทึกผลลัพธ์
    Route::post('/stocks/process', [StockController::class, 'processStore'])->name('stocks.process_store');
});

require __DIR__ . '/auth.php';
