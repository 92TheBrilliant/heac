<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[\d\s\+\-\(\)]+$/', // Allow digits, spaces, +, -, (, )
            ],
            'subject' => [
                'nullable',
                'string',
                'max:255',
            ],
            'message' => [
                'required',
                'string',
                'max:5000',
                'min:10',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.min' => 'Your name must be at least 2 characters.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'phone.regex' => 'Please enter a valid phone number.',
            'message.required' => 'Please enter your message.',
            'message.min' => 'Your message must be at least 10 characters.',
            'message.max' => 'Your message cannot exceed 5000 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'email' => 'email address',
            'phone' => 'phone number',
            'subject' => 'subject',
            'message' => 'message',
        ];
    }
}
