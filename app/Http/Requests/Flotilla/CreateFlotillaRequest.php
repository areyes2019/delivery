<?php

namespace App\Http\Requests\Flotilla;

use Illuminate\Foundation\Http\FormRequest;

class CreateFlotillaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el rol se valida en el service
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
        ];
    }
}
