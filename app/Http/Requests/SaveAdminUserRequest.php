<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveAdminUserRequest extends FormRequest
{
    protected $errorBag = 'userDetails';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $target = $this->route('user');

        return $this->user()?->is_admin && (! $target instanceof User || ! $target->is_admin);
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => strtolower(trim($this->input('email')))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $target = $this->route('user');
        $uniqueEmail = Rule::unique('users', 'email');

        if ($target instanceof User) {
            $uniqueEmail->ignore($target);
        }

        return [
            'name' => ['required', 'string', 'max:100', 'not_regex:/[\r\n]/'],
            'email' => ['required', 'string', 'email:rfc', 'max:254', $uniqueEmail],
            'position' => ['required', 'string', Rule::in(config('admin.positions'))],
            'password' => [$target instanceof User ? 'nullable' : 'required', 'required_with:password_confirmation', 'string', 'min:8', 'max:72', 'confirmed'],
            'is_admin' => ['prohibited'],
            'admin_theme' => ['prohibited'],
        ];
    }

    protected function getRedirectUrl(): string
    {
        $target = $this->route('user');

        return $target instanceof User ? route('admin.users.edit', $target) : route('admin.users.create');
    }
}
