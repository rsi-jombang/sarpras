<?php

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class ReceivePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canCreatePO();
    }

    public function rules(): array
    {
        return [
            'received_date'                => ['required', 'date', 'before_or_equal:today'],
            'notes'                        => ['nullable', 'string', 'max:500'],

            'items'                        => ['required', 'array', 'min:1'],
            'items.*.po_item_id'           => ['required', 'exists:purchase_order_items,id'],
            'items.*.quantity_received'    => ['required', 'integer', 'min:0'],
            'items.*.batch_number'         => ['nullable', 'string', 'max:100'],
            'items.*.expired_date'         => ['nullable', 'date'],
            'items.*.unit_price'           => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'received_date.required' => 'Tanggal penerimaan barang wajib diisi.',
            'items.*.quantity_received.min' => 'Jumlah diterima tidak boleh negatif.',
        ];
    }
}
