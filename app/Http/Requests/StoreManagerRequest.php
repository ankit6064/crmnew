<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManagerRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_no' => 'required|numeric|digits:10',
            'email' => 'required|email',
            'address' => 'required|string|min:3|max:255',
            'manager_type' => 'required|in:1,2'
        ];
    }


    /**
     * Custom messages for validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => 'First Name is required.',
            'last_name.required' => 'Last Name is required.',
            'phone_no.required' => 'Phone Number is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'The email address is already taken.',
            'manager_type.required' => 'Manager Type is required.',
        ];
    }
}
