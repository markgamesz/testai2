<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $primaryKey = 'purchase_id';
    public $incrementing = true;
    protected $keyType = 'int';

    // ให้ Laravel แปลงชนิดคอลัมน์ให้ตรง
    protected $casts = [
        'insert_date'     => 'datetime',
        'buy_date'        => 'date',
        'vat_status'      => 'boolean',
        'purchase_cost'   => 'decimal:2',
        'vat_amount'    => 'decimal:2',
        'total'  => 'decimal:2',
        //'purchase_vat' => 'float',

    ];

    // ต้องมีชื่อคอลัมน์ทั้งหมดที่คุณจะ mass‑assign
    protected $fillable = [
        'purchase_type',
        'payment_type',
        'partner_id',
        'insert_date',
        'buy_date',
        'purchase_document_number',
        'ref_id',
        'purchase_cost',
        'purchase_vat',
        'total',
        'vat_amount',
        'user_id',
        'branch_id',
        'total_qty',
        'status',
        'deleted_by',
        'branch_id_id',
        'grand_total',
    ];

    public function purchaseitems()
    {
        // เปลี่ยน PurchaseItem::class ให้ตรงกับชื่อโมเดลของคุณ
        return $this->hasMany(Purchaseitem::class, 'purchase_id', 'purchase_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    public function partner()
    {
        // เปลี่ยน PurchaseItem::class ให้ตรงกับชื่อโมเดลของคุณ
        return $this->hasMany(Partner::class, 'part_id', 'partner_id');
    }
    public function partners()
    {
        // เปลี่ยน PurchaseItem::class ให้ตรงกับชื่อโมเดลของคุณ
        return $this->belongsTo(Partner::class, 'partner_id','part_id');
    }
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id', 'purchase_id');
    }
    
}
