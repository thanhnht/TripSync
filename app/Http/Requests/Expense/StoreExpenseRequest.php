<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'amount' => $this->amount ?? 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'amount'           => ['required', 'integer', 'min:10000'],
            'paid_by'          => ['required', 'integer', 'exists:users,id'],
            'split_method'     => ['required', 'in:equal,custom'],
            'note'             => ['nullable', 'string', 'max:1000'],
            'splits'           => ['required_if:split_method,custom', 'array'],
            'splits.*.user_id' => ['required_if:split_method,custom', 'integer', 'exists:users,id'],
            'splits.*.amount'  => ['required_if:split_method,custom', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Số tiền tối thiểu là 10.000 ₫.',
        ];
    }
}
