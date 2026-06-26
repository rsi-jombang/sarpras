<?php

namespace App\Http\Requests\StockAdjustment;

use App\Models\StockAdjustment;
use Illuminate\Foundation\Http\FormRequest;

class StoreStockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', StockAdjustment::class);
    }

    public function rules(): array
    {
        return [
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'quantity_adjustment' => ['required', 'integer'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Item wajib dipilih',
            'item_id.exists' => 'Item tidak ditemukan',
            'quantity_adjustment.required' => 'Jumlah penyesuaian wajib diisi',
            'quantity_adjustment.integer' => 'Jumlah penyesuaian harus angka',
            'reason.required' => 'Alasan penyesuaian wajib diisi',
        ];
    }
}
