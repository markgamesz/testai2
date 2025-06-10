<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockUnit extends Model
{
    use HasFactory;
    protected $fillable = [
      'restock_id',
      'sale_price',
      'barcode'
    ];

    public function restock()
    {
        return $this->belongsTo(Restock::class);
    }
}
