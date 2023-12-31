<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'day' => ['date'],
            'intersection' => ['required', 'string'],
            'project_intersection_id' => ['nullable'],
            'approach_name' => ['required', 'string'],
            'weather_condition' => ['required', 'string'],
            'parent_project_id' => ['required', 'exists:parent_projects,id']
        ];
    }
}
