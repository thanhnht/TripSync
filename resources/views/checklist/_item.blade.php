<div class="group flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition {{ $item->is_done ? 'opacity-60' : '' }}"
     id="item-{{ $item->id }}">

    @php
        $canManage = $item->created_by === Auth::id()
                  || $item->assigned_to === Auth::id()
                  || $trip->isOwner(Auth::user());
    @endphp

    {{-- Checkbox toggle (only for creator / assignee / leader) --}}
    @if($canManage)
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
    <div class="w-5 h-5 rounded-full border-2 shrink-0
                {{ $item->is_done ? 'bg-green-500 border-green-500' : 'border-gray-200' }} flex items-center justify-center">
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

    {{-- Category badge (shown on unassigned items) --}}
    @if(!$item->assigned_to && $item->category !== 'Chung')
        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full shrink-0">
            {{ $item->category }}
        </span>
    @endif

    {{-- Assignee badge (assigned items) --}}
    @if($item->assignee)
        <div class="flex items-center gap-1.5 shrink-0">
            <img src="{{ $item->assignee->avatar_url }}" class="w-5 h-5 rounded-full object-cover">
            <span class="text-xs text-gray-500">{{ $item->assignee->name }}</span>
        </div>
    @endif

    {{-- Leader: custom assign dropdown (unassigned items only) --}}
    @if(!$item->assigned_to && $trip->isOwner(Auth::user()))
        <form method="POST" action="{{ route('checklist.assign', [$trip, $item]) }}"
              id="assign-form-{{ $item->id }}" class="shrink-0">
            @csrf @method('PATCH')
            <input type="hidden" name="assigned_to" id="assign-input-{{ $item->id }}">

            <div class="relative" id="assign-wrap-{{ $item->id }}">
                <button type="button"
                        onclick="toggleAssignDropdown({{ $item->id }}, event)"
                        class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-dashed border-gray-300 text-xs text-gray-400 hover:border-primary hover:text-primary transition bg-white whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Phân công
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="assign-menu-{{ $item->id }}"
                     class="dropdown-menu hidden absolute right-0 top-full mt-1.5 w-44 bg-white rounded-xl border border-gray-100 shadow-xl py-1.5 z-20">
                    @foreach($members as $m)
                    <button type="button"
                            onclick="selectAssignee({{ $item->id }}, {{ $m->id }})"
                            class="w-full flex items-center gap-2.5 px-3 py-2 hover:bg-gray-50 transition text-left">
                        <img src="{{ $m->avatar_url }}" class="w-6 h-6 rounded-full object-cover shrink-0">
                        <span class="text-sm text-gray-700 truncate">{{ $m->name }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
        </form>
    @endif

    {{-- Actions (hover) --}}
    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition shrink-0">
        <button onclick="openEditItem({{ $item->id }}, '{{ addslashes($item->content) }}', '{{ addslashes($item->category) }}', {{ $item->assigned_to ?? 'null' }})"
                class="p-1.5 rounded-lg text-gray-400 hover:text-primary hover:bg-primary/10 transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
        </button>
        @if($canManage)
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
