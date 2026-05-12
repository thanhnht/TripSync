<div class="group relative bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-all"
     id="photo-{{ $photo->id }}"
     data-photo-id="{{ $photo->id }}">

    {{-- Thumbnail --}}
    <div class="aspect-square overflow-hidden bg-gray-100 cursor-pointer select-none"
         onclick="openLightbox('{{ $photo->url }}', '{{ addslashes($photo->description ?? $photo->original_name) }}')">
        <img src="{{ $photo->url }}"
             alt="{{ $photo->description ?? $photo->original_name }}"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out"
             loading="lazy">
    </div>

    {{-- Checkbox (always visible) --}}
    <label class="absolute top-2 left-2 cursor-pointer" onclick="event.stopPropagation()">
        <input type="checkbox" name="ids[]" value="{{ $photo->id }}" form="bulk-download-form"
               class="w-5 h-5 rounded border-2 border-white shadow-md accent-primary cursor-pointer bg-white/80"
               onchange="onCheckChange()">
    </label>

    {{-- Selected overlay --}}
    <div class="selected-overlay absolute inset-0 border-2 border-primary rounded-xl hidden pointer-events-none bg-primary/10"></div>

    {{-- Action buttons (hover) --}}
    <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition">
        <a href="{{ route('photo.download', [$trip, $photo]) }}"
           title="Tải về"
           onclick="event.stopPropagation()"
           class="p-1.5 bg-white/90 backdrop-blur-sm rounded-lg text-gray-600 hover:text-primary hover:bg-white transition shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
        </a>

        @if($photo->uploaded_by === Auth::id() || $trip->isOwner(Auth::user()))
        <form method="POST" action="{{ route('photo.destroy', [$trip, $photo]) }}"
              onsubmit="return confirm('Xoá ảnh này?')" onclick="event.stopPropagation()">
            @csrf @method('DELETE')
            <button type="submit" title="Xoá"
                    class="p-1.5 bg-white/90 backdrop-blur-sm rounded-lg text-gray-600 hover:text-red-500 hover:bg-white transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </form>
        @endif
    </div>

    {{-- Caption --}}
    <div class="p-3">
        @if($photo->description)
            <p class="text-xs text-gray-700 truncate font-medium mb-1">{{ $photo->description }}</p>
        @endif
        <div class="flex items-center gap-2">
            <img src="{{ $photo->uploader->avatar_url }}" class="w-5 h-5 rounded-full object-cover shrink-0">
            <span class="text-xs text-gray-500 truncate">{{ $photo->uploader->name }}</span>
            <span class="text-xs text-gray-400 ml-auto shrink-0">{{ $photo->created_at->diffForHumans() }}</span>
        </div>
    </div>
</div>
