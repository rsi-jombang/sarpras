<?php

namespace App\Http\Requests\StockOpname;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockOpnameItemsRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->isSarpras() || $this->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'items'                          => ['required', 'array', 'min:1'],
            'items.*.opname_item_id'         => ['required', 'exists:stock_opname_items,id'],
            'items.*.physical_quantity'      => ['required', 'integer', 'min:0'],
            'items.*.notes'                  => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.*.physical_quantity.required' => 'Jumlah fisik pada baris :position wajib diisi.',
            'items.*.physical_quantity.min'       => 'Jumlah fisik tidak boleh negatif.',
        ];
    }
}
