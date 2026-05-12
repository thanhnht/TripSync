@php
    $statusMap = [
        'planning'  => ['label' => 'Lên kế hoạch', 'cls' => 'bg-blue-100 text-blue-700'],
        'ongoing'   => ['label' => 'Đang diễn ra',  'cls' => 'bg-emerald-100 text-emerald-700'],
        'completed' => ['label' => 'Hoàn thành',     'cls' => 'bg-slate-100 text-slate-600'],
        'cancelled' => ['label' => 'Đã huỷ',         'cls' => 'bg-red-100 text-red-600'],
    ];
    $s = $statusMap[$trip->status] ?? $statusMap['planning'];
@endphp

<a href="{{ route('trips.show', $trip) }}"
   class="group block bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all duration-200">

    {{-- Cover --}}
    <div class="relative h-36 overflow-hidden bg-gradient-to-br from-sky-400 via-blue-500 to-blue-700">
        @if($trip->cover_image)
            <img src="{{ $trip->cover_image_url }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                 alt="{{ $trip->name }}">
        @else
            <div class="absolute inset-0 flex items-center justify-center opacity-30">
                <svg class="w-14 h-14 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                </svg>
            </div>
        @endif

        {{-- Status badge --}}
        <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $s['cls'] }} bg-white/90 backdrop-blur-sm">
            {{ $s['label'] }}
        </span>

        @if($trip->isOwner(Auth::user()))
            <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-black/40 text-white backdrop-blur-sm">
                Trưởng nhóm
            </span>
        @endif
    </div>

    {{-- Body --}}
    <div class="p-4">
        <h3 class="font-semibold text-gray-900 text-[15px] truncate group-hover:text-blue-600 transition-colors">
            {{ $trip->name }}
        </h3>
        <p class="text-[13px] text-gray-500 mt-0.5 flex items-center gap-1 truncate">
            <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ $trip->destination }}
        </p>

        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
            <p class="text-[12px] text-gray-400">
                {{ $trip->start_date->format('d/m') }} → {{ $trip->end_date->format('d/m/Y') }}
                <span class="ml-1">({{ $trip->days_count }} ngày)</span>
            </p>

            <div class="flex -space-x-1.5">
                @foreach($trip->members->take(3) as $member)
                    <img src="{{ $member->avatar_url }}"
                         class="w-6 h-6 rounded-full ring-2 ring-white object-cover"
                         title="{{ $member->name }}">
                @endforeach
                @if($trip->members_count > 3)
                    <div class="w-6 h-6 rounded-full ring-2 ring-white bg-gray-100 flex items-center justify-center">
                        <span class="text-[10px] font-semibold text-gray-500">+{{ $trip->members_count - 3 }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</a>
