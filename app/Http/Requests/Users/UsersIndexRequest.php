<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UsersIndexRequest extends FormRequest
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
            'per_page' => ['required', 'integer'],
            'page' => ['required', 'integer'],
            'search' => ['nullable'],
            'active' => ['nullable'],
            'is_admin' => ['nullable'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'per_page' => $this->per_page ?? 10,
            'page' => $this->page ?? 1,
            'active' => $this->active ?? "",
            'is_admin' => $this->is_admin ?? "",
            'search' => $this->search ?? "",
        ]);
    }
}
