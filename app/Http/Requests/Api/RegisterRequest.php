<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class RegisterRequest extends FormRequest
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
        return  [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ];

    }


public function failedValidation(Validator $validator)
{
    $errors = $validator->errors();

    if ($errors->has('email') && str_contains($errors->first('email'), 'has already been taken')) {
        throw new HttpResponseException(response()->json([
            'message' => 'User already exists with this email',
        ], 422));
    }

    throw new HttpResponseException(response()->json([
        'errors' => $errors,
    ], 422));
}

}
