<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'         => 'sometimes|required|string|max:255',
            'slug'           => 'sometimes|required|string|max:255|unique:clientes,slug,' . $this->route('id'),
            'email_contacto' => 'nullable|email|max:255',
            'telefono'       => 'nullable|string|max:20',
            'plan'           => 'nullable|string|max:50',
            'activo'         => 'boolean',
        ];
    }

}
