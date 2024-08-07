<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // İsteğin yetkili olup olmadığını kontrol eder
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
            'nullable',
            'email',
            'regex:/^[\w\.-]+@(example\.com|example\.org|example\.net)$/i'
        ],
            'phone' => 'required|string|max:15|regex:/^[0-9+\(\)#\.\s\/ext-]+$/',
            'company_id' => 'required|exists:companies,id',
        ];
    }
    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid phone number',
            'email.regex' => 'Please enter a valid email address',
        ];
    }
}
