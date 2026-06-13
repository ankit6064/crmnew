<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeRequest extends FormRequest
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
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'phone_no' => 'required|numeric',
            'email' => 'required|email|unique:users,email',
            'address' => 'required|string|max:255',
            'manager' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get custom error messages for validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.regex' => 'First name must contain only letters and spaces.',
            'last_name.required' => 'Last name is required.',
            'last_name.regex' => 'Last name must contain only letters and spaces.',
            'phone_no.required' => 'Phone number is required.',
            'email.required' => 'Email is required.',
            'address.required' => 'Address is required.',
            'manager.exists' => 'The selected manager is invalid.',
        ];
    }
}
