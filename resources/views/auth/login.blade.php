@extends('layouts.guest')
@section('title', 'Đăng nhập')

@section('content')
<div class="w-full max-w-md mx-auto">
    {{-- Header --}}
    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Đăng nhập</h2>
        <p class="mt-2 text-sm text-gray-500">
            Chào mừng trở lại! Vui lòng truy cập tài khoản của bạn.
        </p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        {{-- Email Field --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Địa chỉ Email</label>
            <div class="relative">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="block w-full px-4 py-3 rounded-lg border @error('email') border-red-500 @else border-gray-300 @enderror text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none placeholder-gray-400"
                    placeholder="name@company.com">
            </div>
            @error('email')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Field --}}
        <div>
            <div class="flex justify-between items-center mb-1">
                <label for="password" class="block text-sm font-semibold text-gray-700">Mật khẩu</label>
                <a href="{{ route('password.request') }}" class="text-xs font-medium text-blue-600 hover:text-blue-500 transition">Quên mật khẩu?</a>
            </div>
            <input id="password" type="password" name="password" required
                class="block w-full px-4 py-3 rounded-lg border @error('password') border-red-500 @else border-gray-300 @enderror text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none placeholder-gray-400"
                placeholder="••••••••">
            @error('password')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember me --}}
        <div class="flex items-center">
            <input type="checkbox" id="remember" name="remember"
                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
            <label for="remember" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">
                Ghi nhớ đăng nhập
            </label>
        </div>

        {{-- Submit Button --}}
        <button type="submit"
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 transform active:scale-[0.98]">
            Đăng nhập
        </button>
    </form>

    {{-- Footer --}}
    <div class="mt-8 pt-6 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-600">
            Chưa có tài khoản?
            <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-500 transition">
                Tạo tài khoản mới
            </a>
        </p>
    </div>
</div>
@endsection
