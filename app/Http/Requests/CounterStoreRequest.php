<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CounterStoreRequest extends FormRequest
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
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
            'interval' => ['required','integer'],
            'day' => ['date'],
            'intersection' => ['required', 'string'],
            'approach_name' => ['required', 'string'],
            'weather_condition' => ['required', 'string'],
        ];
    }
}
