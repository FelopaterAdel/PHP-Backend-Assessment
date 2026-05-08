<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('id');

        return [
            'sku' => 'sometimes|required|string|max:255|unique:products,sku,' . $productId,
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0|max:999999.99',
            'stock_quantity' => 'sometimes|required|integer|min:0|max:999999',
            'low_stock_threshold' => 'sometimes|required|integer|min:0|max:999999',
            'status' => 'sometimes|required|in:active,inactive',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sku.required' => 'Product SKU is required.',
            'sku.unique' => 'This SKU already exists. Please use a unique SKU.',
            'sku.max' => 'SKU cannot exceed 255 characters.',
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'price.required' => 'Product price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price cannot be negative.',
            'price.max' => 'Price cannot exceed 999999.99.',
            'stock_quantity.required' => 'Stock quantity is required.',
            'stock_quantity.integer' => 'Stock quantity must be a whole number.',
            'stock_quantity.min' => 'Stock quantity cannot be negative.',
            'low_stock_threshold.required' => 'Low stock threshold is required.',
            'low_stock_threshold.integer' => 'Low stock threshold must be a whole number.',
            'status.required' => 'Product status is required.',
            'status.in' => 'Status must be either "active" or "inactive".',
        ];
    }
}
