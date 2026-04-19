{{-- resources/views/auth/reset-password.blade.php --}}
@extends('layouts.guest')
@section('title', 'Đặt lại mật khẩu')

@section('content')
<div class="max-w-md mx-auto">
    {{-- Header Section: Đồng bộ kích thước với trang trước --}}
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mx-auto mb-5 transition-transform hover:rotate-12 duration-300">
            <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Đặt lại mật khẩu</h2>
        <p class="text-sm text-gray-500 mt-2">Vui lòng thiết lập mật khẩu mới mạnh hơn để bảo vệ tài khoản</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email Field: Để readonly nếu email đã được truyền từ URL để tránh nhầm lẫn --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ $email ?? old('email') }}" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed outline-none"
                   readonly>
            @error('email') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Password Field --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mật khẩu mới</label>
            <input type="password" name="password" required autofocus
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all outline-none @error('password') border-red-400 @enderror"
                   placeholder="Tối thiểu 8 ký tự">
            @error('password') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Confirm Password Field --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all outline-none"
                   placeholder="Nhập lại mật khẩu mới">
        </div>

        <button type="submit" class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-lg shadow-green-100 transition-all active:scale-[0.98] mt-2">
            Cập nhật mật khẩu
        </button>
    </form>

    {{-- Link bổ sung nếu cần quay lại --}}
    <p class="text-center mt-8 text-sm text-gray-400">
        Bạn nhớ ra mật khẩu?
        <a href="{{ route('login') }}" class="font-medium text-green-600 hover:underline">Đăng nhập ngay</a>
    </p>
</div>
@endsection
