<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminAppearanceRequest extends FormRequest
{
    protected $errorBag = 'appearanceUpdate';

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
            'theme' => ['required', 'string', Rule::in(['light', 'dark'])],
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('admin.settings').'#appearance';
    }
}
