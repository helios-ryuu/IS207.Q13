<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id; // Lấy ID user hiện tại để trừ ra lúc check trùng

        return [
            'full_name' => 'nullable|string|max:255',
            'phone_number' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($userId)],
            'address' => 'nullable|string|max:500',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
        ];
    }
}