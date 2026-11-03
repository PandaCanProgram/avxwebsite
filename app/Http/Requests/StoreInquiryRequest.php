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

        return route($this->input('source') === 'projects' ? 'projects' : 'home').'#contact';
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
            'source' => ['required', Rule::in($this->isInvestment() ? ['investment'] : ['home', 'projects'])],
            'name' => ['required', 'string', 'max:100', 'not_regex:/[\r\n]/'],
            'email' => ['required', 'string', 'email:rfc', 'max:254'],
            'organization' => ['nullable', 'string', 'max:150'],
            'subject' => [$this->isInvestment() ? 'prohibited' : 'required', 'string', 'max:160', 'not_regex:/[\r\n]/'],
            'phone' => [$this->isInvestment() ? 'nullable' : 'prohibited', 'string', 'max:40'],
            'interest' => [$this->isInvestment() ? 'required' : 'prohibited', Rule::in(config('inquiries.interest_areas'))],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'website.prohibited' => 'Your inquiry could not be submitted.',
            'submission_id.required' => 'Please refresh this page and try again.',
            'submission_id.uuid' => 'Please refresh this page and try again.',
            'name.not_regex' => 'Please enter your name on a single line.',
            'subject.not_regex' => 'Please enter your subject on a single line.',
        ];
    }
}
