<?php

namespace App\Http\Requests\ClientRequest;

use Illuminate\Foundation\Http\FormRequest;

class CreateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 📍 POSICIONES
            'pickup_position' => ['required', 'array'],
            'pickup_position.lat' => ['required', 'numeric', 'between:-90,90'],
            'pickup_position.lng' => ['required', 'numeric', 'between:-180,180'],

            'destination_position' => ['required', 'array'],
            'destination_position.lat' => ['required', 'numeric', 'between:-90,90'],
            'destination_position.lng' => ['required', 'numeric', 'between:-180,180'],

            // 📞 REMITENTE
            'remitente_nombre' => ['required', 'string', 'max:255'],
            'remitente_telefono' => ['required', 'string', 'max:20'],

            // 📦 DESTINATARIO
            'destinatario_nombre' => ['required', 'string', 'max:255'],
            'destinatario_telefono' => ['required', 'string', 'max:20'],

            // 📝 DESCRIPCIONES
            'pickup_description' => ['nullable', 'string', 'max:255'],
            'destination_description' => ['nullable', 'string', 'max:255'],

            // 💰 COSTOS
            'fare_offered' => ['nullable', 'numeric', 'min:0'],
            'product_amount' => ['nullable', 'numeric', 'min:0'],

            // 📝 EXTRA
            'observaciones' => ['nullable', 'string'],
        ];
    }
}
