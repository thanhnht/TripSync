<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'   => 'required|string|max:255',
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Vui lòng nhập họ và tên.',
            'name.max'       => 'Họ tên không được vượt quá 255 ký tự.',
            'phone.max'      => 'Số điện thoại không được vượt quá 20 ký tự.',
            'avatar.image'   => 'File phải là ảnh.',
            'avatar.mimes'   => 'Ảnh phải có định dạng JPG, PNG hoặc WebP.',
            'avatar.max'     => 'Ảnh không được vượt quá 2MB.',
        ];
    }
}
