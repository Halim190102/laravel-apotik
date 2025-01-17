<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ProductRequest extends FormRequest
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
            'category_id' => $isUpdate ? 'nullable|exists:categories,id' : 'required|exists:categories,id',
            'name' => $isUpdate ? 'nullable|string' : 'required|string',
            'description' => $isUpdate ? 'nullable|string' : 'required|string',
            'price' => $isUpdate ? 'nullable|integer' : 'required|integer',
            'stock' => $isUpdate ? 'nullable|integer' : 'required|integer',
            'picture' => $isUpdate ? 'nullable|image|mimes:jpg,png,jpeg,webp|max:10240' : 'nullable|image|mimes:jpg,png,jpeg,webp|max:10240',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'The category id is required.',
            'category_id.exists' => 'The selected category does not exist in our records.',
            'name.required' => 'The name is required.',
            'description.required' => 'The description is required.',
            'price.required' => 'The price is required.',
            'stock.required' => 'The stock is required.',
            'name.required' => 'The name is required.',
            'picture.image' => 'The picture must be an image',
            'picture.mimes' => 'The picture must be in jpg, png, jpeg, or webp format',
            'picture.max' => 'The picture must not exceed 10 MB',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new ValidationException($validator, response()->json($errors));
    }
}
