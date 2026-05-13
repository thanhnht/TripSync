@extends('layouts.app')
@section('title', 'Checklist – ' . $trip->name)
@section('page-title', 'Checklist chuẩn bị')
@section('page-subtitle', $trip->name . ' · ' . $trip->destination)

@section('header-actions')
    <a href="{{ route('trips.show', $trip) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
        ← Quay lại chuyến đi
    </a>
@endsection

@section('content')

{{-- Progress bar --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-6">
    <div class="flex items-center justify-between mb-3">
        <div>
            <span class="text-sm font-semibold text-gray-800">Tiến độ chuẩn bị</span>
            <span class="text-xs text-gray-500 ml-2" id="progress-text">{{ $done }}/{{ $total }} mục</span>
        </div>
        <span class="text-lg font-bold text-primary" id="progress-percent">
            {{ $total > 0 ? round($done / $total * 100) : 0 }}%
        </span>
    </div>
    <div class="w-full bg-gray-100 rounded-full h-2.5">
        <div id="progress-bar"
             class="bg-primary h-2.5 rounded-full transition-all duration-500"
             style="width: {{ $total > 0 ? round($done / $total * 100) : 0 }}%"></div>
    </div>
    @if($total > 0 && $done === $total)
        <p class="text-xs text-green-600 font-medium mt-2">🎉 Đã chuẩn bị xong tất cả!</p>
    @endif
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Left: checklist grouped by category --}}
    <div class="col-span-2 space-y-4">

        @if($grouped->isEmpty())
            <div class="bg-white rounded-xl border border-dashed border-gray-200 p-16 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <p class="text-gray-500 font-medium">Chưa có mục nào.</p>
                <p class="text-sm text-gray-400 mt-1">Thêm đồ dùng cần chuẩn bị bên phải.</p>
            </div>
        @else
            @foreach($grouped as $category => $items)
            @php
                $catDone  = $items->where('is_done', true)->count();
                $catTotal = $items->count();
            @endphp
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                {{-- Category header --}}
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-50 bg-gray-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-semibold text-gray-800">{{ $category }}</h3>
                        <span class="text-xs text-gray-400">{{ $catDone }}/{{ $catTotal }}</span>
                    </div>
                    @if($catDone === $catTotal && $catTotal > 0)
                        <span class="text-xs text-green-600 font-medium">✓ Xong</span>
                    @endif
                </div>

                {{-- Items --}}
                <div class="divide-y divide-gray-50">
                    @foreach($items as $item)
                        @include('checklist._item', compact('item', 'trip'))
                    @endforeach
                </div>
            </div>
            @endforeach
        @endif
    </div>

    {{-- Right: add form + members summary --}}
    <div class="space-y-5">

        {{-- Add item form --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Thêm mục mới</h3>
            <form method="POST" action="{{ route('checklist.store', $trip) }}" class="space-y-3">
                @csrf
                <div>
                    <input type="text" name="content" required autofocus
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition"
                           placeholder="VD: Kem chống nắng, Sạc dự phòng...">
                </div>
                <div>
                    <input type="text" name="category" list="category-list"
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition"
                           placeholder="Nhóm (VD: Quần áo, Giấy tờ...)">
                    <datalist id="category-list">
                        @foreach($grouped->keys() as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                        <option value="Giấy tờ">
                        <option value="Quần áo">
                        <option value="Thiết bị điện tử">
                        <option value="Thuốc & Y tế">
                        <option value="Đồ ăn & Nước uống">
                        <option value="Chung">
                    </datalist>
                </div>
                <div>
                    <select name="assigned_to"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition">
                        <option value="">— Không phân công —</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="w-full py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition">
                    Thêm vào danh sách
                </button>
            </form>
        </div>

        {{-- Members progress --}}
        @if($members->count() > 1)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Theo từng thành viên</h3>
            <div class="space-y-3">
                @foreach($members as $member)
                @php
                    $myItems = $grouped->flatten()->where('assigned_to', $member->id);
                    $myDone  = $myItems->where('is_done', true)->count();
                    $myTotal = $myItems->count();
                @endphp
                @if($myTotal > 0)
                <div class="flex items-center gap-3">
                    <img src="{{ $member->avatar_url }}" class="w-7 h-7 rounded-full object-cover shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-700 truncate">{{ $member->name }}</span>
                            <span class="text-xs text-gray-500 ml-2 shrink-0">{{ $myDone }}/{{ $myTotal }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-primary h-1.5 rounded-full transition-all"
                                 style="width: {{ $myTotal > 0 ? round($myDone/$myTotal*100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Edit item modal --}}
<div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Chỉnh sửa mục</h3>
        <form id="edit-form" method="POST">
            @csrf @method('PUT')
            <div class="space-y-3">
                <input type="text" name="content" id="edit-content" required
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition">
                <input type="text" name="category" id="edit-category" list="category-list"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition"
                       placeholder="Nhóm">
                <select name="assigned_to" id="edit-assigned"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition">
                    <option value="">— Không phân công —</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="closeEditModal()"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Huỷ
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-dark transition">
                    Lưu
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Toggle done (AJAX) ────────────────────────────────────────────────
async function toggleItem(id, btn) {
    try {
        const res = await fetch(`/trips/{{ $trip->id }}/checklist/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        if (!res.ok) return;
        const data = await res.json();

        // Update checkbox button
        const row = document.getElementById(`item-${id}`);
        const txt = document.getElementById(`item-text-${id}`);

        if (data.is_done) {
            btn.classList.replace('border-gray-300', 'bg-green-500');
            btn.classList.replace('hover:border-green-400', 'border-green-500');
            btn.classList.add('text-white');
            btn.innerHTML = `<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>`;
            row.classList.add('opacity-60');
            txt.classList.add('line-through', 'text-gray-400');
            txt.classList.remove('text-gray-800');
        } else {
            btn.classList.replace('bg-green-500', 'border-gray-300');
            btn.classList.replace('border-green-500', 'hover:border-green-400');
            btn.classList.remove('text-white');
            btn.innerHTML = '';
            row.classList.remove('opacity-60');
            txt.classList.remove('line-through', 'text-gray-400');
            txt.classList.add('text-gray-800');
        }

        // Update global progress bar
        document.getElementById('progress-bar').style.width     = data.percent + '%';
        document.getElementById('progress-percent').textContent  = data.percent + '%';
        document.getElementById('progress-text').textContent     = `${data.done}/${data.total} mục`;
    } catch (e) {
        console.error('Toggle failed', e);
    }
}

// ── Edit modal ────────────────────────────────────────────────────────
function openEditItem(id, content, category, assignedTo) {
    const modal = document.getElementById('edit-modal');
    document.getElementById('edit-form').action = `/trips/{{ $trip->id }}/checklist/${id}`;
    document.getElementById('edit-content').value  = content;
    document.getElementById('edit-category').value = category;
    document.getElementById('edit-assigned').value = assignedTo ?? '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('edit-content').focus();
}
function closeEditModal() {
    const modal = document.getElementById('edit-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeEditModal();
});
</script>
@endpush
