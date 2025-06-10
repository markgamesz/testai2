<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    // กำหนด primary key เป็น branch_id
    protected $primaryKey = 'branch_id';
    public $incrementing = false; // เพราะเราใช้ string ไม่ใช่ integer

    protected $keyType = 'string';

    protected $fillable = [
        'branch_id',
        'com_id',
        'branch_name',
        'branch_address',
        'district',
        'subdistrict',
        'province',
        'zipcode',
        'phone',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** คนที่อัปเดตล่าสุด */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

