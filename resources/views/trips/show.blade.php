@extends('layouts.app')
@section('title', $trip->name)
@section('page-title', $trip->name)
@section('page-subtitle', $trip->destination)

@section('header-actions')
    @if($trip->isOwner(Auth::user()))
        <a href="{{ route('trips.edit', $trip) }}"
           class="flex items-center gap-2 px-3.5 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            Chỉnh sửa
        </a>
        <form method="POST" action="{{ route('trips.destroy', $trip) }}"
              onsubmit="return confirm('Bạn chắc chắn muốn xoá chuyến đi này?')">
            @csrf @method('DELETE')
            <button class="flex items-center gap-2 px-3.5 py-2 rounded-lg border border-red-200 text-sm font-medium text-red-500 hover:bg-red-50 transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Xoá
            </button>
        </form>
    @else
        <form method="POST" action="{{ route('trips.leave', $trip) }}"
              onsubmit="return confirm('Bạn chắc chắn muốn rời chuyến đi này?')">
            @csrf
            <button class="flex items-center gap-2 px-3.5 py-2 rounded-lg border border-orange-200 text-sm font-medium text-orange-500 hover:bg-orange-50 transition">
                Rời chuyến đi
            </button>
        </form>
    @endif
@endsection

@section('content')
@php
    $sc = [
        'planning'  => 'bg-blue-500/90',
        'ongoing'   => 'bg-emerald-500/90',
        'completed' => 'bg-slate-500/90',
        'cancelled' => 'bg-red-500/90',
    ];
