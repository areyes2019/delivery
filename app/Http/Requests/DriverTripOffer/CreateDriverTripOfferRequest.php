<?php

namespace App\Http\Requests\DriverTripOffer;

use Illuminate\Foundation\Http\FormRequest;

class CreateDriverTripOfferRequest extends FormRequest
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
            'id_driver' => 'required|exists:users,id',
            'id_client_request' => 'required|exists:client_requests,id',
            'fare_offered' => 'required|numeric|min:0',
            'time' => 'required|numeric|min:0',
            'distance' => 'required|numeric|min:0',
        ];
    }
}
