<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminEmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id'  => 'required|exists:clientes,id',
            'name'        => 'required|string|max:255',
            'lastname'    => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'nullable|string|max:20',
            'password'    => 'required|string|min:8',
        ];
    }
}

