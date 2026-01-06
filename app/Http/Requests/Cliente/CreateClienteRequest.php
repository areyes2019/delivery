<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;

class CreateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el middleware ya filtra
    }

    public function rules(): array
    {
        return [
            'nombre'          => 'required|string|max:255',
            'slug'            => 'required|string|max:255|unique:clientes,slug',
            'email_contacto'  => 'nullable|email',
            'telefono'        => 'nullable|string|max:20',
            'plan'            => 'nullable|string|max:50',
        ];
    }
}
