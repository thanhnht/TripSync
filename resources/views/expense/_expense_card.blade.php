@php
    $isLinked = $expense->trip_activity_id !== null;
@endphp

<div class="group bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all p-4"
     id="expense-{{ $expense->id }}">

    <div class="flex items-start gap-3">
        {{-- Icon --}}
        <div class="w-9 h-9 rounded-lg {{ $isLinked ? 'bg-blue-50' : 'bg-gray-50' }} flex items-center justify-center shrink-0 mt-0.5">
            @if($isLinked)
                <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            @else
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            @endif
        </div>

        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h4 class="font-semibold text-gray-900 text-sm">{{ $expense->title }}</h4>
                @if($isLinked)
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 border border-blue-200">
                        Từ lịch trình
                    </span>
                @endif
                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                    {{ $expense->splitMethodLabel() }}
                </span>
            </div>

            <div class="flex items-center flex-wrap gap-3 mt-1.5">
                <span class="text-sm font-bold text-emerald-600">{{ $expense->formatted_amount }}</span>
                <span class="text-xs text-gray-500 flex items-center gap-1">
                    <img src="{{ $expense->payer->avatar_url }}" class="w-4 h-4 rounded-full object-cover shrink-0">
                    {{ $expense->payer->name }} trả
                </span>
                <span class="text-xs text-gray-400">
                    {{ $expense->created_at->format('d/m/Y') }}
                </span>
            </div>

            @if($expense->note)
                <p class="text-xs text-gray-400 mt-1 italic">{{ $expense->note }}</p>
            @endif

            {{-- Splits --}}
            @if($expense->splits->count())
            <div class="mt-2 flex flex-wrap gap-1.5">
                @foreach($expense->splits as $split)
                    <span class="inline-flex items-center gap-1 text-xs bg-gray-50 border border-gray-100 rounded-lg px-2 py-0.5">
                        <img src="{{ $split->user->avatar_url }}" class="w-4 h-4 rounded-full object-cover">
                        {{ $split->user->name }}:
                        <span class="font-medium text-gray-700">{{ number_format($split->amount, 0, ',', '.') }}₫</span>
                    </span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition">
            @if($expense->created_by === Auth::id() || $trip->isOwner(Auth::user()))
                <button onclick="openExpenseModal({{ $expense->id }})"
                        title="Chỉnh sửa"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-primary hover:bg-primary/10 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </button>

                <form method="POST" action="{{ route('expense.destroy', [$trip, $expense]) }}"
                      onsubmit="return confirm('Xoá khoản chi này?')">
                    @csrf @method('DELETE')
                    <button type="submit" title="Xoá"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
