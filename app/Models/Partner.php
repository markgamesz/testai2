<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $primaryKey = 'part_id';
    public $incrementing = false;
    // ระบุประเภทของ primary key เป็น string

    protected $keyType = 'string';
    protected $fillable = [
        'part_id',
        'part_name',
        'part_detial',
        'district',
        'subdistrict',
        'province',
        'zipcode',
        'part_vatnum',
        'part_vatstatus',
        'phone',
        'type',
        'partner_type',
    ];
}
