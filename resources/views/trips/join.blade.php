@extends('layouts.app')
@section('title', 'Tham gia chuyến đi')
@section('page-title', 'Tham gia chuyến đi')
@section('page-subtitle', 'Nhập mã mời từ trưởng nhóm')

@section('content')
<div class="max-w-md">
    <div class="bg-white rounded-xl border border-gray-100 p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-50 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Nhập mã mời</h2>
            <p class="text-sm text-gray-500 mt-1">Yêu cầu trưởng nhóm chia sẻ mã 8 ký tự</p>
        </div>

        <form method="POST" action="{{ route('trips.join.post') }}" class="space-y-5">
            @csrf

            <div>
                <input type="text" name="invite_code" maxlength="8" required autofocus
                       class="w-full px-5 py-4 rounded-xl border-2 border-gray-200 text-center text-2xl font-mono font-bold tracking-widest uppercase focus:outline-none focus:border-blue-400 transition @error('invite_code') border-red-400 @enderror"
                       placeholder="XXXXXXXX"
                       value="{{ old('invite_code') }}"
                       oninput="this.value = this.value.toUpperCase()">
                @error('invite_code')
                    <p class="mt-2 text-xs text-red-600 text-center">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full py-3 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition shadow-md shadow-primary/30">
                Tham gia chuyến đi
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-5">
            Hoặc <a href="{{ route('trips.create') }}" class="text-primary font-medium hover:underline">tạo chuyến đi mới</a>
        </p>
    </div>
</div>
@endsection
