<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveJobOrderRequest extends FormRequest
{
    protected $errorBag = 'jobOrder';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (is_array($this->input('assignees'))) {
            $this->merge(['assignees' => array_values(array_filter(
                $this->input('assignees'), fn (mixed $value): bool => $value !== null && $value !== '',
            ))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160', 'not_regex:/[\r\n]/'],
            'description' => ['required', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date_format:Y-m-d'],
            'assignees' => ['required', 'array', 'min:1'],
            'assignees.*' => ['bail', 'required', 'integer', 'distinct', Rule::exists('users', 'id')
                ->where(fn (Builder $query): Builder => $query->where('is_admin', false))],
            'created_by' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'assignees.required' => 'Choose at least one user for this job order.',
            'assignees.min' => 'Choose at least one user for this job order.',
            'assignees.*.distinct' => 'Choose each participant only once.',
            'assignees.*.exists' => 'Choose an existing team member from User Roles.',
        ];
    }

    protected function getRedirectUrl(): string
    {
        $jobOrder = $this->route('job_order');

        return $jobOrder ? route('admin.job-orders.edit', $jobOrder) : route('admin.job-orders.create');
    }
}
