@extends('layouts.guest')
@section('title', 'Quên mật khẩu')

@section('content')
<div class="text-center mb-7">
    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
        </svg>
    </div>
    <h2 class="text-xl font-bold text-gray-900">Quên mật khẩu?</h2>
    <p class="text-sm text-gray-500 mt-1.5">Nhập email đã đăng ký, chúng tôi sẽ gửi link đặt lại mật khẩu.</p>
</div>

<form method="POST" action="{{ route('password.email') }}" class="space-y-4" novalidate>
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email đăng ký</label>
        <input type="email" name="email" value="{{ old('email') }}" autofocus
               class="input-field @error('email') !border-red-400 @enderror"
               placeholder="you@example.com">
        @error('email')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn-primary">Gửi link đặt lại mật khẩu</button>
</form>

<div class="mt-6 pt-5 border-t border-gray-100 text-center">
    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-blue-600 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Quay lại đăng nhập
    </a>
</div>
@endsection
