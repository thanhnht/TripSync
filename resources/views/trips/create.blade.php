@extends('layouts.app')
@section('title', 'Tạo chuyến đi')
@section('page-title', 'Tạo chuyến đi mới')
@section('page-subtitle', 'Điền thông tin để bắt đầu lên kế hoạch')

@section('content')
<form method="POST" action="{{ route('trips.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-3 gap-6 items-start">

        {{-- ── Cột trái: các trường nhập ──────────────────────────── --}}
        <div class="col-span-2 space-y-5">

            {{-- Tên & Điểm đến --}}
            <div class="bg-white rounded-xl border border-gray-100 p-6 space-y-5">
                <h3 class="text-sm font-semibold text-gray-800">Thông tin cơ bản</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Tên chuyến đi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="f-name" value="{{ old('name') }}" required
                           oninput="syncPreview()"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition @error('name') border-red-400 @enderror"
                           placeholder="Ví dụ: Đà Lạt mùa hè 2025">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Điểm đến <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="destination" id="f-dest" value="{{ old('destination') }}" required
                           oninput="syncPreview()"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition @error('destination') border-red-400 @enderror"
                           placeholder="Ví dụ: Đà Lạt, Lâm Đồng">
                    @error('destination') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mô tả</label>
                    <textarea name="description" rows="3" oninput="syncPreview()"
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition resize-none"
                              placeholder="Ghi chú về chuyến đi...">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Thời gian & Trạng thái --}}
            <div class="bg-white rounded-xl border border-gray-100 p-6 space-y-5">
                <h3 class="text-sm font-semibold text-gray-800">Thời gian & Trạng thái</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Ngày khởi hành <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="f-start" value="{{ old('start_date') }}" required
                               oninput="syncPreview()"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition @error('start_date') border-red-400 @enderror">
                        @error('start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Ngày về <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date" id="f-end" value="{{ old('end_date') }}" required
                               oninput="syncPreview()"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition @error('end_date') border-red-400 @enderror">
                        @error('end_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Trạng thái</label>
                    <select name="status"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition">
                        <option value="planning" {{ old('status','planning') == 'planning' ? 'selected' : '' }}>Đang lên kế hoạch</option>
                        <option value="ongoing"  {{ old('status') == 'ongoing'  ? 'selected' : '' }}>Đang diễn ra</option>
                    </select>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <a href="{{ route('dashboard') }}"
                   class="flex-1 py-3 text-center rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Huỷ
                </a>
                <button type="submit"
                        class="flex-1 py-3 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition shadow-md shadow-primary/30">
                    Tạo chuyến đi
                </button>
            </div>
        </div>

        {{-- ── Cột phải: ảnh bìa + preview ─────────────────────────── --}}
        <div class="space-y-5 sticky top-6">

            {{-- Cover image --}}
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div id="cover-preview"
                     class="relative h-44 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center cursor-pointer group"
                     onclick="document.getElementById('cover_image').click()">
                    <img id="cover-img" src="" class="hidden absolute inset-0 w-full h-full object-cover">
                    <div id="cover-placeholder" class="text-center text-white px-4">
                        <svg class="w-8 h-8 mx-auto mb-2 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-xs font-medium opacity-80">Click để tải ảnh bìa</p>
                        <p class="text-[11px] opacity-60 mt-0.5">JPG, PNG – tối đa 5MB</p>
                    </div>
                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                        <span class="text-white text-xs font-medium">Thay đổi ảnh</span>
                    </div>
                </div>
                <input type="file" id="cover_image" name="cover_image" class="hidden" accept="image/*">
                <div class="px-4 py-3 border-t border-gray-50">
                    <p class="text-[11px] text-gray-400 text-center">Ảnh bìa sẽ hiển thị trong chuyến đi</p>
                </div>
            </div>

            {{-- Preview card --}}
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-[10.5px] font-semibold text-gray-400 uppercase tracking-wider mb-3">Xem trước</p>
                <p id="prev-name" class="text-sm font-bold text-gray-800 truncate">Tên chuyến đi</p>
                <p id="prev-dest" class="text-xs text-gray-400 mt-0.5 truncate">Điểm đến</p>
                <div class="mt-3 pt-3 border-t border-gray-50 flex items-center justify-between text-[11px] text-gray-400">
                    <span id="prev-dates">Chưa chọn ngày</span>
                    <span id="prev-days" class="font-medium text-blue-500"></span>
                </div>
            </div>
        </div>

    </div>
</form>

@push('scripts')
<script>
document.getElementById('cover_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
        document.getElementById('cover-img').src = ev.target.result;
        document.getElementById('cover-img').classList.remove('hidden');
        document.getElementById('cover-placeholder').classList.add('hidden');
    };
    reader.readAsDataURL(file);
});

function syncPreview() {
    const name  = document.getElementById('f-name').value;
    const dest  = document.getElementById('f-dest').value;
    const start = document.getElementById('f-start').value;
    const end   = document.getElementById('f-end').value;

    document.getElementById('prev-name').textContent = name  || 'Tên chuyến đi';
    document.getElementById('prev-dest').textContent = dest  || 'Điểm đến';

    if (start && end) {
        const s = new Date(start), e = new Date(end);
        const days = Math.max(1, Math.round((e - s) / 86400000) + 1);
        const fmt = d => d.toLocaleDateString('vi-VN', { day:'2-digit', month:'2-digit' });
        document.getElementById('prev-dates').textContent = fmt(s) + ' – ' + fmt(e);
        document.getElementById('prev-days').textContent  = days + ' ngày';
    } else {
        document.getElementById('prev-dates').textContent = 'Chưa chọn ngày';
        document.getElementById('prev-days').textContent  = '';
    }
}
</script>
@endpush
@endsection
