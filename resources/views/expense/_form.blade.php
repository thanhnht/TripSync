{{-- Expense form (shared for add & edit) --}}
{{-- Variables: $formAction, $method ('POST'|'PUT'), $expense (optional, for edit) --}}
@php
    $isEdit = isset($expense);
    $val = fn(string $field, $default = '') => old($field, $isEdit ? $expense->{$field} : $default);
@endphp

<form method="POST" action="{{ $formAction }}" id="{{ $isEdit ? 'edit-expense-form' : 'add-expense-form' }}">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    <div class="space-y-4">
        {{-- Title --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tên khoản chi <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ $val('title') }}" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition"
                   placeholder="VD: Tiền khách sạn, Ăn trưa...">
        </div>

        {{-- Amount --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Số tiền (₫) <span class="text-red-500">*</span></label>
            <input type="number" name="amount" value="{{ $val('amount', 0) }}" min="0" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition"
                   placeholder="0">
        </div>

        {{-- Paid by --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Người trả <span class="text-red-500">*</span></label>
            <select name="paid_by" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition">
                @foreach($members as $member)
                    <option value="{{ $member->id }}"
                        {{ (int)$val('paid_by', Auth::id()) === $member->id ? 'selected' : '' }}>
                        {{ $member->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Split method --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Cách chia</label>
            <div class="grid grid-cols-2 gap-2">
                <label class="flex items-center gap-2 px-4 py-3 rounded-xl border border-gray-200 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition">
                    <input type="radio" name="split_method" value="equal"
                           {{ $val('split_method', 'equal') === 'equal' ? 'checked' : '' }}
                           class="text-primary" onchange="toggleCustomSplits(false)">
                    <span class="text-sm font-medium text-gray-700">Chia đều</span>
                </label>
                <label class="flex items-center gap-2 px-4 py-3 rounded-xl border border-gray-200 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition">
                    <input type="radio" name="split_method" value="custom"
                           {{ $val('split_method', 'equal') === 'custom' ? 'checked' : '' }}
                           class="text-primary" onchange="toggleCustomSplits(true)">
                    <span class="text-sm font-medium text-gray-700">Tuỳ chỉnh</span>
                </label>
            </div>
        </div>

        {{-- Custom splits --}}
        <div id="{{ $isEdit ? 'edit-custom-splits' : 'add-custom-splits' }}"
             class="{{ $val('split_method', 'equal') === 'custom' ? '' : 'hidden' }}">
            <label class="block text-sm font-medium text-gray-700 mb-2">Phân bổ chi tiêu</label>
            <div class="space-y-2">
                @foreach($members as $i => $member)
                @php
                    $splitAmount = 0;
                    if ($isEdit) {
                        $splitAmount = $expense->splits->firstWhere('user_id', $member->id)?->amount ?? 0;
                    }
                @endphp
                <div class="flex items-center gap-3">
                    <img src="{{ $member->avatar_url }}" class="w-7 h-7 rounded-full object-cover shrink-0">
                    <span class="text-sm text-gray-700 flex-1">{{ $member->name }}</span>
                    <input type="hidden" name="splits[{{ $i }}][user_id]" value="{{ $member->id }}">
                    <input type="number" name="splits[{{ $i }}][amount]"
                           value="{{ old("splits.{$i}.amount", $splitAmount) }}"
                           min="0"
                           class="w-32 px-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition"
                           placeholder="0">
                    <span class="text-xs text-gray-400">₫</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Note --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Ghi chú</label>
            <textarea name="note" rows="2"
                      class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400 transition resize-none"
                      placeholder="Ghi chú thêm...">{{ $val('note') }}</textarea>
        </div>
    </div>

    <div class="flex gap-3 mt-5">
        @if($isEdit)
            <button type="button" onclick="closeExpenseModal()"
                    class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Huỷ
            </button>
        @endif
        <button type="submit"
                class="flex-1 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-dark transition">
            {{ $isEdit ? 'Cập nhật' : 'Thêm khoản chi' }}
        </button>
    </div>
</form>
