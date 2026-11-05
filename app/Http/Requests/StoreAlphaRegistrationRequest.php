<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlphaRegistrationRequest extends FormRequest
{
    protected $errorBag = 'alphaRegistration';

    public function authorize(): bool
    {
        abort_unless(config('inquiries.alpha_registration_enabled'), 404);

        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('alpha_name'))) {
            $this->merge(['alpha_name' => trim($this->input('alpha_name'))]);
        }

        if (is_string($this->input('alpha_email'))) {
            $this->merge(['alpha_email' => strtolower(trim($this->input('alpha_email')))]);
        }
    }

    /** @return array<string, array<mixed>> */
    public function rules(): array
    {
        return [
            'alpha_name' => ['required', 'string', 'max:100', 'not_regex:/[\r\n]/'],
            'alpha_email' => ['bail', 'required', 'string', 'max:254', 'email:rfc,dns'],
            'source' => ['required', Rule::in(['home', 'projects', 'investment', 'drone-systems'])],
            'alpha_website' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'alpha_name.required' => 'Please enter your name.',
            'alpha_name.not_regex' => 'Please enter your name on a single line.',
            'alpha_email.required' => 'Please enter your email address.',
            'alpha_email.email' => 'Please enter a valid email address that can receive an invitation.',
            'alpha_website.prohibited' => 'The spam check blocked this submission. Please reload the page and try again.',
        ];
    }

    public function returnUrl(): string
    {
        return route(match ($this->input('source')) {
            'projects' => 'projects',
            'investment' => 'investment',
            'drone-systems' => 'drone-systems',
            default => 'home',
        }).'#alpha-registration';
    }

    protected function getRedirectUrl(): string
    {
        return $this->returnUrl();
    }
}
