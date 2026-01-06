<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateDespachadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el rol se valida en el service
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email'    => 'required|email',
            'phone'    => 'nullable|string|max:50',
            'password' => 'required|string|min:8',
        ];
    }
}
