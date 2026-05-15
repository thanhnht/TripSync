<?php

namespace App\Http\Requests\Trip;

use Illuminate\Foundation\Http\FormRequest;

class JoinTripRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'invite_code' => 'required|string|size:8',
        ];
    }

    public function messages(): array
    {
        return [
            'invite_code.required' => 'Vui lòng nhập mã mời.',
            'invite_code.size'     => 'Mã mời phải gồm đúng 8 ký tự.',
        ];
    }
}
