@php
    $badge = $activity->status_badge;
    $statusColors = [
        'green'  => 'bg-green-100 text-green-700 border-green-200',
        'red'    => 'bg-red-100 text-red-700 border-red-200',
        'yellow' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
    ];
    $leftBorderColors = [
        'approved' => 'border-l-green-400',
        'rejected' => 'border-l-red-400',
        'suggested'=> 'border-l-yellow-400',
    ];
@endphp

<div class="group relative bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all
            border-l-4 {{ $leftBorderColors[$activity->status] ?? 'border-l-gray-200' }}
            {{ $activity->status === 'rejected' ? 'opacity-60' : '' }}"
     id="activity-{{ $activity->id }}">

    <div class="p-4">
        {{-- Top row: icon + title + badges + actions --}}
        <div class="flex items-start gap-3">
            {{-- Type icon --}}
            <div class="w-9 h-9 rounded-lg bg-gray-50 flex items-center justify-center text-lg shrink-0 mt-0.5">
                {{ $activity->type_icon }}
            </div>

            {{-- Title & meta --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="font-semibold text-gray-900 text-sm">{{ $activity->title }}</h4>

                    {{-- Status badge --}}
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium border {{ $statusColors[$badge['color']] }}">
                        {{ $badge['label'] }}
                    </span>
                </div>

                {{-- Meta row --}}
                <div class="flex items-center flex-wrap gap-3 mt-1.5">
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                        {{ $activity->type_label }}
                    </span>

                    @if($activity->time_range)
                        <span class="text-xs text-gray-500 flex items-center gap-1">
                            <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $activity->time_range }}
                        </span>
                    @endif

                    @if($activity->location)
                        <span class="text-xs text-gray-500 flex items-center gap-1 truncate max-w-xs">
                            <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $activity->location }}
                        </span>
                    @endif

                    @if($activity->estimated_cost > 0)
                        <span class="text-xs text-emerald-600 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $activity->formatted_cost }}
                        </span>
                    @endif
                </div>

                @if($activity->description)
                    <p class="text-xs text-gray-500 mt-1.5 leading-relaxed line-clamp-2">
                        {{ $activity->description }}
                    </p>
                @endif

                @if($activity->reference_url)
                    <a href="{{ $activity->reference_url }}" target="_blank"
                       class="inline-flex items-center gap-1 text-xs text-blue-500 hover:underline mt-1">
                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Xem tham khảo
                    </a>
                @endif
            </div>

            {{-- Action buttons (right) --}}
            <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition">

                {{-- Owner: approve/reject --}}
                @if($trip->isOwner(Auth::user()) && $activity->status === 'suggested')
                    <form method="POST" action="{{ route('schedule.activities.approve', [$trip, $activity]) }}">
                        @csrf @method('PATCH')
                        <button type="submit" title="Duyệt"
                                class="p-1.5 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('schedule.activities.reject', [$trip, $activity]) }}">
                        @csrf @method('PATCH')
                        <button type="submit" title="Từ chối"
                                class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </form>
                @endif

                {{-- Edit (creator or owner) --}}
                @if($activity->created_by === Auth::id() || $trip->isOwner(Auth::user()))
                    <button onclick="openEditModal(
                                {{ $activity->id }},
                                '{{ addslashes($activity->title) }}',
                                '{{ addslashes($activity->description ?? '') }}',
                                '{{ $activity->type }}',
                                '{{ $activity->start_time ? substr($activity->start_time,0,5) : '' }}',
                                '{{ $activity->end_time   ? substr($activity->end_time,0,5)   : '' }}',
                                '{{ addslashes($activity->location ?? '') }}',
                                '{{ $activity->estimated_cost }}',
                                '{{ addslashes($activity->reference_url ?? '') }}'
                            )"
                            title="Chỉnh sửa"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-primary hover:bg-primary/10 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </button>

                    <form method="POST" action="{{ route('schedule.activities.destroy', [$trip, $activity]) }}"
                          onsubmit="return confirm('Xoá hoạt động này?')">
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

        {{-- Bottom row: votes + comment toggle + creator --}}
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">

            {{-- Vote buttons --}}
            <div class="flex items-center gap-3">
                <button id="btn-up-{{ $activity->id }}"
                        onclick="castVote({{ $activity->id }}, 'up')"
                        class="flex items-center gap-1.5 text-sm font-medium transition hover:text-green-600
                               {{ $activity->user_vote === 'up' ? 'text-green-600' : 'text-gray-400' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017a2 2 0 01-1.768-1.053l-1.48-2.96a2 2 0 00-1.768-1.053H4a2 2 0 01-2-2V8a2 2 0 012-2h2.293c.552 0 1.055-.224 1.414-.586l3.586-3.586A1 1 0 0112 2h0a2 2 0 012 2v4z"/>
                    </svg>
                    <span id="up-count-{{ $activity->id }}">{{ $activity->up_votes_count }}</span>
                </button>

                <button id="btn-down-{{ $activity->id }}"
                        onclick="castVote({{ $activity->id }}, 'down')"
                        class="flex items-center gap-1.5 text-sm font-medium transition hover:text-red-500
                               {{ $activity->user_vote === 'down' ? 'text-red-500' : 'text-gray-400' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 011.768 1.053l1.48 2.96a2 2 0 001.768 1.053H20a2 2 0 012 2v5a2 2 0 01-2 2h-2.293a2 2 0 00-1.414.586l-3.586 3.586A1 1 0 0112 22h0a2 2 0 01-2-2v-4z"/>
                    </svg>
                    <span id="down-count-{{ $activity->id }}">{{ $activity->down_votes_count }}</span>
                </button>

                <button onclick="toggleComments({{ $activity->id }})"
                        class="flex items-center gap-1.5 text-sm text-gray-400 hover:text-primary transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    {{ $activity->comments->count() }} bình luận
                </button>
            </div>

            {{-- Creator --}}
            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                <img src="{{ $activity->creator->avatar_url }}" class="w-5 h-5 rounded-full object-cover">
                {{ $activity->creator->name }}
            </div>
        </div>
    </div>

    {{-- Comments section --}}
    <div id="comments-{{ $activity->id }}" class="hidden border-t border-gray-50 bg-gray-50/50 px-4 py-4 rounded-b-xl">

        {{-- Comment list --}}
        @forelse($activity->comments as $comment)
        <div class="flex gap-3 mb-3 group/comment">
            <img src="{{ $comment->user->avatar_url }}" class="w-7 h-7 rounded-full object-cover shrink-0 mt-0.5">
            <div class="flex-1 bg-white rounded-xl px-3 py-2 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-700">{{ $comment->user->name }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                        @if($comment->user_id === Auth::id() || $trip->isOwner(Auth::user()))
                            <form method="POST" action="{{ route('schedule.comments.destroy', [$trip, $comment]) }}">
                                @csrf @method('DELETE')
                                <button class="text-gray-300 hover:text-red-400 transition opacity-0 group-hover/comment:opacity-100">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-0.5 leading-relaxed">{{ $comment->content }}</p>
            </div>
        </div>
        @empty
            <p class="text-xs text-gray-400 text-center py-2">Chưa có bình luận nào.</p>
        @endforelse

        {{-- Add comment form --}}
        <form method="POST" action="{{ route('schedule.activities.comment', [$trip, $activity]) }}"
              class="flex gap-2 mt-2">
            @csrf
            <img src="{{ Auth::user()->avatar_url }}" class="w-7 h-7 rounded-full object-cover shrink-0 mt-1">
            <div class="flex-1 flex gap-2">
                <input type="text" name="content" required
                       class="flex-1 px-3 py-2 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 transition"
                       placeholder="Viết bình luận...">
                <button type="submit"
                        class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition">
                    Gửi
                </button>
            </div>
        </form>
    </div>
</div>
