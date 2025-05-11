<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateRequest extends FormRequest
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
        // Assuming 'employee_id' is the unique identifier for each employee.
        $employeeId = $this->route('employee_id'); // Get the employee_id ID from the URL route

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_no' => 'required|numeric|digits:10',
            'email' => 'required|email|unique:users,email,' . $employeeId,  // Ensure email is unique except for the current employee
            'address' => 'nullable|string|max:255',
            'manager' => 'nullable|exists:users,id', // Only managers that exist in the users table
        ];
    }

    /**
     * Get the custom error messages for the validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'phone_no.required' => 'Phone number is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'The email address is already taken.',
            'manager.exists' => 'The selected manager is invalid.',
        ];
    }
}
