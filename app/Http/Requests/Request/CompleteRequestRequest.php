<?php

namespace App\Http\Requests\Request;

use Illuminate\Foundation\Http\FormRequest;

class CompleteRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canApproveRequest();
    }

    public function rules(): array
    {
        return [
            'received_by_name' => ['required', 'string', 'max:100'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'received_by_name.required' => 'Nama petugas yang mengambil barang wajib diisi.',
        ];
    }
}
