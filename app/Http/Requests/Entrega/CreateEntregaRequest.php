<?php

namespace App\Http\Requests\Entrega;

use Illuminate\Foundation\Http\FormRequest;

class CreateEntregaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            
            'remitente_nombre' => 'required|string',
            'remitente_telefono' => 'required|string',

            'destinatario_nombre' => 'required|string',
            'destinatario_telefono' => 'required|string',

            'origen_direccion' => 'required|string',
            'destino_direccion' => 'required|string',

            'origen_lat' => 'required|numeric',
            'origen_lng' => 'required|numeric',
            'destino_lat' => 'required|numeric',
            'destino_lng' => 'required|numeric',

            'observaciones' => 'nullable|string',
        ];
    }

}