@endphp
<div class="space-y-4">

    {{-- ── Cover (full width) ───────────────────────────────────── --}}
    <div class="rounded-xl overflow-hidden h-44 bg-gradient-to-br from-blue-500 to-blue-700 relative">
        @if($trip->cover_image)
            <img src="{{ $trip->cover_image_url }}" class="w-full h-full object-cover">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/10 to-transparent flex items-end px-5 py-4">
            <div class="flex items-center gap-2.5">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold text-white backdrop-blur-sm {{ $sc[$trip->status] ?? 'bg-slate-500/90' }}">
                    {{ $trip->status_label }}
                </span>
                <span class="text-white/75 text-xs">{{ $trip->days_count }} ngày</span>
                <span class="text-white/35 text-xs">·</span>
                <span class="text-white/75 text-xs">{{ $trip->members->count() }} thành viên</span>
            </div>
        </div>
    </div>

    {{-- ── Main grid (2+1) ──────────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-4 items-start">

        {{-- Left col ------------------------------------------------ --}}
        <div class="col-span-2 space-y-4">

            {{-- Info strip --}}
            <div class="bg-white rounded-xl border border-gray-100 px-5 py-4">
                <div class="flex items-start divide-x divide-gray-100">
                    <div class="pr-5">
                        <p class="text-[10.5px] font-medium text-gray-400 uppercase tracking-wider">Điểm đến</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $trip->destination }}</p>
                    </div>
                    <div class="px-5">
                        <p class="text-[10.5px] font-medium text-gray-400 uppercase tracking-wider">Khởi hành</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $trip->start_date->format('d/m/Y') }}</p>
                    </div>
                    <div class="px-5">
                        <p class="text-[10.5px] font-medium text-gray-400 uppercase tracking-wider">Kết thúc</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $trip->end_date->format('d/m/Y') }}</p>
                    </div>
                    <div class="pl-5">
                        <p class="text-[10.5px] font-medium text-gray-400 uppercase tracking-wider">Thời gian</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $trip->days_count }} ngày</p>
                    </div>
                </div>
                @if($trip->description)
                    <p class="mt-3 pt-3 border-t border-gray-50 text-sm text-gray-500 leading-relaxed">{{ $trip->description }}</p>
                @endif
            </div>

            {{-- Module cards 2×2 --}}
            <div class="grid grid-cols-2 gap-3">

                {{-- Lịch trình --}}
                <a href="{{ route('schedule.index', $trip) }}"
                   class="bg-white rounded-xl border border-gray-100 p-4 hover:border-blue-200 hover:shadow-sm transition-all group">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0 group-hover:bg-blue-100 transition">
                            <svg class="w-[18px] h-[18px] text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">Lịch trình</p>
                            <p class="text-xs text-gray-400">{{ $activityCount }} hoạt động</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <div class="pl-12 space-y-1.5">
                        @php $pct = $activityCount > 0 ? round($activityApproved / $activityCount * 100) : 0 @endphp
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] text-gray-400">{{ $activityApproved }}/{{ $activityCount }} đã duyệt</p>
                            <p class="text-[11px] font-semibold text-emerald-600">{{ $pct }}%</p>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                </a>

                {{-- Chi tiêu --}}
                <a href="{{ route('expense.index', $trip) }}"
                   class="bg-white rounded-xl border border-gray-100 p-4 hover:border-emerald-200 hover:shadow-sm transition-all group">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0 group-hover:bg-emerald-100 transition">
                            <svg class="w-[18px] h-[18px] text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 group-hover:text-emerald-600 transition-colors">Chi tiêu</p>
                            <p class="text-xs text-gray-400">{{ $expenseCount }} khoản chi</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <div class="pl-12">
                        <p class="text-[17px] font-bold text-gray-800 leading-none">
                            {{ $totalExpense > 0 ? number_format($totalExpense, 0, ',', '.') . ' ₫' : '—' }}
                        </p>
                        <p class="text-[11px] text-gray-400 mt-1">Tổng chi tiêu nhóm</p>
                    </div>
                </a>

                {{-- Checklist --}}
                <a href="{{ route('checklist.index', $trip) }}"
                   class="bg-white rounded-xl border border-gray-100 p-4 hover:border-amber-200 hover:shadow-sm transition-all group">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center shrink-0 group-hover:bg-amber-100 transition">
                            <svg class="w-[18px] h-[18px] text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 group-hover:text-amber-600 transition-colors">Checklist</p>
                            <p class="text-xs text-gray-400">Chuẩn bị hành trang</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <div class="pl-12 space-y-1.5">
                        @php $cpct = $checklistTotal > 0 ? round($checklistDone / $checklistTotal * 100) : 0 @endphp
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] text-gray-400">{{ $checklistDone }}/{{ $checklistTotal }} việc xong</p>
                            <p class="text-[11px] font-semibold text-amber-600">{{ $cpct }}%</p>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-400 rounded-full transition-all" style="width:{{ $cpct }}%"></div>
                        </div>
                    </div>
                </a>

                {{-- Thư viện ảnh --}}
                <a href="{{ route('photo.index', $trip) }}"
                   class="bg-white rounded-xl border border-gray-100 p-4 hover:border-violet-200 hover:shadow-sm transition-all group">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center shrink-0 group-hover:bg-violet-100 transition">
                            <svg class="w-[18px] h-[18px] text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 group-hover:text-violet-600 transition-colors">Thư viện ảnh</p>
                            <p class="text-xs text-gray-400">{{ $photoCount }} ảnh</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-violet-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    @if($previewPhotos->isNotEmpty())
                    <div class="grid grid-cols-4 gap-1 pl-12">
                        @foreach($previewPhotos->take(4) as $i => $photo)
                        <div class="aspect-square rounded-md overflow-hidden bg-gray-100 relative">
                            <img src="{{ $photo->url }}" class="w-full h-full object-cover">
                            @if($i === 3 && $photoCount > 4)
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center rounded-md">
                                <span class="text-white text-[11px] font-semibold">+{{ $photoCount - 4 }}</span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-gray-400 pl-12">Chưa có ảnh nào</p>
                    @endif
                </a>

            </div>{{-- /modules 2×2 --}}
        </div>{{-- /left col --}}

        {{-- Right col (sticky) ------------------------------------- --}}
        <div class="space-y-4 sticky top-6 self-start">

            {{-- Invite code --}}
            @if($trip->isOwner(Auth::user()))
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Mã mời</h3>
                <div class="bg-blue-50 rounded-lg px-4 py-3 text-center mb-3">
                    <p class="text-xl font-bold text-blue-600 tracking-widest font-mono">{{ $trip->invite_code ?? '--------' }}</p>
                    <p class="text-[11px] text-gray-400 mt-1">Chia sẻ để mời thành viên</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="navigator.clipboard.writeText('{{ $trip->invite_code }}').then(()=>alert('Đã sao chép!'))"
                            class="flex-1 flex items-center justify-center gap-1.5 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Sao chép
                    </button>
                    <form method="POST" action="{{ route('trips.invite.regenerate', $trip) }}" class="flex-1">
                        @csrf
                        <button class="w-full flex items-center justify-center gap-1.5 py-1.5 text-xs font-medium text-gray-500 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Tạo mới
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Members --}}
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-800">Thành viên</h3>
                    <span class="text-[11px] text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $trip->members->count() }} người</span>
                </div>
                <div class="space-y-2.5">
                    @foreach($trip->members as $member)
                    <div class="flex items-center gap-2.5 group">
                        <img src="{{ $member->avatar_url }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-100 shrink-0" alt="{{ $member->name }}">
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-medium text-gray-800 truncate leading-tight">{{ $member->name }}</p>
                            <p class="text-[11px] text-gray-400 truncate">{{ $member->email }}</p>
                        </div>
                        <div class="shrink-0 flex items-center gap-1.5">
                            @if($member->pivot->role === 'owner')
                                <span class="text-[10.5px] font-medium text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-full">Trưởng nhóm</span>
                            @else
                                @if($trip->isOwner(Auth::user()))
                                <form method="POST" action="{{ route('trips.members.remove', [$trip, $member->id]) }}"
                                      onsubmit="return confirm('Xoá thành viên này?')"
                                      class="opacity-0 group-hover:opacity-100 transition">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-300 hover:text-red-500 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Status update (owner only) --}}
            @if($trip->isOwner(Auth::user()))
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Trạng thái chuyến đi</h3>
                <form method="POST" action="{{ route('trips.status', $trip) }}" class="flex gap-2">
                    @csrf @method('PATCH')
                    <select name="status"
                            class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 focus:outline-none focus:border-blue-400">
                        @foreach(['planning' => 'Lên kế hoạch', 'ongoing' => 'Đang diễn ra', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã huỷ'] as $val => $label)
                            <option value="{{ $val }}" {{ $trip->status == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                        Lưu
                    </button>
                </form>
            </div>
            @endif

        </div>{{-- /right col --}}
    </div>{{-- /main grid --}}
</div>
@endsection
