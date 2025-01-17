<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

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
        return [
            'name' => 'required|string',
            'profilepict' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'email' => 'required|string|email:filter|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name is required',
            'email.required' => 'The email is required',
            'email.email' => 'The email is not valid',
            'email.unique' => 'The email is already registered',
            'password.required' => 'The password is required',
            'password.min' => 'The password must be at least 8 characters long',
            'password.confirmed' => 'The password confirmation does not match',
            'profilepict.image' => 'The profile picture must be an image',
            'profilepict.mimes' => 'The profile picture must be in jpg, png, jpeg, or webp format',
            'profilepict.max' => 'The profile picture must not exceed 5 MB',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new ValidationException($validator, response()->json($errors));
    }
}
