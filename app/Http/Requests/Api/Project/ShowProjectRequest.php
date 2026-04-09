<?php

namespace App\Http\Requests\Api\Project;

use App\Http\Requests\Api\ApiFormRequest;

class ShowProjectRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'project_id' => $this->input('project_id') ?? $this->route('project'),
        ]);
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'string'],
        ];
    }
}
