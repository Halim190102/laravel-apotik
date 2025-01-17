<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ImageRequest extends FormRequest
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
            'profilepict' => 'required|image|mimes:jpg,png,jpeg,webp|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'profilepict.required' => 'The profile picture is required',
            'profilepict.image' => 'The profile picture must be an image',
            'profilepict.mimes' => 'The profile picture must be in jpg, png, jpeg, or webp format',
            'profilepict.max' => 'The profile picture must not exceed 5 MB',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new ValidationException($validator, response()->json($errors));
    }
}
