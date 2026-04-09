<?php

namespace App\Http\Requests\Project;

use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ProjectStatusUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $statusNotes = $this->input('status_notes');
        $normalizedStatusNotes = is_string($statusNotes) ? trim($statusNotes) : $statusNotes;

        $this->merge([
            'project_id' => $this->input('project_id') ?? $this->route('project'),
            'status_notes' => $normalizedStatusNotes === '' ? null : $normalizedStatusNotes,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'string'],
            'status' => ['required', new Enum(ProjectStatus::class)],
            'status_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
