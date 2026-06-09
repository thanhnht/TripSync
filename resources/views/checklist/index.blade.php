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

@php
    $defaultCats = [
        '📄' => 'Giấy tờ',
        '👕' => 'Quần áo',
        '🔌' => 'Thiết bị điện tử',
        '💊' => 'Thuốc & Y tế',
        '🍱' => 'Đồ ăn & Nước uống',
        '📦' => 'Chung',
    ];
@endphp

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

    {{-- Left: checklist --}}
    <div class="col-span-2 space-y-5">

        {{-- Section 1: Chờ phân công --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <h2 class="text-sm font-semibold text-gray-700">Chờ phân công</h2>
                @if($pending->count() > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                        {{ $pending->count() }}
                    </span>
                @endif
                @if($trip->isOwner(Auth::user()) && $pending->count() > 0)
                    <span class="text-xs text-amber-600 ml-1">· Chọn người phụ trách bên dưới</span>
                @endif
            </div>

            @if($pending->isEmpty())
                <div class="bg-white rounded-xl border border-dashed border-gray-200 px-5 py-6 text-center">
                    <p class="text-sm text-green-600 font-medium">Tất cả đã được phân công 🎉</p>
                </div>
            @else
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden divide-y divide-gray-50">
                    @foreach($pending as $item)
                        @include('checklist._item', compact('item', 'trip', 'members'))
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Section 2: Đã phân công --}}
        @if($groupedAssigned->isNotEmpty())
        <div>
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Đã phân công</h2>
            <div class="space-y-4">
                @foreach($groupedAssigned as $category => $items)
                @php
                    $catDone  = $items->where('is_done', true)->count();
                    $catTotal = $items->count();
                @endphp
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-50 bg-gray-50/60">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-semibold text-gray-800">{{ $category }}</h3>
                            <span class="text-xs text-gray-400">{{ $catDone }}/{{ $catTotal }}</span>
                        </div>
                        @if($catDone === $catTotal && $catTotal > 0)
                            <span class="text-xs text-green-600 font-medium">✓ Xong</span>
                        @endif
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($items as $item)
                            @include('checklist._item', compact('item', 'trip', 'members'))
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Empty state (nothing at all) --}}
        @if($pending->isEmpty() && $groupedAssigned->isEmpty())
            <div class="bg-white rounded-xl border border-dashed border-gray-200 p-16 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <p class="text-gray-500 font-medium">Chưa có mục nào.</p>
                <p class="text-sm text-gray-400 mt-1">Đề xuất đồ dùng cần chuẩn bị bên phải.</p>
            </div>
        @endif
    </div>

    {{-- Right: add form + members summary --}}
    <div class="space-y-5">

        {{-- Add item form --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-1">Đề xuất mục mới</h3>
            <p class="text-xs text-gray-400 mb-4">
                @if($trip->isOwner(Auth::user()))
                    Thêm mục và tuỳ chọn phân công ngay.
                @else
                    Đề xuất đồ dùng, trưởng nhóm sẽ phân công.
                @endif
            </p>
            <form method="POST" action="{{ route('checklist.store', $trip) }}" class="space-y-3">
                @csrf
                <div>
                    <input type="text" name="content" required autofocus
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition"
                           placeholder="VD: Kem chống nắng, Sạc dự phòng...">
                </div>
                @php $extraCats = $groupedAssigned->keys()->diff(array_values($defaultCats)); @endphp
                <div class="relative">
                    <input type="hidden" name="category" id="add-category-val">
                    <button type="button" onclick="toggleDropdown('add-category-menu', event)"
                            class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm hover:border-blue-400 transition text-left">
                        <span id="add-category-display" class="flex-1 text-gray-400">Chọn nhóm...</span>
                        <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="add-category-menu"
                         class="dropdown-menu hidden absolute left-0 right-0 top-full mt-1 bg-white rounded-xl border border-gray-100 shadow-xl py-1.5 z-20 max-h-56 overflow-y-auto">
                        @foreach($defaultCats as $icon => $label)
                        <button type="button"
                                onclick="pickCategory('add-category-menu','add-category-val','add-category-display','{{ $label }}','{{ $icon }}')"
                                class="w-full flex items-center gap-2.5 px-3 py-2 hover:bg-gray-50 transition text-left">
                            <span class="text-base w-5 text-center shrink-0">{{ $icon }}</span>
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </button>
                        @endforeach
                        @foreach($extraCats as $cat)
                        <button type="button"
                                onclick="pickCategory('add-category-menu','add-category-val','add-category-display','{{ $cat }}','📌')"
                                class="w-full flex items-center gap-2.5 px-3 py-2 hover:bg-gray-50 transition text-left">
                            <span class="text-base w-5 text-center shrink-0">📌</span>
                            <span class="text-sm text-gray-700">{{ $cat }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @if($trip->isOwner(Auth::user()))
                <div class="relative">
                    <input type="hidden" name="assigned_to" id="add-assignee-val">
                    <button type="button" onclick="toggleDropdown('add-assignee-menu', event)"
                            class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm hover:border-blue-400 transition text-left">
                        <span id="add-assignee-display" class="flex items-center gap-2 flex-1 min-w-0 text-gray-400">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Phân công sau</span>
                        </span>
                        <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="add-assignee-menu"
                         class="dropdown-menu hidden absolute left-0 right-0 top-full mt-1 bg-white rounded-xl border border-gray-100 shadow-xl py-1.5 z-20">
                        <button type="button"
                                onclick="pickAssignee('add-assignee-menu','add-assignee-val','add-assignee-display','','','','Phân công sau')"
                                class="w-full flex items-center gap-2.5 px-3 py-2 hover:bg-gray-50 transition text-left">
                            <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-400">Phân công sau</span>
                        </button>
                        @foreach($members as $member)
                        <button type="button"
                                onclick="pickAssignee('add-assignee-menu','add-assignee-val','add-assignee-display','{{ $member->id }}','{{ $member->avatar_url }}','{{ addslashes($member->name) }}','')"
                                class="w-full flex items-center gap-2.5 px-3 py-2 hover:bg-gray-50 transition text-left">
                            <img src="{{ $member->avatar_url }}" class="w-6 h-6 rounded-full object-cover shrink-0">
                            <span class="text-sm text-gray-700 truncate">{{ $member->name }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif
                <button type="submit"
                        class="w-full py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition">
                    Đề xuất
                </button>
            </form>
        </div>

        {{-- Members progress --}}
        @if($members->count() > 1)
        @php
            $allItems = $pending->concat($groupedAssigned->flatten());
        @endphp
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Theo từng thành viên</h3>
            <div class="space-y-3">
                @foreach($members as $member)
                @php
                    $myItems = $allItems->where('assigned_to', $member->id);
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
                <div class="relative">
                    <input type="hidden" name="category" id="edit-category">
                    <button type="button" onclick="toggleDropdown('edit-category-menu', event)"
                            class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm hover:border-blue-400 transition text-left">
                        <span id="edit-category-display" class="flex-1 text-gray-400">Chọn nhóm...</span>
                        <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="edit-category-menu"
                         class="dropdown-menu hidden absolute left-0 right-0 top-full mt-1 bg-white rounded-xl border border-gray-100 shadow-xl py-1.5 z-20 max-h-56 overflow-y-auto">
                        @foreach($defaultCats as $icon => $label)
                        <button type="button"
                                onclick="pickCategory('edit-category-menu','edit-category','edit-category-display','{{ $label }}','{{ $icon }}')"
                                class="w-full flex items-center gap-2.5 px-3 py-2 hover:bg-gray-50 transition text-left">
                            <span class="text-base w-5 text-center shrink-0">{{ $icon }}</span>
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </button>
                        @endforeach
                        @foreach($extraCats as $cat)
                        <button type="button"
                                onclick="pickCategory('edit-category-menu','edit-category','edit-category-display','{{ $cat }}','📌')"
                                class="w-full flex items-center gap-2.5 px-3 py-2 hover:bg-gray-50 transition text-left">
                            <span class="text-base w-5 text-center shrink-0">📌</span>
                            <span class="text-sm text-gray-700">{{ $cat }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @if($trip->isOwner(Auth::user()))
                <div class="relative">
                    <input type="hidden" name="assigned_to" id="edit-assigned">
                    <button type="button" onclick="toggleDropdown('edit-assignee-menu', event)"
                            class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm hover:border-blue-400 transition text-left">
                        <span id="edit-assignee-display" class="flex items-center gap-2 flex-1 min-w-0 text-gray-400">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Bỏ phân công</span>
                        </span>
                        <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="edit-assignee-menu"
                         class="dropdown-menu hidden absolute left-0 right-0 top-full mt-1 bg-white rounded-xl border border-gray-100 shadow-xl py-1.5 z-20">
                        <button type="button"
                                onclick="pickAssignee('edit-assignee-menu','edit-assigned','edit-assignee-display','','','','Bỏ phân công')"
                                class="w-full flex items-center gap-2.5 px-3 py-2 hover:bg-gray-50 transition text-left">
                            <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-400">Bỏ phân công</span>
                        </button>
                        @foreach($members as $member)
                        <button type="button"
                                onclick="pickAssignee('edit-assignee-menu','edit-assigned','edit-assignee-display','{{ $member->id }}','{{ $member->avatar_url }}','{{ addslashes($member->name) }}','')"
                                class="w-full flex items-center gap-2.5 px-3 py-2 hover:bg-gray-50 transition text-left">
                            <img src="{{ $member->avatar_url }}" class="w-6 h-6 rounded-full object-cover shrink-0">
                            <span class="text-sm text-gray-700 truncate">{{ $member->name }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif
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

        document.getElementById('progress-bar').style.width    = data.percent + '%';
        document.getElementById('progress-percent').textContent = data.percent + '%';
        document.getElementById('progress-text').textContent    = `${data.done}/${data.total} mục`;
    } catch (e) {
        console.error('Toggle failed', e);
    }
}

// ── Members data (for edit modal display sync) ────────────────────────
const membersData = {
    @foreach($members as $m)
    {{ $m->id }}: { name: '{{ addslashes($m->name) }}', avatar: '{{ $m->avatar_url }}' },
    @endforeach
};

// ── Shared dropdown helpers ───────────────────────────────────────────
function toggleDropdown(menuId, event) {
    event.stopPropagation();
    const menu = document.getElementById(menuId);
    const isHidden = menu.classList.contains('hidden');
    closeAllDropdowns();
    if (isHidden) menu.classList.remove('hidden');
}

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
}

document.addEventListener('click', closeAllDropdowns);

// ── Inline item assign dropdown ───────────────────────────────────────
function toggleAssignDropdown(id, event) {
    toggleDropdown(`assign-menu-${id}`, event);
}

// ── Assign via event delegation (handles clicks on [data-assign-url] and their children) ──
document.addEventListener('click', async function(e) {
    const btn = e.target.closest('[data-assign-url]');
    if (!btn) return;

    const url      = btn.dataset.assignUrl;
    const memberId = btn.dataset.memberId;

    closeAllDropdowns();

    const body = new URLSearchParams();
    body.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    if (memberId) body.append('assigned_to', memberId);

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString(),
        });
        if (res.ok) {
            window.location.reload();
        } else {
            const txt = await res.text();
            alert('Lỗi ' + res.status + ': ' + txt);
        }
    } catch (e) {
        alert('Lỗi: ' + e.message);
    }
});

