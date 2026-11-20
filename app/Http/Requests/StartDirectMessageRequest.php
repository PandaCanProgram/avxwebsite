<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StartDirectMessageRequest extends FormRequest
{
    protected $errorBag = 'directMessage';

    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'recipient_id' => ['bail', 'required', 'integer', Rule::exists('users', 'id')
                ->where(fn (Builder $query): Builder => $query->where('is_admin', false))],
            'created_by' => ['prohibited'],
            'members' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'recipient_id.required' => 'Choose a user to message.',
            'recipient_id.exists' => 'Choose an existing team member from User Roles.',
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('admin.messages.direct.create');
    }
}
