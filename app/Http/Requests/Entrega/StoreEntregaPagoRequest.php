<?php

namespace App\Http\Requests\Entrega;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntregaPagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // autorización fina luego (policy)
    }

    public function rules(): array
    {
        return [
            'payment_method'  => 'required|in:CASH,CARD',
            'amount_received' => 'required|numeric|min:0',
            'reference'       => 'nullable|string|max:255',
        ];
    }
}
