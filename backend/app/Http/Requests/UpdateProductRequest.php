<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Check ownership ở Policy
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive,out_of_stock,pending,rejected,hidden',
            'category_ids' => 'sometimes|array',
            'category_ids.*' => 'exists:categories,id',
        ];
    }
}