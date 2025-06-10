<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    // ถ้าใช้ชื่อตารางมาตรฐาน 'stocks' สามารถไม่ต้องระบุ $table
    protected $table = 'stocks';

    // ถ้า primary key ใช้ id ตาม default ก็ไม่ต้องระบุ $primaryKey
    // protected $primaryKey = 'id';

    // กำหนดฟิลด์ที่อนุญาตให้ mass assign
    protected $fillable = [
        'product_id',
        'quantity',
        'avg_cost',
        'purchase_item_id',
        'unit_price',
        'sale_total',
        'unit_cost',
        'total_cost',
        'margin_pct',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity'   => 'integer',
        'unit_price' => 'decimal:2',
        'sale_total' => 'decimal:2',
        'unit_cost'  => 'decimal:2',
        'total_cost' => 'decimal:2',
        'margin_pct' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(PurchaseItem::class, 'product_id', 'id');
    }
    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function product2()
    {
        return $this->belongsTo(Purchase::class);
    }

    use HasFactory;
}
