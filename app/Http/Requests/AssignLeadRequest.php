<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignLeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'employee_id' => 'required',
            'lead_id' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'Please Select a Employee.',
            'lead_id.required' => 'Please Select a Lead.',
        ];
    }
}
