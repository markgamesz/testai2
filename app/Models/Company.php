<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $table = 'company';
    protected $primaryKey = 'com_id';

    protected $fillable = [
        'com_name',
        'com_detial',
        'com_vatnum',
        'com_statusvat',
        'com_branchid',
        'com_branchname',
        'district',
        'subdistrict',
        'province',
        'zipcode',
        'phone',
        'updated_by',
    ];
}
