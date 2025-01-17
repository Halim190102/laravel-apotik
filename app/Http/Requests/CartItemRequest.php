<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CartItemRequest extends FormRequest
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
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'product_id' => $isUpdate ? 'nullable' : 'required|exists:products,id',
            'qty' => $isUpdate ? 'nullable|integer|min:1' : 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'The product id is required.',
            'product_id.exists' => 'The selected product does not exist in our records.',
            'qty.required' => 'The quantity is required.',
            'qty.min' => 'The quantity must be at least 1.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new ValidationException($validator, response()->json($errors));
    }
}
