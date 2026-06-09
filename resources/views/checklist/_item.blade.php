<div class="group flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition {{ $item->is_done ? 'opacity-60' : '' }}"
     id="item-{{ $item->id }}">

    @php
        $isOwner   = $trip->isOwner(Auth::user());
        $canToggle = $isOwner
            || ($item->assigned_to ? $item->assigned_to === Auth::id() : $item->created_by === Auth::id());
        $canManage = $isOwner || $item->created_by === Auth::id() || $item->assigned_to === Auth::id();
    @endphp

    {{-- Checkbox toggle --}}
    @if($canToggle)
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
    @else
    <div class="w-5 h-5 rounded-full border-2 shrink-0 cursor-not-allowed
                {{ $item->is_done ? 'bg-green-500 border-green-500' : 'border-gray-200' }} flex items-center justify-center"
         title="{{ $item->assigned_to ? 'Chỉ người được phân công mới tick được' : '' }}">
        @if($item->is_done)
            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        @endif
    </div>
    @endif

    {{-- Content --}}
    <span class="flex-1 text-sm text-gray-800 {{ $item->is_done ? 'line-through text-gray-400' : '' }}"
          id="item-text-{{ $item->id }}">
        {{ $item->content }}
    </span>

    {{-- Category badge (unassigned + non-owner view) --}}
    @if(!$item->assigned_to && !$isOwner && $item->category !== 'Chung')
        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full shrink-0">
            {{ $item->category }}
        </span>
    @endif

    {{-- Assignee area --}}
    @if($isOwner)
        <form method="POST" action="{{ route('checklist.assign', [$trip, $item]) }}" class="shrink-0">
            @csrf
            <select name="assigned_to"
                    onchange="this.form.submit()"
                    class="text-xs rounded-lg border border-gray-200 px-2 py-1.5 text-gray-700 bg-white focus:outline-none focus:border-blue-400 cursor-pointer">
                <option value="">— Bỏ Phân công —</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}" {{ $item->assigned_to === $m->id ? 'selected' : '' }}>
                        {{ $m->name }}
                    </option>
                @endforeach
            </select>
        </form>
    @elseif($item->assignee)
        {{-- Non-leader: read-only assignee badge --}}
        <div class="flex items-center gap-1.5 shrink-0">
            <img src="{{ $item->assignee->avatar_url }}" class="w-5 h-5 rounded-full object-cover">
            <span class="text-xs text-gray-500">{{ $item->assignee->name }}</span>
        </div>
    @endif

    {{-- Edit / Delete (hover only) --}}
    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition shrink-0">
        @if($canManage)
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
        @endif
    </div>
</div>
