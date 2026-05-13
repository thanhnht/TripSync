@extends('layouts.app')
@section('title', 'Lịch trình – ' . $trip->name)
@section('page-title', 'Lịch trình')
@section('page-subtitle', $trip->name . ' · ' . $trip->destination)

@section('header-actions')
    <a href="{{ route('trips.show', $trip) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
        ← Quay lại chuyến đi
    </a>
@endsection

@section('content')

{{-- Stats bar --}}
<div class="grid grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900 leading-tight">{{ $days->count() }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Số ngày</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900 leading-tight">{{ $totalActivities }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Hoạt động</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900 leading-tight">{{ $approvedCount }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Đã duyệt</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900 leading-tight">{{ number_format($totalCost, 0, ',', '.') }} ₫</p>
            <p class="text-xs text-gray-500 mt-0.5">Chi phí dự kiến</p>
        </div>
    </div>
</div>

{{-- Days accordion --}}
<div class="space-y-4" id="schedule-container">
    @forelse($days as $day)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" id="day-{{ $day->id }}">

        {{-- Day header --}}
        <div class="flex items-center justify-between px-6 py-4 cursor-pointer select-none hover:bg-gray-50 transition"
             onclick="toggleDay({{ $day->id }})">
            <div class="flex items-center gap-4">
                {{-- Day badge --}}
                <div class="w-12 h-12 rounded-xl bg-primary flex flex-col items-center justify-center shadow-md shadow-primary/30 shrink-0">
                    <span class="text-white text-xs font-medium leading-none">Ngày</span>
                    <span class="text-white text-lg font-bold leading-tight">{{ $day->day_number }}</span>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $day->display_title }}</h3>
                    <p class="text-sm text-gray-500">
                        {{ $day->date->isoFormat('dddd, D/M/YYYY') }}
                        @if($day->activities->count())
                            · {{ $day->activities->count() }} hoạt động
                        @endif
                        @if($day->total_cost > 0)
                            · {{ number_format($day->total_cost, 0, ',', '.') }} ₫
                        @endif
                    </p>
                    @if($day->note)
                        <p class="text-xs text-gray-400 mt-0.5 italic">📝 {{ Str::limit($day->note, 80) }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Edit day button --}}
                <button onclick="event.stopPropagation(); openDayModal({{ $day->id }}, '{{ addslashes($day->title ?? '') }}', '{{ addslashes($day->note ?? '') }}')"
                        class="p-2 rounded-lg text-gray-400 hover:text-primary hover:bg-primary/10 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </button>
                <svg class="w-5 h-5 text-gray-400 transition-transform" id="chevron-{{ $day->id }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- Day body --}}
        <div id="day-body-{{ $day->id }}" class="border-t border-gray-50">

            {{-- Activities list --}}
            <div class="px-6 py-4 space-y-3" id="activities-{{ $day->id }}">
                @forelse($day->activities as $activity)
                    @include('schedule._activity', compact('activity', 'trip'))
                @empty
                    <div class="py-8 text-center text-gray-400 text-sm" id="empty-{{ $day->id }}">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Chưa có hoạt động nào. Hãy thêm hoạt động đầu tiên!
                    </div>
                @endforelse
            </div>

            {{-- Add activity form --}}
            <div class="px-6 pb-5">
                <button onclick="toggleAddForm({{ $day->id }})"
                        class="flex items-center gap-2 text-sm font-medium text-primary hover:text-primary-dark transition py-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Thêm hoạt động
                </button>

                <div id="add-form-{{ $day->id }}" class="hidden mt-3">
                    @include('schedule._add_form', ['trip' => $trip, 'day' => $day])
                </div>
            </div>
        </div>
    </div>
    @empty
        <div class="bg-white rounded-xl border border-dashed border-gray-200 p-16 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500">Lịch trình chưa được tạo. Hãy kiểm tra ngày bắt đầu/kết thúc của chuyến đi.</p>
        </div>
    @endforelse
</div>

{{-- Modal: sửa tiêu đề ngày --}}
<div id="day-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-5">Chỉnh sửa ngày</h3>
        <form id="day-modal-form" method="POST">
            @csrf @method('PATCH')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tiêu đề ngày</label>
                    <input type="text" name="title" id="day-title-input"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition"
                           placeholder="VD: Khám phá phố cổ">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ghi chú</label>
                    <textarea name="note" id="day-note-input" rows="3"
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition resize-none"
                              placeholder="Lưu ý cho ngày này..."></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="closeDayModal()"
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

@include('schedule._edit_modal')

@endsection

@push('scripts')
<script>
// ── Accordion ─────────────────────────────────────────────────────────
function toggleDay(id) {
    const body = document.getElementById(`day-body-${id}`);
    const chevron = document.getElementById(`chevron-${id}`);
    body.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
}

// ── Add form toggle ────────────────────────────────────────────────────
function toggleAddForm(id) {
    const form = document.getElementById(`add-form-${id}`);
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        form.querySelector('input[name="title"]')?.focus();
    }
}

// ── Edit day modal ─────────────────────────────────────────────────────
function openDayModal(dayId, title, note) {
    const modal   = document.getElementById('day-modal');
    const form    = document.getElementById('day-modal-form');
    form.action   = `/trips/{{ $trip->id }}/schedule/days/${dayId}`;
    document.getElementById('day-title-input').value = title;
    document.getElementById('day-note-input').value  = note;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeDayModal() {
    const modal = document.getElementById('day-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.getElementById('day-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDayModal();
});

// ── Edit activity modal ────────────────────────────────────────────────
function openEditModal(id, title, desc, type, startTime, endTime, location, cost, url) {
    const modal = document.getElementById('edit-modal');
    document.getElementById('edit-form').action = `/trips/{{ $trip->id }}/schedule/activities/${id}`;
    document.getElementById('edit-title').value       = title;
    document.getElementById('edit-description').value = desc;
    document.getElementById('edit-type').value        = type;
    document.getElementById('edit-start_time').value  = startTime;
    document.getElementById('edit-end_time').value    = endTime;
    document.getElementById('edit-location').value    = location;
    document.getElementById('edit-estimated_cost').value = cost;
    document.getElementById('edit-reference_url').value  = url;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeEditModal() {
    const modal = document.getElementById('edit-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// ── Comment toggle ─────────────────────────────────────────────────────
function toggleComments(id) {
    document.getElementById(`comments-${id}`).classList.toggle('hidden');
}

// ── Vote (AJAX) ────────────────────────────────────────────────────────
async function castVote(activityId, voteType) {
    const upBtn   = document.getElementById(`btn-up-${activityId}`);
    const downBtn = document.getElementById(`btn-down-${activityId}`);

    try {
        const res = await fetch(`/trips/{{ $trip->id }}/schedule/activities/${activityId}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ vote: voteType }),
        });

        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            console.error('Vote failed', res.status, err);
            return;
        }

        const data = await res.json();

        document.getElementById(`up-count-${activityId}`).textContent   = data.up_count;
        document.getElementById(`down-count-${activityId}`).textContent = data.down_count;

        setVoteState(upBtn,   data.user_vote === 'up',   'text-green-600', 'text-gray-400');
        setVoteState(downBtn, data.user_vote === 'down', 'text-red-500',   'text-gray-400');
    } catch (e) {
        console.error('Vote error:', e);
    }
}

function setVoteState(btn, isActive, activeClass, inactiveClass) {
    if (isActive) {
        btn.classList.add(activeClass);
        btn.classList.remove(inactiveClass);
    } else {
        btn.classList.remove(activeClass);
        btn.classList.add(inactiveClass);
    }
}
</script>
@endpush
