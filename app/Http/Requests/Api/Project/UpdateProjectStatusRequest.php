<?php

namespace App\Http\Requests\Api\Project;

use App\Enums\ProjectStatus;
use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateProjectStatusRequest extends ApiFormRequest
{
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

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'string'],
            'status' => ['required', new Enum(ProjectStatus::class)],
            'status_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
