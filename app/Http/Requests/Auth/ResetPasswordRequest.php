<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'     => 'Vui lòng nhập email.',
            'email.email'        => 'Email không hợp lệ.',
            'password.required'  => 'Vui lòng nhập mật khẩu mới.',
            'password.min'       => 'Mật khẩu tối thiểu 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ];
    }
}
