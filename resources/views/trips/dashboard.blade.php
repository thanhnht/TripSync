@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Tổng quan')
@section('page-subtitle') Xin chào, {{ Auth::user()->name }}! 👋@endsection
@section('header-actions')
    <a href="{{ route('trips.join') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
        </svg>
        Tham gia
    </a>
    <a href="{{ route('trips.create') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-medium hover:bg-primary-dark transition shadow-md shadow-primary/30">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tạo chuyến đi
    </a>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-4 gap-4 mb-8">
    @php
        $statCards = [
            ['label' => 'Tổng chuyến đi', 'value' => $stats['total'],     'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064', 'color' => 'blue'],
            ['label' => 'Đang lên kế hoạch', 'value' => $stats['planning'],  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'indigo'],
            ['label' => 'Đang diễn ra',      'value' => $stats['ongoing'],   'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'green'],
            ['label' => 'Đã hoàn thành',     'value' => $stats['completed'], 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'purple'],
        ];
    @endphp
    @foreach($statCards as $card)
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-{{ $card['color'] }}-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-{{ $card['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $card['value'] }}</p>
        <p class="text-sm text-gray-500 mt-1">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- My trips --}}
<div class="mb-10">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-lg font-semibold text-gray-900">Chuyến đi của tôi</h2>
        <a href="{{ route('trips.create') }}" class="text-sm text-primary hover:underline">+ Tạo mới</a>
    </div>

    @if($myTrips->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm">Bạn chưa có chuyến đi nào.</p>
            <a href="{{ route('trips.create') }}" class="inline-block mt-3 px-5 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition">
                Tạo chuyến đi đầu tiên
            </a>
        </div>
    @else
        <div class="grid grid-cols-3 gap-5">
            @foreach($myTrips as $trip)
                @include('trips._card', ['trip' => $trip])
            @endforeach
        </div>
    @endif
</div>

{{-- Joined trips --}}
@if($joinedTrips->isNotEmpty())
<div>
    <h2 class="text-lg font-semibold text-gray-900 mb-5">Chuyến đi đã tham gia</h2>
    <div class="grid grid-cols-3 gap-5">
        @foreach($joinedTrips as $trip)
            @include('trips._card', ['trip' => $trip])
        @endforeach
    </div>
</div>
@endif

@endsection
