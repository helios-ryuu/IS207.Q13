<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép mọi user đã đăng nhập đều được dùng
    }

    public function rules(): array
    {
        return [
            // Kiểm tra receiver_id phải tồn tại trong bảng users cột id
            'receiver_id' => 'required|exists:users,id', 
            'content'     => 'required|string|max:1000',
        ];
    }
}