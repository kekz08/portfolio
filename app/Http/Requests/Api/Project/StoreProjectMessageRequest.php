<?php

namespace App\Http\Requests\Api\Project;

use App\Http\Requests\Api\ApiFormRequest;

class StoreProjectMessageRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'project_id' => $this->input('project_id') ?? $this->route('project'),
            'message' => is_string($this->input('message')) ? trim($this->input('message')) : $this->input('message'),
        ]);
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'string'],
            'message' => ['required', 'string', 'max:3000'],
        ];
    }
}
