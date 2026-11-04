<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckInquiryEmailRequest extends FormRequest
{
    public const INVALID_EMAIL_MESSAGE = 'Please check your email address. Its format may be incorrect, or its domain cannot receive email.';

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['bail', 'required', 'string', 'max:254', 'email:rfc,dns'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['email.email' => self::INVALID_EMAIL_MESSAGE];
    }
}
