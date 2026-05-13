{{-- resources/views/auth/forgot-password.blade.php --}}
@extends('layouts.guest')
@section('title', 'Quên mật khẩu')

@section('content')
<div class="max-w-md mx-auto">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-blue-50 rounded-xl flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Quên mật khẩu?</h2>
        <p class="text-sm text-gray-500 mt-2 px-4">Đừng lo lắng, hãy nhập email bạn đã đăng ký. Chúng tôi sẽ gửi một liên kết để bạn thiết lập lại mật khẩu.</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <div class="relative">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email đăng ký</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none @error('email') border-red-400 @enderror"
                   placeholder="name@company.com">

            @error('email')
                <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-[0.98]">
            Gửi link đặt lại mật khẩu
        </button>
    </form>

    <div class="mt-8 pt-6 border-t border-gray-100 text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Quay lại trang đăng nhập
        </a>
    </div>
</div>
@endsection
