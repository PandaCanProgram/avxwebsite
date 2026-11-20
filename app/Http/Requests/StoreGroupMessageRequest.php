<?php

namespace App\Http\Requests;

use App\Models\MessageGroup;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGroupMessageRequest extends FormRequest
{
    protected $errorBag = 'groupMessage';

    public function authorize(): bool
    {
        $group = $this->route('group');

        return $this->user() && $group instanceof MessageGroup && $group->isOwnedBy($this->user());
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
            'client_id' => ['required', 'uuid'],
            'user_id' => ['prohibited'],
            'message_group_id' => ['prohibited'],
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('admin.messages.show', $this->route('group'));
    }
}
