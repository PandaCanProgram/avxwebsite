<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWebsiteMaintenanceRequest extends FormRequest
{
    protected $errorBag = 'maintenanceUpdate';

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
            'maintenance_enabled' => ['required', 'boolean'],
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('admin.settings').'#maintenance';
    }
}
