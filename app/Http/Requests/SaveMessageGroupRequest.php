<?php

namespace App\Http\Requests;

use App\Models\MessageGroup;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveMessageGroupRequest extends FormRequest
{
    protected $errorBag = 'messageGroup';

    public function authorize(): bool
    {
        $group = $this->route('group');

        return $this->user()?->is_admin && (! $group || ($group instanceof MessageGroup && $group->isOwnedBy($this->user()) && ! $group->isDirect()));
    }

    protected function prepareForValidation(): void
    {
        if (is_array($this->input('members'))) {
            $this->merge(['members' => array_values(array_filter(
                $this->input('members'), fn (mixed $value): bool => $value !== null && $value !== '',
            ))]);
        }
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', 'not_regex:/[\r\n]/'],
            'members' => [$this->route('group') ? 'present' : 'required', 'array', 'min:'.($this->route('group') ? '0' : '1')],
            'members.*' => ['bail', 'required', 'integer', 'distinct', Rule::exists('users', 'id')
                ->where(fn (Builder $query): Builder => $query->where('is_admin', false))],
            'created_by' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'members.required' => 'Choose at least one team member for your new group.',
            'members.*.exists' => 'Choose an existing team member from User Roles.',
            'members.*.distinct' => 'Choose each member only once.',
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->route('group') ? route('admin.messages.edit', $this->route('group')) : route('admin.messages.create');
    }
}
