<div class="group flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition {{ $item->is_done ? 'opacity-60' : '' }}"
     id="item-{{ $item->id }}">

    {{-- Checkbox toggle --}}
    <button onclick="toggleItem({{ $item->id }}, this)"
            class="w-5 h-5 rounded-full border-2 shrink-0 flex items-center justify-center transition
                   {{ $item->is_done
                       ? 'bg-green-500 border-green-500 text-white'
                       : 'border-gray-300 hover:border-green-400' }}">
        @if($item->is_done)
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        @endif
    </button>

    {{-- Content --}}
    <span class="flex-1 text-sm text-gray-800 {{ $item->is_done ? 'line-through text-gray-400' : '' }}"
          id="item-text-{{ $item->id }}">
        {{ $item->content }}
    </span>

    {{-- Assignee badge --}}
    @if($item->assignee)
        <div class="flex items-center gap-1.5 shrink-0">
            <img src="{{ $item->assignee->avatar_url }}" class="w-5 h-5 rounded-full object-cover">
            <span class="text-xs text-gray-500">{{ $item->assignee->name }}</span>
        </div>
    @endif

    {{-- Actions (hover) --}}
    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition shrink-0">
        <button onclick="openEditItem({{ $item->id }}, '{{ addslashes($item->content) }}', '{{ addslashes($item->category) }}', {{ $item->assigned_to ?? 'null' }})"
                class="p-1.5 rounded-lg text-gray-400 hover:text-primary hover:bg-primary/10 transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
        </button>
        <form method="POST" action="{{ route('checklist.destroy', [$trip, $item]) }}"
              onsubmit="return confirm('Xoá mục này?')">
            @csrf @method('DELETE')
            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </form>
    </div>
</div>
