<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchaseitem extends Model
{
    use HasFactory;

     /** Mass assignable fields */
     protected $table = 'purchase_items';
     protected $fillable = [
        'purchase_id',
        'product_name',
        'product_detail1',
        'product_detail2',
        'quantity',
        'price',
    ];

    /** Casts */
    protected $casts = [
        'quantity' => 'integer',
        'price'    => 'decimal:2',
    ];

    /** Relationship: parent purchase */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'purchase_id');
    }
}
