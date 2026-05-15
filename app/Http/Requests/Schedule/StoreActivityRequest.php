<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge(['estimated_cost' => $this->estimated_cost ?? 0]);
    }

    public function rules(): array
    {
        return [
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string|max:2000',
            'type'           => 'required|in:transport,accommodation,food,sightseeing,activity,other',
            'start_time'     => 'nullable|date_format:H:i',
            'end_time'       => 'nullable|date_format:H:i|after_or_equal:start_time',
            'location'       => 'nullable|string|max:255',
            'estimated_cost' => 'nullable|integer|min:10000',
            'reference_url'  => 'nullable|url|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'Vui lòng nhập tên hoạt động.',
            'type.required'           => 'Vui lòng chọn loại hoạt động.',
            'type.in'                 => 'Loại hoạt động không hợp lệ.',
            'start_time.date_format'  => 'Giờ bắt đầu không hợp lệ (HH:MM).',
            'end_time.date_format'    => 'Giờ kết thúc không hợp lệ (HH:MM).',
            'end_time.after_or_equal' => 'Giờ kết thúc phải sau giờ bắt đầu.',
            'estimated_cost.integer'  => 'Chi phí phải là số nguyên.',
            'estimated_cost.min'      => 'Chi phí tối thiểu là 10.000 ₫.',
            'reference_url.url'       => 'Link tham khảo không hợp lệ.',
        ];
    }
}
