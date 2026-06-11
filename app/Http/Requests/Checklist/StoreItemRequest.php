<?php

namespace App\Http\Requests\Checklist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $trip   = $this->route('trip');
        $tripId = is_object($trip) ? $trip->id : (int) $trip;

        return [
            'content' => [
                'required', 'string', 'max:255',
                Rule::unique('checklist_items', 'content')
                    ->where('trip_id', $tripId),
            ],
            'category'    => ['nullable', 'string', 'max:100'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.unique' => 'Mục này đã tồn tại trong checklist.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'content'  => mb_strtolower(trim($this->input('content') ?? '')),
            'category' => trim($this->input('category') ?? '') ?: 'Chung',
        ]);
    }
}
