<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchStockTransfer extends Model
{
    use HasFactory;


    protected $fillable = [
        'from_branch_id',
        'to_branch_id',
        'created_by',
        'transferred_at',
    ];

    public function items()
    {
        return $this->hasMany(BranchStockTransferItem::class, 'transfer_id');
    }

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function product()
    {
        return $this->belongsTo(Stock::class);
    }
}
