<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubExpenss extends Model
{
    use HasFactory;

    protected $primaryKey = 'expen_sub_id';

    protected $fillable = [
        'expen_sub_name',
        'expen_sub_type',
        'district',
        'subdistrict',
        'province',
        'zipcode',
        'phone',
    ];
}
