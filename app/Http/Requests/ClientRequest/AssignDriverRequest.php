<?php

namespace App\Http\Requests\ClientRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Entrega\AssignEntregaDriverRequest;

class AssignDriverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|numeric|exists:client_requests,id',
            'id_driver_assigned' => 'required|numeric|exists:users,id',
            'fare_assigned' => 'required|numeric|min:0',
        ];
    }
}
