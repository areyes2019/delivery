<?php

namespace App\Http\Requests\Entrega;

use Illuminate\Foundation\Http\FormRequest;

class ChangeEntregaEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entrega_id' => ['required', 'integer', 'exists:entregas,id'],
            'estado' => [
                'required',
                'in:EN_CAMINO,ENTREGADA',
            ],
        ];
    }
}
