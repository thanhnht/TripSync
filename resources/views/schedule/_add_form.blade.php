<div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
    <p class="text-sm font-semibold text-gray-700 mb-4">Thêm hoạt động mới</p>

    <form method="POST" action="{{ route('schedule.activities.store', [$trip, $day]) }}">
        @csrf

        <div class="grid grid-cols-2 gap-3 mb-3">
            {{-- Tên hoạt động --}}
            <div class="col-span-2">
                <input type="text" name="title" required
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition"
                       placeholder="Tên hoạt động *">
            </div>

            {{-- Loại --}}
            <div>
                <select name="type" required
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition">
                    <option value="">-- Loại hoạt động --</option>
                    <option value="transport">🚗 Di chuyển</option>
                    <option value="accommodation">🏨 Lưu trú</option>
                    <option value="food">🍜 Ăn uống</option>
                    <option value="sightseeing">🏛️ Tham quan</option>
                    <option value="activity">🎯 Hoạt động</option>
                    <option value="other">📌 Khác</option>
                </select>
            </div>

            {{-- Chi phí --}}
            <div>
                <input type="number" name="estimated_cost" min="0" step="1000"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition"
                       placeholder="Chi phí (₫)">
            </div>

            {{-- Giờ bắt đầu --}}
            <div>
                <input type="time" name="start_time"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition">
                <p class="text-xs text-gray-400 mt-1 ml-1">Giờ bắt đầu</p>
            </div>

            {{-- Giờ kết thúc --}}
            <div>
                <input type="time" name="end_time"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition">
                <p class="text-xs text-gray-400 mt-1 ml-1">Giờ kết thúc</p>
            </div>

            {{-- Địa điểm --}}
            <div class="col-span-2">
                <input type="text" name="location"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition"
                       placeholder="Địa điểm">
            </div>

            {{-- Mô tả --}}
            <div class="col-span-2">
                <textarea name="description" rows="2"
                          class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition resize-none"
                          placeholder="Mô tả thêm (tuỳ chọn)..."></textarea>
            </div>

            {{-- Link tham khảo --}}
            <div class="col-span-2">
                <input type="url" name="reference_url"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition"
                       placeholder="Link tham khảo (tuỳ chọn)">
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
