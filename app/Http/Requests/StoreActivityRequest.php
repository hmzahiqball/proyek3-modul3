<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:30', 'unique:activities,code'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'poster' => ['nullable', 'image', 'max:2048'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
        ];
    }
}
