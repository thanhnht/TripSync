@extends('layouts.app')
@section('title', 'Chi tiêu – ' . $trip->name)
@section('page-title', 'Quản lý chi tiêu')
@section('page-subtitle', $trip->name . ' · ' . $trip->destination)

@section('header-actions')
    <a href="{{ route('trips.show', $trip) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
        ← Quay lại chuyến đi
    </a>
@endsection

@section('content')

{{-- Stats bar --}}
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900 leading-tight">{{ number_format($totalExpense, 0, ',', '.') }} ₫</p>
            <p class="text-xs text-gray-500 mt-0.5">Tổng chi tiêu</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900 leading-tight">{{ $expenses->count() }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Khoản chi</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900 leading-tight">{{ $members->count() }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Người tham gia</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Left: expense list + add form --}}
    <div class="col-span-2 space-y-5">

        {{-- Import from schedule --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-blue-800">Nhập từ lịch trình</p>
                    <p class="text-xs text-blue-600">Tự động tạo khoản chi từ các hoạt động đã duyệt có chi phí</p>
                </div>
            </div>
            <form method="POST" action="{{ route('expense.import', $trip) }}">
                @csrf
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl hover:bg-blue-700 transition whitespace-nowrap">
                    Nhập tự động
                </button>
            </form>
        </div>

        {{-- Expense list --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-800">Danh sách chi tiêu</h2>
                <button onclick="toggleAddExpense()"
                        class="flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary-dark transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Thêm khoản chi
                </button>
            </div>

            {{-- Add form (collapsible) --}}
            <div id="add-expense-panel" class="hidden bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Thêm khoản chi mới</h3>
                @include('expense._form', [
                    'formAction' => route('expense.store', $trip),
                    'method'     => 'POST',
                ])
            </div>

            @forelse($expenses as $expense)
                @include('expense._expense_card', compact('expense', 'trip'))
            @empty
                <div class="bg-white rounded-xl border border-dashed border-gray-200 p-16 text-center">
                    <p class="text-4xl mb-4">💸</p>
                    <p class="text-gray-500">Chưa có khoản chi nào.</p>
                    <p class="text-sm text-gray-400 mt-1">Thêm khoản chi hoặc nhập từ lịch trình.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Right: balance + settlements --}}
    <div class="space-y-5">

        {{-- Balance per member --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Số dư từng người</h3>
            @if($members->isEmpty())
                <p class="text-xs text-gray-400 text-center py-4">Chưa có thành viên.</p>
            @else
                <div class="space-y-3">
                    @foreach($members as $member)
                    @php $bal = $balance[$member->id] ?? 0; @endphp
                    <div class="flex items-center gap-3">
                        <img src="{{ $member->avatar_url }}" class="w-8 h-8 rounded-full object-cover shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-700 truncate">{{ $member->name }}</p>
                            <p class="text-xs {{ $bal > 0 ? 'text-green-600' : ($bal < 0 ? 'text-red-500' : 'text-gray-400') }} font-semibold">
                                {{ $bal > 0 ? '+' : '' }}{{ number_format($bal, 0, ',', '.') }} ₫
                            </p>
                        </div>
                        @if($bal > 0)
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Được nhận</span>
                        @elseif($bal < 0)
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Cần trả</span>
                        @else
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Cân bằng</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Settlement suggestions --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Giao dịch thanh toán</h3>
            @if(empty($settlements))
                <div class="text-center py-4">
                    <p class="text-2xl mb-2">🎉</p>
                    <p class="text-xs text-gray-500">Mọi người đã cân bằng!</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($settlements as $s)
                    @php
                        $fromUser = $members->firstWhere('id', $s['from']);
                        $toUser   = $members->firstWhere('id', $s['to']);
                    @endphp
                    @if($fromUser && $toUser)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3">
                        <div class="flex items-center gap-2">
                            <img src="{{ $fromUser->avatar_url }}" class="w-6 h-6 rounded-full object-cover shrink-0">
                            <span class="text-xs font-medium text-gray-700 truncate">{{ $fromUser->name }}</span>
                            <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                            <img src="{{ $toUser->avatar_url }}" class="w-6 h-6 rounded-full object-cover shrink-0">
                            <span class="text-xs font-medium text-gray-700 truncate">{{ $toUser->name }}</span>
                        </div>
                        <p class="text-sm font-bold text-amber-700 mt-1.5">
                            {{ number_format($s['amount'], 0, ',', '.') }} ₫
                        </p>
                    </div>
                    @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Edit expense modal --}}
<div id="edit-expense-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-900 mb-5">Chỉnh sửa khoản chi</h3>
        <div id="edit-expense-form-container"></div>
    </div>
</div>

@endsection

@php
    $expenseJson = $expenses->keyBy('id')->map(fn($e) => [
        'id'           => $e->id,
        'title'        => $e->title,
        'amount'       => $e->amount,
        'paid_by'      => $e->paid_by,
        'split_method' => $e->split_method,
        'note'         => $e->note ?? '',
        'splits'       => $e->splits->map(fn($s) => ['user_id' => $s->user_id, 'amount' => $s->amount])->values(),
    ]);
    $membersJson = $members->map(fn($m) => ['id' => $m->id, 'name' => $m->name, 'avatar' => $m->avatar_url])->values();
@endphp

@push('scripts')
<script>
const expenseData = @json($expenseJson);
const members = @json($membersJson);
const tripId  = {{ $trip->id }};

function toggleAddExpense() {
    const panel = document.getElementById('add-expense-panel');
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        panel.querySelector('input[name="title"]')?.focus();
    }
}

function openExpenseModal(id) {
    const e = expenseData[id];
    if (!e) return;

    const container = document.getElementById('edit-expense-form-container');
    container.innerHTML = buildEditForm(e);

    const modal = document.getElementById('edit-expense-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeExpenseModal() {
    const modal = document.getElementById('edit-expense-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('edit-expense-modal').addEventListener('click', function(ev) {
    if (ev.target === this) closeExpenseModal();
});

function toggleCustomSplits(show) {
    ['add-custom-splits', 'edit-custom-splits'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('hidden', !show);
    });
}

function buildEditForm(e) {
    const membersHtml = members.map((m, i) => {
        const split = e.splits.find(s => s.user_id === m.id);
        return `
        <div class="flex items-center gap-3">
            <img src="${m.avatar}" class="w-7 h-7 rounded-full object-cover shrink-0">
            <span class="text-sm text-gray-700 flex-1">${m.name}</span>
            <input type="hidden" name="splits[${i}][user_id]" value="${m.id}">
            <input type="number" name="splits[${i}][amount]" value="${split ? split.amount : 0}" min="0"
                   class="w-32 px-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition"
                   placeholder="0">
            <span class="text-xs text-gray-400">₫</span>
        </div>`;
    }).join('');

    const memberOptions = members.map(m =>
        `<option value="${m.id}" ${m.id === e.paid_by ? 'selected' : ''}>${m.name}</option>`
    ).join('');

    return `
    <form method="POST" action="/trips/${tripId}/expenses/${e.id}" id="edit-expense-form">
        <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
        <input type="hidden" name="_method" value="PUT">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tên khoản chi</label>
                <input type="text" name="title" value="${escHtml(e.title)}" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Số tiền (₫)</label>
                <input type="number" name="amount" value="${e.amount}" min="0" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Người trả</label>
                <select name="paid_by" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition">
                    ${memberOptions}
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Cách chia</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 px-4 py-3 rounded-xl border border-gray-200 cursor-pointer transition">
                        <input type="radio" name="split_method" value="equal" ${e.split_method === 'equal' ? 'checked' : ''}
                               class="text-primary" onchange="toggleCustomSplits(false)">
                        <span class="text-sm font-medium text-gray-700">Chia đều</span>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-3 rounded-xl border border-gray-200 cursor-pointer transition">
                        <input type="radio" name="split_method" value="custom" ${e.split_method === 'custom' ? 'checked' : ''}
                               class="text-primary" onchange="toggleCustomSplits(true)">
                        <span class="text-sm font-medium text-gray-700">Tuỳ chỉnh</span>
                    </label>
                </div>
            </div>
            <div id="edit-custom-splits" class="${e.split_method === 'custom' ? '' : 'hidden'}">
                <label class="block text-sm font-medium text-gray-700 mb-2">Phân bổ chi tiêu</label>
                <div class="space-y-2">${membersHtml}</div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ghi chú</label>
                <textarea name="note" rows="2"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition resize-none">${escHtml(e.note)}</textarea>
            </div>
        </div>
        <div class="flex gap-3 mt-5">
            <button type="button" onclick="closeExpenseModal()"
                    class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Huỷ
            </button>
            <button type="submit"
                    class="flex-1 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-dark transition">
                Cập nhật
            </button>
        </div>
    </form>`;
}

function escHtml(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endpush
