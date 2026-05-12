@extends('layouts.app')
@section('title', 'Thư viện ảnh – ' . $trip->name)
@section('page-title', 'Thư viện ảnh')
@section('page-subtitle', $trip->name . ' · ' . $trip->destination)

@section('header-actions')
    <a href="{{ route('trips.show', $trip) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
        ← Quay lại chuyến đi
    </a>
@endsection

@section('content')

{{-- Hidden bulk-download form --}}
<form id="bulk-download-form" method="POST" action="{{ route('photo.download-bulk', $trip) }}">
    @csrf
</form>

{{-- Toolbar --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-6">
        <div class="text-sm text-gray-500">
            <span class="text-xl font-bold text-gray-900">{{ $photos->count() }}</span> ảnh
        </div>
        @if($photos->count())
        <div class="text-sm text-gray-500">
            <span class="font-medium text-gray-700">
                {{ $photos->sum(fn($p) => $p->size) > 1048576
                    ? round($photos->sum(fn($p) => $p->size) / 1048576, 1) . ' MB'
                    : round($photos->sum(fn($p) => $p->size) / 1024, 0) . ' KB' }}
            </span> dung lượng
        </div>
        @endif
    </div>

    <div class="flex items-center gap-2">
        @if($photos->count())
        {{-- Select all toggle --}}
        <button id="select-all-btn" onclick="toggleSelectAll()"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span id="select-all-label">Chọn tất cả</span>
        </button>

        {{-- Download selected --}}
        <button id="download-btn" onclick="submitBulkDownload()" disabled
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 transition disabled:opacity-40 disabled:cursor-not-allowed enabled:hover:border-primary enabled:hover:text-primary enabled:hover:bg-primary/5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Tải xuống
            <span id="selected-count-badge" class="hidden bg-primary text-white text-xs font-bold px-1.5 py-0.5 rounded-full leading-none"></span>
        </button>
        @endif

        <button onclick="toggleUploadPanel()"
                class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Tải ảnh lên
        </button>
    </div>
</div>

{{-- Upload panel --}}
<div id="upload-panel" class="hidden bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-800 mb-4">Tải ảnh lên thư viện</h3>
    <form method="POST" action="{{ route('photo.store', $trip) }}" enctype="multipart/form-data" id="upload-form">
        @csrf
        <div class="space-y-4">
            {{-- Drop zone --}}
            <div id="drop-zone"
                 class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center cursor-pointer hover:border-primary hover:bg-primary/5 transition"
                 onclick="document.getElementById('photo-input').click()"
                 ondragover="event.preventDefault(); this.classList.add('border-primary','bg-primary/5')"
                 ondragleave="this.classList.remove('border-primary','bg-primary/5')"
                 ondrop="handleDrop(event)">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm font-medium text-gray-600">Kéo thả ảnh vào đây hoặc <span class="text-primary underline">chọn file</span></p>
                <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP, GIF · Tối đa 10MB/ảnh · Tối đa 20 ảnh/lần</p>
                <input type="file" id="photo-input" name="photos[]" multiple accept="image/*"
                       class="hidden" onchange="previewFiles(this.files)">
            </div>

            {{-- Preview grid --}}
            <div id="preview-grid" class="hidden grid grid-cols-6 gap-2"></div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mô tả (áp dụng cho tất cả ảnh)</label>
                <input type="text" name="description"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition"
                       placeholder="Chú thích cho bộ ảnh này...">
            </div>
        </div>

        <div class="flex gap-3 mt-5">
            <button type="button" onclick="toggleUploadPanel()"
                    class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Huỷ
            </button>
            <button type="submit" id="upload-btn" disabled
                    class="flex-1 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-dark transition disabled:opacity-50 disabled:cursor-not-allowed">
                Tải lên
            </button>
        </div>
    </form>
</div>

{{-- Photo grid --}}
@if($photos->isEmpty())
    <div class="bg-white rounded-xl border border-dashed border-gray-200 p-20 text-center">
        <p class="text-5xl mb-4">📸</p>
        <p class="text-gray-500 font-medium">Chưa có ảnh nào trong thư viện.</p>
        <p class="text-sm text-gray-400 mt-1">Hãy tải ảnh đầu tiên của chuyến đi lên!</p>
    </div>
@else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach($photos as $photo)
            @include('photo._photo_card', compact('photo', 'trip'))
        @endforeach
    </div>
@endif

{{-- Lightbox --}}
<div id="lightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/90"
     onclick="if(event.target===this) closeLightbox()">
    <button onclick="closeLightbox()"
            class="absolute top-4 right-4 text-white/70 hover:text-white transition">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <img id="lightbox-img" src="" alt="" class="max-h-[90vh] max-w-[90vw] object-contain rounded-lg shadow-2xl">
    <p id="lightbox-caption" class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/80 text-sm bg-black/40 px-4 py-2 rounded-full"></p>
</div>

@endsection

@push('scripts')
<script>
const photoDownloadUrls = @json(
    $photos->mapWithKeys(fn($p) => [$p->id => route('photo.download', [$trip, $p])])
);

// ── Upload panel ──────────────────────────────────────────────────────
function toggleUploadPanel() {
    const panel = document.getElementById('upload-panel');
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function previewFiles(files) {
    const grid = document.getElementById('preview-grid');
    const btn  = document.getElementById('upload-btn');
    grid.innerHTML = '';

    if (!files.length) {
        grid.classList.add('hidden');
        btn.disabled = true;
        return;
    }

    grid.classList.remove('hidden');
    grid.classList.add('grid');
    btn.disabled = false;

    Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'aspect-square rounded-lg overflow-hidden bg-gray-100';
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            grid.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

function handleDrop(event) {
    event.preventDefault();
    document.getElementById('drop-zone').classList.remove('border-primary', 'bg-primary/5');
    const input = document.getElementById('photo-input');
    input.files = event.dataTransfer.files;
    previewFiles(event.dataTransfer.files);
}

// ── Checkbox & download ───────────────────────────────────────────────
let allSelected = false;

function onCheckChange() {
    // Re-evaluate the select-all toggle state
    const all     = document.querySelectorAll('input[name="ids[]"]');
    const checked = document.querySelectorAll('input[name="ids[]"]:checked');
    allSelected   = all.length > 0 && checked.length === all.length;
    updateToolbar(checked.length);
}

function toggleSelectAll() {
    allSelected = !allSelected;
    document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = allSelected);
    const checked = document.querySelectorAll('input[name="ids[]"]:checked');
    updateToolbar(checked.length);
}

function updateToolbar(count) {
    // Download button
    const dlBtn  = document.getElementById('download-btn');
    const badge  = document.getElementById('selected-count-badge');
    dlBtn.disabled = count === 0;
    if (count > 0) {
        badge.textContent = count;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }

    // Select-all button label
    const label = document.getElementById('select-all-label');
    if (label) label.textContent = allSelected ? 'Bỏ chọn tất cả' : 'Chọn tất cả';

    // Overlay highlights
    document.querySelectorAll('input[name="ids[]"]').forEach(cb => {
        const overlay = document.querySelector(`#photo-${cb.value} .selected-overlay`);
        if (overlay) overlay.classList.toggle('hidden', !cb.checked);
    });
}

function submitBulkDownload() {
    const checked = [...document.querySelectorAll('input[name="ids[]"]:checked')];
    if (!checked.length) return;

    if (checked.length > 15) {
        // ZIP download
        document.getElementById('bulk-download-form').submit();
        return;
    }

    // Individual downloads — stagger by 300ms to avoid browser blocking
    checked.forEach((cb, i) => {
        const url = photoDownloadUrls[cb.value];
        if (!url) return;
        setTimeout(() => {
            const a = document.createElement('a');
            a.href = url;
            a.download = '';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }, i * 300);
    });
}

// ── Lightbox ──────────────────────────────────────────────────────────
function openLightbox(url, caption) {
    document.getElementById('lightbox-img').src = url;
    document.getElementById('lightbox-caption').textContent = caption;
    const lb = document.getElementById('lightbox');
    lb.classList.remove('hidden');
    lb.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lb = document.getElementById('lightbox');
    lb.classList.add('hidden');
    lb.classList.remove('flex');
    lb.querySelector('img').src = '';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeLightbox();
});
</script>
@endpush
