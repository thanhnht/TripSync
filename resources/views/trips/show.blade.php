@extends('layouts.app')
@section('title', $trip->name)
@section('page-title', $trip->name)
@section('page-subtitle', '📍 ' . $trip->destination)

@section('header-actions')
    @if($trip->isOwner(Auth::user()))
        <a href="{{ route('trips.edit', $trip) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            Chỉnh sửa
        </a>

        <form method="POST" action="{{ route('trips.destroy', $trip) }}"
              onsubmit="return confirm('Bạn chắc chắn muốn xoá chuyến đi này?')">
            @csrf @method('DELETE')
            <button class="flex items-center gap-2 px-4 py-2 rounded-xl border border-red-200 text-sm font-medium text-red-600 hover:bg-red-50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Xoá
            </button>
        </form>
    @else
        <form method="POST" action="{{ route('trips.leave', $trip) }}"
              onsubmit="return confirm('Bạn chắc chắn muốn rời chuyến đi này?')">
            @csrf
            <button class="flex items-center gap-2 px-4 py-2 rounded-xl border border-orange-200 text-sm font-medium text-orange-600 hover:bg-orange-50 transition">
                Rời chuyến đi
            </button>
        </form>
    @endif
@endsection

@section('content')
<div class="grid grid-cols-3 gap-6">

    {{-- Left: main info --}}
    <div class="col-span-2 space-y-6">

        {{-- Cover --}}
        <div class="rounded-xl overflow-hidden h-52 bg-gradient-to-br from-blue-500 to-blue-700 relative">
            @if($trip->cover_image)
                <img src="{{ $trip->cover_image_url }}" class="w-full h-full object-cover">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent flex items-end p-5">
                <div class="flex items-center gap-2.5">
                    @php
                        $sc = [
                            'planning'  => 'bg-blue-500/90',
                            'ongoing'   => 'bg-emerald-500/90',
                            'completed' => 'bg-slate-500/90',
                            'cancelled' => 'bg-red-500/90',
                        ];
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold text-white backdrop-blur-sm {{ $sc[$trip->status] ?? 'bg-slate-500/90' }}">
                        {{ $trip->status_label }}
                    </span>
                    <span class="text-white/80 text-xs">{{ $trip->days_count }} ngày</span>
                    <span class="text-white/40 text-xs">·</span>
                    <span class="text-white/80 text-xs">{{ $trip->members->count() }} thành viên</span>
                </div>
            </div>
        </div>

        {{-- Info card --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Điểm đến</p>
                    <p class="text-sm font-semibold text-gray-900">📍 {{ $trip->destination }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Ngày đi</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $trip->start_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Ngày về</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $trip->end_date->format('d/m/Y') }}</p>
                </div>
            </div>

            @if($trip->description)
                <div class="mt-5 pt-5 border-t border-gray-50">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Mô tả</p>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $trip->description }}</p>
                </div>
            @endif
        </div>

        {{-- Modules --}}
        <div class="grid grid-cols-2 gap-3">
            {{-- Lịch trình --}}
            <a href="{{ route('schedule.index', $trip) }}"
               class="bg-white rounded-xl border border-gray-100 p-4 flex items-center gap-3.5 hover:border-blue-200 hover:shadow-sm transition-all group col-span-2">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0 group-hover:bg-blue-100 transition">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">Lịch trình</p>
                    <p class="text-xs text-gray-500 mt-0.5">Xây dựng lịch trình cộng tác</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-400 shrink-0 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            {{-- Chi tiêu --}}
            <a href="{{ route('expense.index', $trip) }}"
               class="bg-white rounded-xl border border-gray-100 p-4 flex items-center gap-3.5 hover:border-emerald-200 hover:shadow-sm transition-all group">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0 group-hover:bg-emerald-100 transition">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 group-hover:text-emerald-600 transition-colors">Chi tiêu</p>
                    <p class="text-xs text-gray-500 mt-0.5">Quản lý và chia tiền nhóm</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-emerald-400 shrink-0 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            {{-- Thư viện ảnh --}}
            <a href="{{ route('photo.index', $trip) }}"
               class="bg-white rounded-xl border border-gray-100 p-4 flex items-center gap-3.5 hover:border-violet-200 hover:shadow-sm transition-all group">
                <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center shrink-0 group-hover:bg-violet-100 transition">
                    <svg class="w-5 h-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 group-hover:text-violet-600 transition-colors">Thư viện ảnh</p>
                    <p class="text-xs text-gray-500 mt-0.5">Lưu trữ ảnh chuyến đi</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-violet-400 shrink-0 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            {{-- Checklist --}}
            <a href="{{ route('checklist.index', $trip) }}"
               class="bg-white rounded-xl border border-gray-100 p-4 flex items-center gap-3.5 hover:border-amber-200 hover:shadow-sm transition-all group">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0 group-hover:bg-amber-100 transition">
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 group-hover:text-amber-600 transition-colors">Checklist</p>
                    <p class="text-xs text-gray-500 mt-0.5">Chuẩn bị hành trang</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-amber-400 shrink-0 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- Right: members + invite --}}
    <div class="space-y-5">

        {{-- Invite code --}}
        @if($trip->isOwner(Auth::user()))
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Mã mời</h3>
            <div class="bg-blue-50 rounded-xl p-4 text-center mb-3">
                <p class="text-2xl font-bold text-primary tracking-widest font-mono">{{ $trip->invite_code ?? '--------' }}</p>
                <p class="text-xs text-gray-500 mt-1">Chia sẻ mã này để mời thành viên</p>
            </div>
            <div class="flex gap-2">
                <button onclick="navigator.clipboard.writeText('{{ $trip->invite_code }}').then(()=>alert('Đã sao chép!'))"
                        class="flex-1 py-2 text-xs font-medium text-primary bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                    📋 Sao chép
                </button>
                <form method="POST" action="{{ route('trips.invite.regenerate', $trip) }}" class="flex-1">
                    @csrf
                    <button class="w-full py-2 text-xs font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                        🔄 Tạo mới
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Members list --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Thành viên</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ $trip->members->count() }} người</span>
            </div>

            <div class="space-y-3">
                @foreach($trip->members as $member)
                <div class="flex items-center gap-3 group">
                    <img src="{{ $member->avatar_url }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-100" alt="{{ $member->name }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $member->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $member->email }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($member->pivot->role === 'owner')
                            <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Trưởng nhóm</span>
                        @else
                            <span class="text-xs text-gray-500">Thành viên</span>
                            @if($trip->isOwner(Auth::user()))
                            <form method="POST" action="{{ route('trips.members.remove', [$trip, $member->id]) }}"
                                  onsubmit="return confirm('Xoá thành viên này?')"
                                  class="opacity-0 group-hover:opacity-100 transition">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                            @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Update status (owner only) --}}
        @if($trip->isOwner(Auth::user()))
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Cập nhật trạng thái</h3>
            <form method="POST" action="{{ route('trips.status', $trip) }}" class="flex gap-2">
                @csrf @method('PATCH')
                <select name="status"
                        class="flex-1 px-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-400">
                    @foreach(['planning' => '📋 Lên kế hoạch', 'ongoing' => '🚀 Đang diễn ra', 'completed' => '✅ Hoàn thành', 'cancelled' => '❌ Đã huỷ'] as $val => $label)
                        <option value="{{ $val }}" {{ $trip->status == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition">
                    Lưu
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
