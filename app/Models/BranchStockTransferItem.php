<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchStockTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_id', 'stock_id',
    ];

    public function transfer()
    {
        return $this->belongsTo(BranchStockTransfer::class, 'transfer_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }
}
