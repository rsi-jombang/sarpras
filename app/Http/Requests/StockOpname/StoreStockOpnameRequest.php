<?php

namespace App\Http\Requests\StockOpname;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockOpnameRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->isSarpras() || $this->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'opname_date' => ['required', 'date', 'before_or_equal:today'],
            'notes'       => ['nullable', 'string', 'max:500'],
        ];
    }
}
