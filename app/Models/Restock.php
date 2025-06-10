<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restock extends Model
{
    use HasFactory;
    protected $table = 'restock';
    protected $fillable = [
      'purchase_item_id',
      'restock_qty',
      'restock_total',
      'split_count',
      'user_id','restocked_at'
    ];

    protected $casts = [
        'restocked_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(PurchaseItem::class,'purchase_item_id');
    }

    public function units()
    {
        return $this->hasMany(RestockUnit::class);
    }

    
    
}
