<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateManagerRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email,' . $this->route('manager_id'),
            'address' => 'nullable|string|max:255',
            'manager_type' => 'required|in:1,2'
        ];
    }


    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'phone_no.required' => 'Phone number is required.',
            'phone_no.numeric' => 'Phone number must be a valid number.',
            'phone_no.digits' => 'Phone number must be 10 digits.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already taken.',
            'manager_type.required' => 'Manager type is required.',
            'manager_type.in' => 'Manager type must be either "Internal" or "External".',
        ];
    }
}
