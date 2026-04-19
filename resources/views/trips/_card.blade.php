{{-- resources/views/trips/_card.blade.php --}}
@php
    $statusColors = [
        'planning'  => 'bg-blue-100 text-blue-700',
        'ongoing'   => 'bg-green-100 text-green-700',
        'completed' => 'bg-gray-100 text-gray-600',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
    $statusColor = $statusColors[$trip->status] ?? 'bg-gray-100 text-gray-600';
@endphp

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all group">
    {{-- Cover --}}
    <a href="{{ route('trips.show', $trip) }}" class="block relative h-40 overflow-hidden bg-gradient-to-br from-blue-400 to-indigo-600">
        @if($trip->cover_image)
            <img src="{{ $trip->cover_image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $trip->name }}">
        @else
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-12 h-12 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                </svg>
            </div>
        @endif
        <div class="absolute top-3 right-3">
            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                {{ $trip->status_label }}
            </span>
        </div>
        @if($trip->isOwner(Auth::user()))
            <div class="absolute top-3 left-3">
                <span class="px-2 py-1 rounded-full text-xs font-medium bg-black/50 text-white backdrop-blur-sm">
                    Trưởng nhóm
                </span>
            </div>
        @endif
    </a>

    {{-- Content --}}
    <div class="p-4">
        <a href="{{ route('trips.show', $trip) }}">
            <h3 class="font-semibold text-gray-900 hover:text-primary transition truncate">{{ $trip->name }}</h3>
        </a>
        <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ $trip->destination }}
        </p>

        <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-50">
            <div class="text-xs text-gray-500">
                <span>{{ $trip->start_date->format('d/m') }}</span>
                <span class="mx-1">→</span>
                <span>{{ $trip->end_date->format('d/m/Y') }}</span>
                <span class="ml-1 text-gray-400">({{ $trip->days_count }} ngày)</span>
            </div>
            <div class="flex -space-x-2">
                @foreach($trip->members->take(3) as $member)
                    <img src="{{ $member->avatar_url }}" class="w-6 h-6 rounded-full ring-2 ring-white object-cover" title="{{ $member->name }}">
                @endforeach
                @if($trip->members_count > 3)
                    <div class="w-6 h-6 rounded-full ring-2 ring-white bg-gray-100 flex items-center justify-center">
                        <span class="text-xs text-gray-600 font-medium">+{{ $trip->members_count - 3 }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
