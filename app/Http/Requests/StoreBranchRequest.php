<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize()
    {
        return true; // เปลี่ยนตามเงื่อนไขการอนุญาตของคุณ
    }

    /**
     * ตัดช่องว่างด้านท้ายของทุกฟิลด์ก่อน validate
     */
    protected function prepareForValidation()
    {
        // รายชื่อฟิลด์ที่ต้องการ trim
        $fieldsToTrim = [
            'branch_id',
            'branch_name',
            'branch_address',
            'district',
            'subdistrict',
            'province',
            'zipcode',
            'phone',
            'updated_by',
        ];

        $trimmed = [];
        foreach ($fieldsToTrim as $field) {
            // ใช้ rtrim() ตัดช่องว่างด้านท้าย
            $value = $this->input($field, '');
            $trimmed[$field] = is_string($value)
                ? rtrim($value)
                : $value;
        }

        $this->merge($trimmed);
    }

    /**
     * กำหนดกฎการ validate
     */
    public function rules()
    {
        return [
            'branch_id'      => 'required|string|size:5|unique:branches,branch_id',
            'branch_name'    => 'required|string|max:100',
            'branch_address' => 'required|string|max:255',
            'district'       => 'required|string|max:100',
            'subdistrict'    => 'required|string|max:100',
            'province'       => 'required|string|max:100',
            'zipcode'        => 'required|string|size:5',      // ถ้ารหัสไปรษณีย์ 5 หลัก
            'phone'          => ['required', 'string', 'max:20', 'regex:/^[0-9\-\+\s]+$/'],
            'is_active'      => 'required|boolean',
            'updated_by'     => 'required|integer|exists:users,id',
        ];
    }

    /**
     * (ไม่บังคับ) ข้อความ error แบบ custom
     */
   
}
