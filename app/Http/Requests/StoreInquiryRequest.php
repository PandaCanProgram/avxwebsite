<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInquiryRequest extends FormRequest
{
    protected $errorBag = 'inquiry';

    public function isInvestment(): bool
    {
        return $this->routeIs('inquiries.investment');
    }

    public function returnUrl(): string
    {
        if ($this->isInvestment()) {
            return route('investment').'#investor-interest';
        }

        return route(match ($this->input('source')) {
            'projects' => 'projects',
            'drone-systems' => 'drone-systems',
            default => 'home',
        }).'#contact';
    }

    protected function getRedirectUrl(): string
    {
        return $this->returnUrl();
    }

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
            'submission_id' => ['required', 'uuid'],
            'source' => ['required', Rule::in($this->isInvestment() ? ['investment'] : ['home', 'projects', 'drone-systems'])],
            'name' => ['required', 'string', 'max:100', 'not_regex:/[\r\n]/'],
            'email' => ['bail', 'required', 'string', 'max:254', 'email:rfc,dns'],
            'organization' => ['nullable', 'string', 'max:150'],
            'subject' => [$this->isInvestment() ? 'prohibited' : 'required', 'string', 'max:160', 'not_regex:/[\r\n]/'],
            'phone' => ['bail', 'nullable', 'string', 'max:40', 'regex:/^\+?(?=(?:\D*\d){7,15}\D*$)[0-9 ().-]+$/D'],
            'interest' => [$this->isInvestment() ? 'required' : 'prohibited', Rule::in(config('inquiries.interest_areas'))],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['prohibited'],
            'inquiry_extra' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.email' => CheckInquiryEmailRequest::INVALID_EMAIL_MESSAGE,
            'phone.regex' => 'Please enter a phone number with 7 to 15 digits. You can include a country code, spaces, parentheses, or hyphens.',
            'website.prohibited' => 'The spam check blocked this submission. Please reload the page and try again.',
            'inquiry_extra.prohibited' => 'The spam check blocked this submission. Please reload the page and try again.',
            'submission_id.required' => 'Please refresh this page and try again.',
            'submission_id.uuid' => 'Please refresh this page and try again.',
            'name.not_regex' => 'Please enter your name on a single line.',
            'subject.not_regex' => 'Please enter your subject on a single line.',
        ];
    }
}
