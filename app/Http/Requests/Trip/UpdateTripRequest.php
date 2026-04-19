<?php

namespace App\Http\Requests\Trip;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'destination' => 'required|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'status'      => 'nullable|in:planning,ongoing,completed,cancelled',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'Vui lòng nhập tên chuyến đi.',
            'name.max'                => 'Tên chuyến đi tối đa 255 ký tự.',
            'destination.required'    => 'Vui lòng nhập điểm đến.',
            'cover_image.image'       => 'File phải là ảnh.',
            'cover_image.mimes'       => 'Ảnh bìa phải là JPG, PNG hoặc WebP.',
            'cover_image.max'         => 'Ảnh bìa tối đa 5MB.',
            'start_date.required'     => 'Vui lòng chọn ngày khởi hành.',
            'start_date.date'         => 'Ngày khởi hành không hợp lệ.',
            'end_date.required'       => 'Vui lòng chọn ngày về.',
            'end_date.date'           => 'Ngày về không hợp lệ.',
            'end_date.after_or_equal' => 'Ngày về phải bằng hoặc sau ngày khởi hành.',
            'status.in'               => 'Trạng thái không hợp lệ.',
        ];
    }
}
