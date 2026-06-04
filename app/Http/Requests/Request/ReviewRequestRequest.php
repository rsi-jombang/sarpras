<?php

namespace App\Http\Requests\Request;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canApproveRequest();
    }

    public function rules(): array
    {
        $isApproved = in_array($this->action, ['approve', 'partial']);

        return [
            'action'       => ['required', 'in:approve,partial,reject'],
            'review_notes' => ['nullable', 'string', 'max:500'],

            // Wajib ada detail per item saat approve/partial
            'items'                      => [$isApproved ? 'required' : 'nullable', 'array'],
            'items.*.request_item_id'    => [$isApproved ? 'required' : 'nullable', 'exists:request_items,id'],
            'items.*.quantity_approved'  => [$isApproved ? 'required' : 'nullable', 'integer', 'min:0'],
            'items.*.rejection_reason'   => ['nullable', 'string', 'max:255'],

            // Wajib ada alasan jika reject
            'review_notes' => [
                $this->action === 'reject' ? 'required' : 'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required'      => 'Aksi review wajib dipilih (setujui / tolak).',
            'review_notes.required'=> 'Alasan penolakan wajib diisi.',
        ];
    }
}
