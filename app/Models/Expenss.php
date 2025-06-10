<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenss extends Model
{
    use HasFactory;

    protected $primaryKey = 'expen_id';

    protected $fillable = [
        'expen_name',
        'insert_date',
        'buy_date',
        'expen_cost',
        'expen_vat',
        'expen_total',
        'remark',
        'expen_sub_id',
    ];

    // กำหนดความสัมพันธ์กับ SubExpenss (ถ้ามี)
    public function subExpenss()
    {
        return $this->belongsTo(SubExpenss::class, 'expen_sub_id', 'expen_sub_id');
    }
}
