<?php

namespace App\Http\Requests\Flotilla;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFlotillaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|required|string|max:255',
        ];
    }
}
