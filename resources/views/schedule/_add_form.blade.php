<div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
    <p class="text-sm font-semibold text-gray-700 mb-4">Thêm hoạt động mới</p>

    <form method="POST" action="{{ route('schedule.activities.store', [$trip, $day]) }}" novalidate>
        @csrf

        <div class="grid grid-cols-2 gap-3 mb-3">
            {{-- Tên hoạt động --}}
            <div class="col-span-2">
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition @error('title') !border-red-400 @enderror"
                       placeholder="Tên hoạt động *">
                @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Loại --}}
            <div>
                <select name="type"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition @error('type') !border-red-400 @enderror">
                    <option value="">-- Loại hoạt động --</option>
                    <option value="transport"      {{ old('type') == 'transport'      ? 'selected' : '' }}>🚗 Di chuyển</option>
                    <option value="accommodation"  {{ old('type') == 'accommodation'  ? 'selected' : '' }}>🏨 Lưu trú</option>
                    <option value="food"           {{ old('type') == 'food'           ? 'selected' : '' }}>🍜 Ăn uống</option>
                    <option value="sightseeing"    {{ old('type') == 'sightseeing'    ? 'selected' : '' }}>🏛️ Tham quan</option>
                    <option value="activity"       {{ old('type') == 'activity'       ? 'selected' : '' }}>🎯 Hoạt động</option>
                    <option value="other"          {{ old('type') == 'other'          ? 'selected' : '' }}>📌 Khác</option>
                </select>
                @error('type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Chi phí --}}
            <div>
                <input type="number" name="estimated_cost" value="{{ old('estimated_cost') }}" step="1000"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition @error('estimated_cost') !border-red-400 @enderror"
                       placeholder="Chi phí (₫)">
                @error('estimated_cost')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @else
                    <p class="mt-1 text-xs text-gray-400">Tối thiểu 10.000 ₫ nếu có nhập</p>
                @enderror
            </div>

            {{-- Giờ bắt đầu --}}
            <div>
                <input type="time" name="start_time" value="{{ old('start_time') }}"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition @error('start_time') !border-red-400 @enderror">
                @error('start_time')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @else
                    <p class="text-xs text-gray-400 mt-1 ml-1">Giờ bắt đầu</p>
                @enderror
            </div>

            {{-- Giờ kết thúc --}}
            <div>
                <input type="time" name="end_time" value="{{ old('end_time') }}"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition @error('end_time') !border-red-400 @enderror">
                @error('end_time')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @else
                    <p class="text-xs text-gray-400 mt-1 ml-1">Giờ kết thúc</p>
                @enderror
            </div>

            {{-- Địa điểm --}}
            <div class="col-span-2">
                <input type="text" name="location" value="{{ old('location') }}"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition"
                       placeholder="Địa điểm">
            </div>

            {{-- Mô tả --}}
            <div class="col-span-2">
                <textarea name="description" rows="2"
                          class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition resize-none"
                          placeholder="Mô tả thêm (tuỳ chọn)...">{{ old('description') }}</textarea>
            </div>

            {{-- Link tham khảo --}}
            <div class="col-span-2">
                <input type="text" name="reference_url" value="{{ old('reference_url') }}"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition @error('reference_url') !border-red-400 @enderror"
                       placeholder="Link tham khảo (tuỳ chọn)">
                @error('reference_url') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-2 justify-end">
            <button type="button" onclick="toggleAddForm({{ $day->id }})"
                    class="px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
                Huỷ
            </button>
            <button type="submit"
                    class="px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-dark transition shadow-md shadow-primary/30">
                Thêm hoạt động
            </button>
        </div>
    </form>
</div>
