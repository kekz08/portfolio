<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HomeContentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'array'],
            'content.sections' => ['required', 'array', 'min:1'],
            'content.navbar' => ['required', 'array'],
            'content.sidebar' => ['required', 'array'],
            'content.hero' => ['required', 'array'],
            'content.about' => ['required', 'array'],
            'content.services' => ['required', 'array'],
            'content.skills' => ['required', 'array'],
            'content.facts' => ['required', 'array'],
            'content.experience' => ['required', 'array'],
            'content.projects' => ['required', 'array'],
            'content.process' => ['required', 'array'],
            'content.blogs' => ['required', 'array'],
            'content.testimonials' => ['required', 'array'],
            'content.contact' => ['required', 'array'],
            'content.footer' => ['required', 'array'],
        ];
    }
}
