<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cho phép mọi user đã đăng nhập
    }

    public function rules()
    {
        return [
            // Validate thông tin địa chỉ (province bắt buộc, district/ward có thể trống)
            'receiver_name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:15',
            'street_address' => 'required|string|max:255',
            'province' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',

            // Validate đơn hàng
            'payment_method' => 'required|string|in:cash,bank_transfer,wallet,credit_card',
            'notes' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'receiver_name.required' => 'Vui lòng nhập tên người nhận.',
            'phone_number.required' => 'Vui lòng nhập số điện thoại.',
            'street_address.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
        ];
    }
}