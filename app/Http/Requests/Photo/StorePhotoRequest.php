<?php

namespace App\Http\Requests\Photo;

use Illuminate\Foundation\Http\FormRequest;

class StorePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photos'            => ['required', 'array', 'min:1', 'max:20'],
            'photos.*'          => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:20240'],
            'description'       => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'photos.required' => 'Vui lòng chọn ít nhất một ảnh.',
            'photos.*.image'  => 'File phải là hình ảnh.',
            'photos.*.mimes'  => 'Chỉ chấp nhận định dạng jpg, jpeg, png, webp, gif.',
            'photos.*.max'    => 'Mỗi ảnh không được vượt quá 10MB.',
        ];
    }
}
