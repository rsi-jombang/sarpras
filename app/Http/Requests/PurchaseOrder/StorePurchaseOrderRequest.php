<?php

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canCreatePO();
    }

    public function rules(): array
    {
        return [
            'supplier_name'              => ['nullable', 'string', 'max:200'],
            'supplier_contact'           => ['nullable', 'string', 'max:200'],
            'order_date'                 => ['nullable', 'date'],
            'expected_date'              => ['nullable', 'date', 'after_or_equal:order_date'],
            'notes'                      => ['nullable', 'string', 'max:500'],

            'items'                      => ['required', 'array', 'min:1'],
            'items.*.item_id'            => ['required', 'exists:items,id'],
            'items.*.quantity_ordered'   => ['required', 'integer', 'min:1'],
            'items.*.unit_price'         => ['required', 'numeric', 'min:0'],
            'items.*.batch_number'       => ['nullable', 'string', 'max:100'],
            'items.*.expired_date'       => ['nullable', 'date', 'after:today'],
            'items.*.notes'              => ['nullable', 'string', 'max:255'],
        ];
    }
}
