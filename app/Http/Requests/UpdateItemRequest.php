<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:items,code,' . $this->route('item')->id,
            'unit_of_measure' => 'required|string|max:20',
            'item_type' => 'required|in:consumable,inventory',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'name.required' => 'Nama barang harus diisi.',
            'code.required' => 'Kode barang harus diisi.',
            'code.unique' => 'Kode barang sudah digunakan.',
            'unit_of_measure.required' => 'Satuan harus dipilih.',
            'item_type.required' => 'Tipe barang harus dipilih.',
            'item_type.in' => 'Tipe barang tidak valid.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
