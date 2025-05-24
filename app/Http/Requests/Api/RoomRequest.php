<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'number' => 'required|string|unique:rooms,number',
            'type' => 'required|in:single,double,suite',
            'price_per_night' => 'required|numeric|min:0',
            'status' => 'required|in:available,unavailable',
        ];

        if ($this->isMethod('PATCH') || $this->isMethod('PUT')) {
            $rules['number'] = 'string|unique:rooms,number,' . $this->route('room');
            $rules['type'] = 'in:single,double,suite';
            $rules['price_per_night'] = 'numeric|min:0';
            $rules['status'] = 'in:available,unavailable';
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
