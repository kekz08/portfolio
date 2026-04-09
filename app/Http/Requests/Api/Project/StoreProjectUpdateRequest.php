<?php

namespace App\Http\Requests\Api\Project;

use App\Http\Requests\Api\ApiFormRequest;

class StoreProjectUpdateRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'project_id' => $this->input('project_id') ?? $this->route('project'),
            'title' => is_string($this->input('title')) ? trim($this->input('title')) : $this->input('title'),
            'description' => is_string($this->input('description')) ? trim($this->input('description')) : $this->input('description'),
        ]);
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'string'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:3000'],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }
}
