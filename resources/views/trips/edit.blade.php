@extends('layouts.app')
@section('title', 'Sửa chuyến đi')
@section('page-title', 'Chỉnh sửa chuyến đi')
@section('page-subtitle', $trip->name)

@section('header-actions')
    <a href="{{ route('trips.show', $trip) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
        ← Quay lại
    </a>
@endsection

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('trips.update', $trip) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Cover image --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="relative h-48 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center cursor-pointer group"
                 onclick="document.getElementById('cover_image').click()">
                <img id="cover-img"
                     src="{{ $trip->cover_image ? $trip->cover_image_url : '' }}"
                     class="{{ $trip->cover_image ? '' : 'hidden' }} absolute inset-0 w-full h-full object-cover">
                <div id="cover-placeholder" class="{{ $trip->cover_image ? 'hidden' : '' }} text-center text-white">
                    <svg class="w-10 h-10 mx-auto mb-2 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm font-medium opacity-80">Click để thay đổi ảnh bìa</p>
                </div>
                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                    <span class="text-white text-sm font-medium">Thay đổi ảnh bìa</span>
                </div>
            </div>
            <input type="file" id="cover_image" name="cover_image" class="hidden" accept="image/*">
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-6 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tên chuyến đi <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $trip->name) }}" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Điểm đến <span class="text-red-500">*</span></label>
                <input type="text" name="destination" value="{{ old('destination', $trip->destination) }}" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mô tả</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition resize-none">{{ old('description', $trip->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày khởi hành</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $trip->start_date->format('Y-m-d')) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày về</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $trip->end_date->format('Y-m-d')) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Trạng thái</label>
                <select name="status"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition">
                    @foreach(['planning' => 'Đang lên kế hoạch', 'ongoing' => 'Đang diễn ra', 'completed' => 'Đã hoàn thành', 'cancelled' => 'Đã huỷ'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $trip->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('trips.show', $trip) }}"
               class="flex-1 py-3 text-center rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Huỷ
            </a>
            <button type="submit"
                    class="flex-1 py-3 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition shadow-md shadow-primary/30">
                Lưu thay đổi
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('cover_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (ev) => {
            const img = document.getElementById('cover-img');
            img.src = ev.target.result;
            img.classList.remove('hidden');
            document.getElementById('cover-placeholder').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });
</script>
@endsection