// ── Category custom select ────────────────────────────────────────────
const categoryIcons = @json(array_flip($defaultCats));

function pickCategory(menuId, hiddenId, displayId, value, icon) {
    document.getElementById(hiddenId).value = value;
    const display = document.getElementById(displayId);
    display.innerHTML = `<span class="text-base mr-0.5">${icon}</span> <span class="text-gray-700">${value}</span>`;
    display.classList.remove('text-gray-400');
    closeAllDropdowns();
}

// ── Custom select (add form + edit modal) ─────────────────────────────
function pickAssignee(menuId, hiddenId, displayId, value, avatar, name, placeholder) {
    document.getElementById(hiddenId).value = value;
    const display = document.getElementById(displayId);
    if (value) {
        display.innerHTML = `<img src="${avatar}" class="w-5 h-5 rounded-full object-cover shrink-0"><span class="text-sm text-gray-700 truncate">${name}</span>`;
        display.classList.remove('text-gray-400');
    } else {
        display.innerHTML = `<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg><span>${placeholder}</span>`;
        display.classList.add('text-gray-400');
    }
    closeAllDropdowns();
}

// ── Edit modal ────────────────────────────────────────────────────────
function openEditItem(id, content, category, assignedTo) {
    const modal = document.getElementById('edit-modal');
    document.getElementById('edit-form').action = `/trips/{{ $trip->id }}/checklist/${id}`;
    document.getElementById('edit-content').value = content;

    // Sync category dropdown display
    document.getElementById('edit-category').value = category;
    const catDisplay = document.getElementById('edit-category-display');
    if (catDisplay && category) {
        const icon = categoryIcons[category] ?? '📌';
        catDisplay.innerHTML = `<span class="text-base mr-0.5">${icon}</span> <span class="text-gray-700">${category}</span>`;
        catDisplay.classList.remove('text-gray-400');
    } else if (catDisplay) {
        catDisplay.innerHTML = 'Chọn nhóm...';
        catDisplay.classList.add('text-gray-400');
    }

    // Sync custom assignee dropdown
    const hiddenInput = document.getElementById('edit-assigned');
    if (hiddenInput) {
        const m = assignedTo ? membersData[assignedTo] : null;
        pickAssignee('edit-assignee-menu', 'edit-assigned', 'edit-assignee-display',
            assignedTo ? String(assignedTo) : '',
            m ? m.avatar : '',
            m ? m.name  : '',
            'Bỏ phân công');
    }

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
