<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminPasswordRequest extends FormRequest
{
    protected $errorBag = 'passwordUpdate';

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
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => ['required', 'string', 'min:12', 'max:72', 'confirmed', 'different:current_password'],
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('admin.settings').'#password';
    }
}
