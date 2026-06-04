<?php

namespace App\Http\Requests\Request;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isUnit();
    }

    public function rules(): array
    {
        return [
            'needed_date'                => ['nullable', 'date', 'after_or_equal:today'],
            'priority'                   => ['required', 'in:low,normal,high,urgent'],
            'purpose'                    => ['nullable', 'string', 'max:500'],
            'notes'                      => ['nullable', 'string', 'max:500'],

            'items'                      => ['required', 'array', 'min:1'],
            'items.*.item_id'            => ['required', 'exists:items,id'],
            'items.*.quantity_requested' => ['required', 'integer', 'min:1', 'max:9999'],
            'items.*.notes'              => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'                      => 'Minimal satu barang harus ditambahkan.',
            'items.*.item_id.required'            => 'Barang pada baris :position tidak valid.',
            'items.*.item_id.exists'              => 'Barang pada baris :position tidak ditemukan.',
            'items.*.quantity_requested.required' => 'Jumlah pada baris :position wajib diisi.',
            'items.*.quantity_requested.min'      => 'Jumlah minimal 1.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Pastikan tidak ada duplikat item_id dalam satu permintaan
        if ($this->has('items')) {
            $itemIds = collect($this->items)->pluck('item_id')->filter()->toArray();
            if (count($itemIds) !== count(array_unique($itemIds))) {
                $this->merge(['_duplicate_items' => true]);
            }
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->_duplicate_items ?? false) {
                $validator->errors()->add('items', 'Terdapat barang yang sama dalam satu permintaan. Gabungkan jumlahnya.');
            }
        });
    }
}
