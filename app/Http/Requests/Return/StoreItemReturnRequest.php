<?php

namespace App\Http\Requests\ReturnRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isUnit() || $this->user()->isSarpras() || $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'unit_id'  => ['nullable', 'integer', 'exists:units,id'],
            'reason'   => ['nullable', 'string', 'max:100'],
            'notes'    => ['nullable', 'string', 'max:500'],
            'items'    => ['required', 'array', 'min:1'],
            'items.*.item_id'          => ['required', 'exists:items,id'],
            'items.*.request_item_id'  => ['nullable', 'exists:request_items,id'],
            'items.*.quantity_returned' => ['required', 'integer', 'min:1'],
            'items.*.condition'        => ['nullable', 'in:usable,damaged'],
            'items.*.notes'            => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'              => 'Minimal 1 barang harus ditambahkan.',
            'items.min'                   => 'Minimal 1 barang harus ditambahkan.',
            'items.*.item_id.required'    => 'Barang wajib dipilih.',
            'items.*.quantity_returned.required' => 'Jumlah pengembalian wajib diisi.',
            'items.*.quantity_returned.min'      => 'Jumlah pengembalian minimal 1.',
        ];
    }
}