{{-- Modal: sửa hoạt động --}}
<div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white px-6 pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Chỉnh sửa hoạt động</h3>
            <button onclick="closeEditModal()" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="edit-form" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tên hoạt động *</label>
                <input type="text" id="edit-title" name="title" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Loại hoạt động *</label>
                    <select id="edit-type" name="type" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition">
                        <option value="transport">🚗 Di chuyển</option>
                        <option value="accommodation">🏨 Lưu trú</option>
                        <option value="food">🍜 Ăn uống</option>
                        <option value="sightseeing">🏛️ Tham quan</option>
                        <option value="activity">🎯 Hoạt động</option>
                        <option value="other">📌 Khác</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Chi phí dự kiến (₫)</label>
                    <input type="number" id="edit-estimated_cost" name="estimated_cost" min="0" step="1000"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Giờ bắt đầu</label>
                    <input type="time" id="edit-start_time" name="start_time"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Giờ kết thúc</label>
                    <input type="time" id="edit-end_time" name="end_time"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Địa điểm</label>
                <input type="text" id="edit-location" name="location"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition"
                       placeholder="Tên địa điểm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mô tả</label>
                <textarea id="edit-description" name="description" rows="3"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition resize-none"
                          placeholder="Mô tả chi tiết..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Link tham khảo</label>
                <input type="url" id="edit-reference_url" name="reference_url"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition"
                       placeholder="https://...">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeEditModal()"
                        class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Huỷ
                </button>
                <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-dark transition shadow-md shadow-primary/30">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
